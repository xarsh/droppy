<?php

namespace Droppy\WebApplicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Droppy\WebApplicationBundle\Entity\Tag
 *
 * @ORM\Table(name="tag")
 * @ORM\Entity(repositoryClass="Droppy\WebApplicationBundle\Entity\TagRepository")
 */
class Tag
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
	 * @var string $name
	 *
	 * @ORM\Column(name="name", type="string", length=20, nullable=false)
	 * @Assert\NotBlank(message="error.tag.name.blank")
	 * @Assert\MaxLength(limit=20, message="error.tag.name.too_long")
	 */
	private $name;

	/**
	 * @var integer $level
	 *
	 * @ORM\Column(name="level", type="integer", nullable=false)
	 */
	private $level;

	/**
	 * @var ArrayCollection<Schedule> $schedules
	 *
	 * @ORM\ManyToMany(targetEntity="Droppy\WebApplicationBundle\Entity\Schedule", inversedBy="tags")
	 * @ORM\JoinTable(name="schedule_has_tag")
	 */
	private $schedules;

	/**
	 * @var ArrayCollection<Schedule> $events
	 *
	 * @ORM\ManyToMany(targetEntity="Droppy\WebApplicationBundle\Entity\Event", inversedBy="tags")
	 * @ORM\JoinTable(name="event_has_tag")
	 */
	private $events;
	
	public function __construct()
	{
		$this->schedules = new \Doctrine\Common\Collections\ArrayCollection();
		$this->events = new \Doctrine\Common\Collections\ArrayCollection();
		$this->level = 1;
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
	 * Set name
	 *
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set level
	 *
	 * @param integer $level
	 */
	public function setLevel($level)
	{
		$this->level = $level;
	}

	/**
	 * Get level
	 *
	 * @return integer
	 */
	public function getLevel()
	{
		return $this->level;
	}

	/**
	 * Add schedule
	 *
	 * @param Droppy\WebApplicationBundle\Entity\Schedule $schedule
	 */
	public function addSchedule(\Droppy\WebApplicationBundle\Entity\Schedule $schedule)
	{
		$this->schedules->add($schedule);
	}

	/**
	 * Get schedules
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getSchedules()
	{
		return $this->schedules;
	}

	/**
	 * Add event
	 *
	 * @param Droppy\WebApplicationBundle\Entity\Event $event
	 */
	public function addEvent(\Droppy\WebApplicationBundle\Entity\Event $event)
	{
		$this->events->add($event);
	}

	/**
	 * Get events
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getEvents()
	{
		return $this->events;
	}
}
