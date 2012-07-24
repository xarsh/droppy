<?php

namespace Droppy\UserBundle\Entity;
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
    public function dropEvent(User $user, Event $event, $inCalendar=false, $isCreator=false)
    {
        $userEventRelation = new UserDropsEventRelation($user, $event);
        $eventLiked = $this->em->getRepository('DroppyUserBundle:User')->isLikingEvent($user, $event);
        $userEventRelation->setEventLiked($eventLiked);
        $userEventRelation->setInCalendar($inCalendar);
        $userEventRelation->setUserIsCreator($isCreator);
        $user->addDroppedEvent($userEventRelation);
        $event->addDroppingUser($userEventRelation);
    }

    public function undropEvent(User $user, Event $event)
    {
        $toRemove = $this->em->getRepository('DroppyEventBundle:Event')->getEventRelation($user, $event);
        $event->removeDroppingUser($toRemove);
        $user->removeDroppingEvent($toRemove);
    }

    public function addCreatedEvent(User $user, Event $event)
    {
        $user->addCreatedEvent($event);
        $event->setCreator($user);
        $this->dropEvent($user, $event, true, true);
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
        $userDropsUserRelation = new UserDropsUserRelation($dropping, $dropped);
        $dropped->addDropper($userDropsUserRelation);
        $dropping->addDroppingUser($userDropsUserRelation);
        foreach($dropped->getCreatedEventsWithVisibility('public') as $event)
        {
            $this->dropEvent($dropping, $event);
        }
    }

    public function undropUser(User $dropping, User $dropped)
    {
    	$userDropsUserRelation = $this->em->getRepository('DroppyUserBundle:User')->getRelation($dropping, $dropped);
    	
        $dropping->removeDroppingUser($userDropsUserRelation);
        $dropped->removeDropper($userDropsUserRelation);

        $eventsToUndrop = $this->em->getRepository('DroppyEventBundle:Event')->getEventsToUndrop($dropping, $dropped);
        foreach($eventsToUndrop as $eventRelation)
        {
            $dropping->removeDroppedEvent($eventRelation);
            $eventRelation->getEvent()->removeDroppingUser($eventRelation);
        }
    }
    
   	
   	public function usersToUsersAndDropStatus(ArrayCollection $userArray, User $currentUser)
   	{
   		$self = $this;
   		return $userArray->map(function(User $user) use ($currentUser, $self) 
   		{
   			return array('user' => $user, 'dropped' => $user->getDroppersAsUsers()->contains($currentUser));
   		});
   	}

}
