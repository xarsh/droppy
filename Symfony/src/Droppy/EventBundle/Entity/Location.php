<?php

namespace Droppy\EventBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Droppy\EventBundle\Entity\Location
 *
 * @ORM\Table(name="location")
 * @ORM\Entity
 */
class Location
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
	 * @var string $place
	 *
	 * @ORM\Column(name="place", type="string", length=40)
	 * @Assert\MaxLength(limit=40, message="error.location.place.too_long")
	 */
	private $place;
	
	/**
	* @var Event $events
	*
	* @ORM\OneToMany(targetEntity="Droppy\EventBundle\Entity\Event", mappedBy="location")
	*/
	private $events;
	
	public function __construct()
	{
		$events = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * Set place
	 *
	 * @param string $place
	 */
	public function setPlace($place)
	{
		$this->place = $place;
	}
	
	/**
	 * Get location
	 *
	 * @return string
	 */
	public function getPlace()
	{
		return $this->place;
	}

	/**
	 * Get event
	 * 
	 * @return ArrayCollection
	 */
	public function getEvents()
	{
		return $this->events;
	}
	
	/**
	* Add event
	*
	* @param Event $event
	*/
	public function addEvent(Event $event)
	{
		$this->events[] = $event;
	}
}