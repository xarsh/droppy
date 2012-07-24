<?php

namespace Droppy\EventBundle\Util;

use Symfony\Component\Translation\Translator;

use Doctrine\Common\Collections\Collection;

use Droppy\UserBundle\Entity\UserDropsEventRelation;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Droppy\EventBundle\Entity\Event;
use Droppy\UserBundle\Entity\User;

class EventSelector
{
	protected $em;
	protected $dm;
	protected $translator;
	
	public function __construct(EntityManager $em, DateManipulator $dm, Translator $translator)
	{
		$this->em = $em;
		$this->dm = $dm;
		$this->translator = $translator;
	}
	
	protected function getDate($dateString)
	{
		if(empty($dateString))
		{
			$date = new \DateTime(date("Y-m-d"));
		}
		else
		{
			$date = new \DateTime($dateString);
		}
		return $date;
	}
	
	public function partitionEventsThisDay(Collection $events, \DateTime $date)
	{
		$dm = $this->dm;
		return $events->partition(
					function($id, UserDropsEventRelation $rel) use ($date, $dm) {
						return $dm->compare($date, $rel->getEvent()->getStartDateTime()->getDate()) >= 0;
					});
	}
	
	public function partitionEventsThisWeek(Collection $events, \DateTime $date, $lastDayOfWeek)
	{
		$dm = $this->dm;
		return $events->partition(
				function($id, UserDropsEventRelation $rel) use ($date, $dm, $lastDayOfWeek)
				{
					return $dm->inSameWeek($date, $rel->getEvent()->getStartDateTime()->getDate(), $lastDayOfWeek);
				});
	}
	
	public function partitionEventsThisMonth(Collection $events, \DateTime $date)
	{
		$dm = $this->dm;
		return $events->partition(
				function($id, UserDropsEventRelation $rel) use ($date, $dm)
				{
					return $dm->inSameMonth($date, $rel->getEvent()->getStartDateTime()->getDate());
				});
	}
	
	public function getDroppedEventsByPeriod(User $user, $dateString = null, $offset=0, $maxResults=20)
	{
		$date = $this->getDate($dateString);
		$repository = $this->em->getRepository('DroppyEventBundle:Event'); 
		$events = new ArrayCollection($repository->getDroppedEvents($user, $date, $offset, $maxResults));
		return $this->divideByPeriod($user, $events, $date);
	}
	
	protected function translate($message)
	{
	    return $this->translator->trans($message, array(), 'DroppyMainBundle');
	}
	
	protected function divideByPeriod(User $user, $events, $date)
	{
		$lastDayOfWeek = ($user->getSettings()->getFirstDayOfWeek() + 6) % 7;
		list($eventsToday, $eventsAfterToday) = $this->partitionEventsThisDay($events, $date);
		list($eventsThisWeek, $eventsAfterThisWeek) = $this->partitionEventsThisWeek($eventsAfterToday, $date, $lastDayOfWeek);
		list($eventsThisMonth, $eventsAfterThisMonth) = $this->partitionEventsThisMonth($eventsAfterThisWeek, $date);
		return array(
		        $this->translate('time.today') => $eventsToday->toArray(), 
		        $this->translate('time.this_week') => $eventsThisWeek->toArray(),
		        $this->translate('time.this_month') => $eventsThisMonth->toArray(),
		        $this->translate('time.after_this_month') => $eventsAfterThisMonth->toArray(),
		    );
	}
	
	public function getEventsByPeriod($dateString = null, $offset=0, $maxResults=20)
	{
		$date = $this->getDate($dateString);
		$events = $this->em->getRepository('DroppyEventBundle:Event')->latestEvents($date, $offset, $maxResults);
		return $this->divideByPeriod($events, $date);
	}
}
