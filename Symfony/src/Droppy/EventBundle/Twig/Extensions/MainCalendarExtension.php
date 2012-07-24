<?php

namespace Droppy\EventBundle\Twig\Extensions;

class MainCalendarExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
			'getDayNumber' => new \Twig_Function_Method($this, 'getDayNumber'),
			'getDayNumberColor' => new \Twig_Function_Method($this, 'getDayNumberColor'),
			'getCellColor' => new \Twig_Function_Method($this, 'getCellColor'),
        );
	}

	public function getFilters()
	{
		return array(
			'nextMonth' => new \Twig_Filter_Method($this, 'nextMonth'),
			'previousMonth' => new \Twig_Filter_Method($this, 'previousMonth'),
			'numberOfWeeks' => new \Twig_Filter_Method($this, 'numberOfWeeks')
		);
	}

    public function getDayNumber($yearmonth, $number, $calendarStartsOnSunday = true)
	{
		if ($number < 0 && $number > 43)
            throw new \InvalidArgumentException("getDayNumber() is unable to handle strange dates.");
		
		$firstCell = $this->getFirstCell($yearmonth, $calendarStartsOnSunday);

		$dayNumber = $firstCell->modify("+" . $number . "day")->format('j');

        return $dayNumber;
    }

    public function getDayNumberColor($yearmonth, $number, $calendarStartsOnSunday = true)
	{
		if ($number < 0 && $number > 43)
            throw new \InvalidArgumentException("getDayNumber() is unable to handle strange dates.");
		
		$firstCell = $this->getFirstCell($yearmonth, $calendarStartsOnSunday);

		if ($firstCell->modify("+" . $number . "day")->format('Ym') == $yearmonth):
			return "days";
		else:
			return "days txt_gray " . $firstCell->format('Ym');
		endif;
    }

    public function getCellColor($yearmonth, $number, $calendarStartsOnSunday = true)
	{
		if ($number < 0 && $number > 43)
            throw new \InvalidArgumentException("getDayNumber() is unable to handle strange dates.");

		$firstCell = $this->getFirstCell($yearmonth, $calendarStartsOnSunday);
		$today = new \DateTime('today');
		
		if ($firstCell->modify("+" . $number . "day") == $today):
			return "today";
		elseif ($firstCell->format('D') == 'Sun'):
			return "sun";
		elseif ($firstCell->format('D') == 'Sat'):
			return "sat";
		else:
			return "day";
		endif;
	}

	public function getFirstCell($yearmonth, $calendarStartsOnSunday = true)
	{
		$firstCell = \DateTime::createFromFormat('Ymd', $yearmonth . "01");

		if($calendarStartsOnSunday):
			$firstCell->modify("Sunday last week");
		else:
			$firstCell->modify("Monday this week");
		endif;
		
		return $firstCell;
	}

	public function nextMonth($thisMonth)
	{
		$nextMonth = clone $thisMonth;
		return $nextMonth->modify("first day of next month");
	}

	public function previousMonth($thisMonth)
	{
		$prevMonth = clone $thisMonth;
		return $prevMonth->modify("first day of last month");
	}

	public function numberOfWeeks($dateTime)
	{
		$numberOfCells = $dateTime->format('t');
		$numberOfCells += $dateTime->modify("first day of this month")->format('w');

		if ($numberOfCells <= 27):
			return 27;
		elseif (28 < $numberOfCells && $numberOfCells <= 35):
		    return 34;
		else:
			return 41;
		endif;
	}	

    public function getName()
    {
        return 'main_calendar_extension';
    }
}
