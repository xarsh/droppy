<?php

namespace Droppy\UserBundle\Entity;

use Droppy\EventBundle\Entity\Event;

use Doctrine\ORM\Mapping as ORM;

/**
 * Droppy\UserBundle\Entity\UserDropsEventRelation
 *
 * @ORM\Table(name="user_drops_event")
 * @ORM\Entity
 */

class UserDropsEventRelation
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
	*  @var User $user
	*
	* @ORM\ManyToOne(targetEntity="Droppy\UserBundle\Entity\User", inversedBy="droppedEvents")
	* @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	*/
	private $user;


	/**
	*  @var Event $event
	*
	* @ORM\ManyToOne(targetEntity="Droppy\EventBundle\Entity\Event", inversedBy="droppingUsers", cascade={"persist"})
	* @ORM\JoinColumn(name="event_id", referencedColumnName="id", nullable=false)
	*/
	private $event;
	
	/**
	 * @var datetime $date
	 *
	 * @ORM\Column(name="date", type="datetime", nullable=false)
	 */
	private $date;
	
	/**
	 * @var boolean $inCalendar
	 * 
	 * @ORM\Column(name="event_in", type="boolean", nullable=false)
	 */
	private $inCalendar;
	
	/**
	 * @var boolean $eventLiked
	 * 
	 * @ORM\Column(name="event_liked", type="boolean", nullable=false)
	 */
	private $eventLiked;
	
	
	/**
	 * @var boolean $userIsCreator
	 * 
	 * @ORM\Column(name="user_is_creator", type="boolean", nullable=false)  
	 */
	private $userIsCreator;
	
	/**
	 * @var boolewn $new
	 * 
	 * @ORM\Column(name="new", type="boolean", nullable=false)
	 */
	private $new;
	
	
	
	public function __construct(User $user=null, Event $event=null)
	{
		$this->user = $user;
		$this->event = $event;
		$this->date = new \DateTime();
		$this->inCalendar = false;
		$this->eventLiked = false;
		$this->userIsCreator = false;
		$this->new = true;
	}
	
	/**
	 * Get id
	 * 
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	* Set user
	*
	* @param Droppy\UserBundle\Entity\User $user
	*/
	public function setUser(\Droppy\UserBundle\Entity\User $user)
	{
		$this->user = $user;
	}
	
	/**
	 * Get user
	 *
	 * @return Droppy\UserBundle\Entity\User $user
	 */
	public function getUser()
	{
		return $this->user;
	}
	
	/**
	* Set event
	*
	* @param Droppy\EventBundle\Entity\Event $event
	*/
	public function setEvent(\Droppy\EventBundle\Entity\Event $event)
	{
		$this->event = $event;
	}
	
	/**
	 * Get event
	 *
	 * @return Droppy\EventBundle\Entity\Event $event
	 */
	public function getEvent()
	{
		return $this->event;
	}
	
	/**
	 * Set in calendar
	 * 
	 * @param boolean $inCalendar
	 */
	public function setInCalendar($inCalendar)
	{
		$this->inCalendar = $inCalendar;
	}
	
	/**
	 * Is in calendar
	 * 
	 * @return boolean
	 */
	public function isInCalendar()
	{
		return $this->inCalendar;
	}
	
	/**
	* Set event liked
	*
	* @param boolean $eventLiked
	*/
	public function setEventLiked($eventLiked)
	{
		$this->eventLiked = $eventLiked;
	}
	
	/**
	 * Is event liked
	 *
	 * @return boolean
	 */
	public function isEventLiked()
	{
		return $this->eventLiked;
	}
	
	/**
	 * Set user is creator
	 *
	 * @param boolean $userIsCreator
	 */
	public function setUserIsCreator($userIsCreator)
	{
	    $this->userIsCreator = $userIsCreator;
	}
	
	/**
	 * Is user is creator
	 *
	 * @return boolean
	 */
	public function isUserIsCreator()
	{
	    return $this->userIsCreator;
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
	 * Set new
	 * 
	 * @param boolean $new
	 */
	public function setNew($new)
	{
	    $this->new = $new;
	}
	
	/**
	 * Get new
	 * 
	 * @return boolean
	 */
	public function getNew()
	{
	    return $this->new;
	}
}
