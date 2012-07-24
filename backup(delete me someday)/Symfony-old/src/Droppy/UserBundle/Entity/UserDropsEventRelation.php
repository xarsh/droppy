<?php

namespace Droppy\UserBundle\Entity;

use Droppy\WebApplicationBundle\Entity\Event;

use Doctrine\ORM\Mapping as ORM;

/**
 * Droppy\UserBundle\Entity\UserDropsEventRelation
 *
 * @ORM\Table(name="user_drops_event_relation")
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
	* @ORM\ManyToOne(targetEntity="Droppy\WebApplicationBundle\Entity\Event", inversedBy="droppingUsers", cascade={"persist"})
	* @ORM\JoinColumn(name="event_id", referencedColumnName="id", nullable=false)
	*/
	private $event;
	
	/**
	 * @var datetime $date
	 *
	 * @ORM\Column(name="date", type="datetime", nullable=false)
	 */
	private $date;
	
	public function __construct(User $user=null, Event $event=null)
	{
		$this->user = $user;
		$this->event = $event;
		$this->date = new \DateTime();
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
	* @param Droppy\WebApplicationBundle\Entity\Event $event
	*/
	public function setEvent(\Droppy\WebApplicationBundle\Entity\Event $event)
	{
		$this->event = $event;
	}
	
	/**
	 * Get event
	 *
	 * @return Droppy\WebApplicationBundle\Entity\Event $event
	 */
	public function getEvent()
	{
		return $this->event;
	}
}
