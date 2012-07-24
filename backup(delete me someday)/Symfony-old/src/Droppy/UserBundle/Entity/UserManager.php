<?php

namespace Droppy\UserBundle\Entity;

use FOS\UserBundle\Entity\UserManager as BaseUserManager;
use Droppy\WebApplicationBundle\Entity\Schedule;
use Droppy\WebApplicationBundle\Entity\Event;

/**
 * Droppy\UserBundle\Entity\UserManager
 *
 */

class UserManager extends BaseUserManager
{
	public function dropSchedule(User $user, Schedule $schedule)
	{
		$userScheduleRelation = new UserDropsScheduleRelation($user, $schedule);
		$user->addDroppedSchedule($userScheduleRelation);
		$schedule->addDroppingUser($userScheduleRelation);
	} 

	public function undropSchedule(User $user, Schedule $schedule)
	{
	    $toRemove = $user->getDroppedSchedules()->filter(
	        function($sch) use ($schedule) {
    	        return $sch->getSchedule() == $schedule;
	        })->first();
	    $schedule->removeDroppingUser($toRemove);
		$user->removeDroppedSchedule($toRemove);
	}
	
	public function addCreatedSchedule(User $user, Schedule $schedule)
	{
		$this->dropSchedule($user, $schedule);
		$user->addCreatedSchedule($schedule);
		$schedule->setCreator($user);
	}

	public function dropEvent(User $user, Event $event)
	{
		$userEventRelation = new UserDropsEventRelation($user, $event);
		$user->addDroppedEvent($userEventRelation);
		$event->addDroppingUser($userEventRelation);
	}

	public function undropEvent(User $user, Event $event)
	{
		$toRemove = $user->getDroppedEvent()->filter(
			  function($eve) use ($event) {
			   	return $eve->getEvent() == $event;
		          })->first();
		$event->removeDroppingUser($toRemove);
		$user->removeDroppingEvent($toRemove);
	}

	public function addCreatedEvent(User $user, Event $event)
	{
		$user->addCreatedEvent($event);
		$event->setCreator($user);
	}
}
