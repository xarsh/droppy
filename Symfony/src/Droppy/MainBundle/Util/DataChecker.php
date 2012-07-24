<?php

namespace Droppy\MainBundle\Util;

use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

use Droppy\UserBundle\Entity\User;
use Droppy\UserBundle\Entity\UserDropsEventRelation;
use Droppy\UserBundle\Entity\UserDropsUserRelation;
use Droppy\MainBundle\Exception\ObjectNotFoundException;
use Droppy\MainBundle\Exception\BadRequestException;
use Droppy\EventBundle\Entity\Event;

class DataChecker
{
	/**
	 * @var SecurityContext $securityContext
	 */
	protected $securityContext;
	
	public function __construct(SecurityContext $securityContext)
	{
		$this->securityContext = $securityContext;
	}
	
	/**
	 * Gets User or throws Exception if not looged in
	 * 
	 * @throws AccessDeniedException
	 * @return User
	 */
	public function getUserOrDie()
	{
		if($this->userIsLogged() === false)
		{
			throw new AccessDeniedException('User is not logged.');
		}
		return $this->securityContext->getToken()->getUser();
	}
	
	/**
	 * Checks if user is logged in
	 * @return boolean
	 */	
	public function userIsLogged()
	{
		return $this->securityContext->isGranted('ROLE_USER');
	}
	
	/**
	 * Checks if $user is dropping $event
	 * 
	 * @param Event $event
	 * @throws ObjectNotFoundException
	 */
	public function getDroppedEventRelationOrDie(Event $event)
	{
		$user = $this->getUserOrDie();
		$relation = $user->getDroppedEvents()->filter(function(UserDropsEventRelation $eventRelation) use($event)
											{
												return $event->equals($eventRelation->getEvent());
											});
		if($relation->isEmpty())
		{
			throw new ObjectNotFoundException(sprintf('User "%s" is not dropping
								event "%d"("%s").', $user->getUsername(), $event->getId(), $event->getName()));
		}
		return $relation->first();
	}
	
	/**
	 * Checks if $user is liking $event
	 *
	 * @param Event $event
	 * @param boolean $liking
	 * @throws ObjectNotFoundException
	 */
	public function checkLikedEvent(Event $event, $likes)
	{
	    $user = $this->getUserOrDie();
	    $isLiking = $user->getLikedEvents()->exists(function($id,  Event $likedEvent) use($event)
	                                                  {
	                                                      return $likedEvent->equals($event);
	                                                   });
	    if($isLiking === $likes)
	    {
	        throw new ObjectNotFoundException(sprintf('User "%s" is not liking
	                event "%d"("%s").', $user->getUsername(), $event->getId(), $event->getName()));
	    }
	}
	
	/**
	 * Checks if $user is dropping ($drop=true) / not dropping ($drop=false) $other
	 * 
	 * @param User $other
	 * @param boolean $drop
	 */
	public function checkDroppedUser(User $other, $drop)
	{
		$user = $this->getUserOrDie();
		$isDropping = $user->getDroppingUsers()->exists(function($id, UserDropsUserRelation $userRelation) use($other)
											{
												return $other->equals($userRelation->getDropped());
											});
		if($isDropping === $drop)
		{
			throw new ObjectNotFoundException(sprintf('User "%s" is not dropping
											user "%d"("%s").', $user->getUsername(), $other->getId(), $other->getUsername()));
		}
	}
	
	/**
	 * Check if the logged in user is the event creator
	 * 
	 * @param Event $event
	 * @throws AccessDeniedException
	 */
	public function checkIsCreator(Event $event)
	{
		$user = $this->getUserOrDie();
		if($user->equals($event->getCreator()))
		{
			throw new AccessDeniedException(sprintf('User "%d"("%s") is not the creator of the event "%d"("%s")',
											$user->getId(), $user->getUsername(), $event->getId(), $event->getName()));
		}
	}
	
	public function checkXmlHttpRequest(Request $request)
	{
	    if($request->isXmlHttpRequest() === false)
	    {
	        throw new BadRequestException('This route can only be used by XmlHttpRequests'); 
	    }
	}
}