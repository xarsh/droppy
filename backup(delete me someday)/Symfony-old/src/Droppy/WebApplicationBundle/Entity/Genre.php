<?php

namespace Droppy\WebApplicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Droppy\WebApplicationBundle\Entity\Genre
 *
 * @ORM\Table(name="genre")
 * @ORM\Entity(repositoryClass="Droppy\WebApplicationBundle\Entity\GenreRepository")
 */
class Genre
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
	 * @ORM\Column(name="name", type="string", length=20)
	 */
	private $name;
	
	/**
	* @var string $canonicalName
	*
	* @ORM\Column(name="canonical_name", type="string", length=20)
	*/
	private $canonicalName;

	/**
	 * @var ArrayCollection<Schedule> schedules
	 *
	 * @ORM\OneToMany(targetEntity="Droppy\WebApplicationBundle\Entity\Schedule", mappedBy="genre")
	 */
	private $schedules;

	public function __construct()
	{
		$this->schedules = new \Doctrine\Common\Collections\ArrayCollection();
	}
	
	/**
	 * To string
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return (string)$this->getName();
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
	 * Add schedules
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
}
