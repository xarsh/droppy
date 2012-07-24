<?php

namespace Droppy\EventBundle\Util;

class DateManipulator
{
	public function compare(\DateTime $date, \DateTime $other)
	{
		if($date->format("Ymd") != $other->format("Ymd"))
		{
			return ($date->format("Ymd") < $other->format("Ymd")) ? -1 : 1;
		}
		return 0;
	}
	
	public function equals(\DateTime $date, \DateTime $other)
	{
		return $this->compare($date, $other) == 0;
	}
	
	public function dateInInterval(\DateTime $date, \DateTime $start, \DateTime $end)
	{
		if($this->compare($date, $start) == -1)
		{
			return false;
		}
		if($this->compare($date, $end) == 1)
		{
			return false;
		}
		return true;
	}
	
	protected function getDaysOfWeek()
	{
		return array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday',
						'friday', 'saturday');
	} 
	
	public function inSameWeek(\DateTime $date, \DateTime $other, $lastDayOfWeek)
	{
		$daysOfWeek = $this->getDaysOfWeek();
		$start = clone $other;
		$firstDayOfWeek = ($lastDayOfWeek + 1) % 7;
	    $start->modify($daysOfWeek[($lastDayOfWeek + 1) % 7] . ' last week');
		$end = clone $other;
		$end->modify($daysOfWeek[$lastDayOfWeek] . ' this week');
		return $this->dateInInterval($date, $start, $end);
	}
	
	public function inSameMonth(\DateTime $date, \DateTime $other)
	{
		$start = clone $other;
		$start->modify('first day of this month');
		$end = clone $other;
		$end->modify('last day of this month');
		return $this->dateInInterval($date, $start, $end);
	}
}
