<?php

namespace Droppy\UserBundle\Entity;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Droppy\MainBundle\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Entity\UserManager as BaseUserManager;
use Droppy\EventBundle\Entity\Event;
use Droppy\UserBundle\Exception\UnauthroizedAccessException;

/**
 * Droppy\UserBundle\Entity\UserManager
 */

class UserManager extends BaseUserManager
{
	public function getUserById($id) 
	{
		$user = $this->em->getRepository('DroppyUserBundle:User')->find($id);
		if($user === null)
		{
			throw new HttpException(404);
		}
		return $user;
	}
	
    public function dropEvent(User $user, Event $event, $inCalendar=true, $isCreator=false, $checkRelation=true)
    {
    	if($checkRelation === true)
    	{
    		$userEventRelation = $this->em->getRepository('DroppyEventBundle:Event')->getEventRelation($user, $event);
    		if($userEventRelation !== null)
    		{
	    		throw new BadRequestException(sprintf('User %s is already dropping event %s(%d).',
    											$user->getUsername(), $event->getName(), $event->getId()));
    		}
    	}
        $userEventRelation = new UserDropsEventRelation($user, $event);
        $eventLiked = $this->em->getRepository('DroppyUserBundle:User')->isLikingEvent($user, $event);
        $userEventRelation->setEventLiked($eventLiked);
        $userEventRelation->setInCalendar($inCalendar);
        $userEventRelation->setUserIsCreator($isCreator);
        $user->addDroppedEvent($userEventRelation);
        $event->addDroppingUser($userEventRelation);
    }

    public function undropEvent(User $user, Event $event, $forceCheck=true)
    {
    	$toRemove = $this->em->getRepository('DroppyEventBundle:Event')->getEventRelation($user, $event);
        if($toRemove === null)
        {
        	if($forceCheck === false)
        	{
        		return;
        	}
        	throw new BadRequestException(sprintf('User %s is not dropping event %s(%d).', 
        									$user->getUsername(), $event->getName(), $event->getId()));	
        }
        $event->removeDroppingUser($toRemove);
        $user->removeDroppedEvent($toRemove);
    }

    public function addCreatedEvent(User $user, Event $event)
    {
        $user->addCreatedEvent($event);
        $event->setCreator($user);
        $this->dropEvent($user, $event, true, true, false);
        if($event->getPrivacySettings()->getVisibility() === 'public') {
            foreach($user->getDroppers() as $dropper) {
                $this->dropEvent($dropper->getDropping(), $event, true, false, false);
            }
        }
    }

    public function removeCreatedEvent(User $user, Event $event)
    {
        $user->removeCreatedEvent($event);
    }

    public function addLikedEvent(User $user, Event $event)
    {
        $this->setLikedInEventRelation($user, $event, true);
        $user->addLikedEvent($event);
        $event->addLikingUser($user);
    }

    public function removeLikedEvent(User $user, Event $event)
    {
        $this->setLikedInEventRelation($user, $event, false);
        $user->removeLikedEvent($event);
        $event->removeLikingUser($user);
    }

    public function setLikedInEventRelation(User $user, Event $event, $liked)
    {
    	$eventRelation = $this->em->getRepository('DroppyEventBundle:Event')->getEventRelation($user, $event);
        if(empty($eventRelation) === false && $eventRelation instanceof UserDropsEventRelation)
        {
            $eventRelation->setEventLiked($liked);
        }
    }

    public function dropUser(User $dropping, User $dropped)
    {
    	$userDropsUserRelation = $this->em->getRepository('DroppyUserBundle:User')->getRelation($dropping, $dropped);
    	if($userDropsUserRelation !== null)
    	{
    		throw new BadRequestException(sprintf('User %s is already dropping user %s',
    			$dropping->getUsername(), $dropped->getUsername()));
    	}
        $userDropsUserRelation = new UserDropsUserRelation($dropping, $dropped);
        $dropped->addDropper($userDropsUserRelation);
        $dropping->addDroppingUser($userDropsUserRelation);
        $events = $dropped->getCreatedEventsWithVisibility('public');
        foreach($events as $event)
        {
            $this->dropEvent($dropping, $event, true, false, false);
        }
        $today = new \DateTime;
        $today->setTime(0, 0, 0);
        $eventsArray = $events->filter(function(Event $event) use ($today) {
            return $event->getEndDateTime()->getDate() >= $today;
        })->slice(0, 20);
        if(count($eventsArray) > 0) {
            return $this->em->getRepository('DroppyEventBundle:Event')->eventsToEventsRelations($eventsArray, $dropping);
        } else {
            return $eventsArray;
        }
    }

    public function undropUser(User $dropping, User $dropped)
    {
    	$userDropsUserRelation = $this->em->getRepository('DroppyUserBundle:User')->getRelation($dropping, $dropped);
    	if($userDropsUserRelation === null) 
    	{
    		throw new BadRequestException(sprintf('User %s is not dropping user %s',
    			 $dropping->getUsername(), $dropped->getUsername()));
    	}
    	if(is_array($userDropsUserRelation))
    	{
    	    foreach($userDropsUserRelation as $relation) 
    	    {
    	        $dropping->removeDroppingUser($relation);
    	        $dropped->removeDropper($relation);
    	    }    
    	}
    	else
    	{
    	    $dropping->removeDroppingUser($userDropsUserRelation);
    	    $dropped->removeDropper($userDropsUserRelation);
    	}
        $eventsToUndrop = $this->em->getRepository('DroppyEventBundle:Event')->getEventsToUndrop($dropping, $dropped);
        $today = new \DateTime;
        $today->setTime(0, 0, 0);
        $toReturn = array();
        foreach($eventsToUndrop as $eventRelation)
        {
            $event = $eventRelation->getEvent();
            if($event->getEndDateTime()->getDate() >= $today)
            {
                $toReturn[] = $this->em->getRepository('DroppyEventBundle:Event')->getEventAndRelation($event, $dropping);
            }
            $dropping->removeDroppedEvent($eventRelation, false);
            $event->removeDroppingUser($eventRelation);
        }
        return $toReturn;
    }
    
   	
   	public function usersToUsersAndDropStatus(ArrayCollection $userArray, User $currentUser)
   	{
   	    $result = array();
   	    foreach($userArray as $user) 
   	    {
   	        $result[] = array('user' => $user, 'dropped' => $this->isDroppingUser($currentUser, $user));    
   	    }
   	    return $result;
   	}
   	
   	public function isDroppingUser(User $dropping, User $dropped)
   	{
   	    foreach($dropping->getDroppingUsers() as $userRelation)
   	    {
   	        if($dropped->equals($userRelation->getDropped()))
   	        {
   	            return true;
   	        }
   	    }
   	    return false;
   	}
   	
   	public function loadUserByUsername($username)
   	{
   		$user = $this->findUserByUsernameOrEmail($username);
   	
   		if (!$user || !$user->isAccountNonLocked()) {
   			throw new UsernameNotFoundException(sprintf('No user with name "%s" was found.', $username));
   		}
   	
   		return $user;
   	}
   	
   	public function lockUser(User $user)
   	{
   	    $user->setLocked(true);
   	    foreach($user->getCreatedEvents() as $event)
   	    {
   	        $event->setLocked(true);
   	    }
   	    $this->updateUser($user);
   	}

}
