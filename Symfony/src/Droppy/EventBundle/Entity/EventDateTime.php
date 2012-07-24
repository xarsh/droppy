<?php

namespace Droppy\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Droppy\MainBundle\Entity\Timezone;

/**
 * Droppy\EventBundle\Entity\EventDateTime
 *
 * @ORM\Table(name="event_date_time")
 * @ORM\Entity
 */
class EventDateTime
{
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var DateTime $date
	 *
	 * @ORM\Column(name="date", type="date", nullable=false)
	 */
	private $date;

	/**
	 * @var DateTime $time
	 *
	 * @ORM\Column(name="time", type="time", nullable=true)
	 */
	private $time;

	/**
	 * @var boolean $allDay
	 *
	 * @ORM\Column(name="all_day", type="boolean", nullable=false)
	 * @Assert\NotNull()
	 */
	private $allDay;
	
	/**
	* @var Timezone $timezone
	*
	* @ORM\ManyToOne(targetEntity="Droppy\MainBundle\Entity\Timezone")
	* @ORM\JoinColumn(name="timezone_id", referencedColumnName="id", nullable=false)
	* @Assert\Valid()
	*/
	private $timezone;
	
	public function __construct()
	{
	    $this->allDay = true;
	}
	
	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set date
	 *
	 * @param DateTime $date
	 */
	public function setDate(\DateTime $date = null)
	{
		$this->date = $date;
	}

	/**
	 * Get date
	 *
	 * @return DateTime
	 */
	public function getDate()
	{
		return $this->date;
	}
	
	/**
	* Set time
	*
	* @param DateTime $time
	*/
	public function setTime(\DateTime $time = null)
	{
		$this->time = $time;
	}
	
	/**
	 * Get time
	 *
	 * @return DateTime
	 */
	public function getTime()
	{
		return $this->time;
	}
	
	/**
	* Set timezone
	*
	* @param Timezone $timezone
	*/
	public function setTimezone(Timezone $timezone)
	{
		$this->timezone = $timezone;
	}
	
	/**
	 * Get timezone
	 *
	 * @return Timezone
	 */
	public function getTimezone()
	{
		return $this->timezone;
	}
	
	/**
	 * Get allDay
	 * 
	 *  @return boolean
	 */
	public function isAllDay()
	{
		return $this->allDay;
	}
	
	/**
	 * Set allDay
	 * 
	 * @param boolean $allDay
	 */
	public function setAllDay($allDay)
	{
		$this->allDay = $allDay;
	}
	
	public function compare(EventDateTime $other)
	{
        if($this->date->format("Ymd") != $other->getDate()->format("Ymd"))
        {
            return ($this->date->format("Ymd") < $other->getDate()->format("Ymd")) ? -1 : 1;
        }
        if($this->allDay && $other->allDay)
        {
            return 0;
        }
        if($this->time->format("Hm") != $other->getTime()->format("Hm"))
        {
            return $this->time->format("Hm") < $other->getTime()->format("Hm") ? -1 : 1;
        }
        return 0;
	}
	
}
