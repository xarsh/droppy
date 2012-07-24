<?php

namespace Droppy\UserBundle\Entity;

use Droppy\EventBundle\Entity\Event;

use Doctrine\ORM\Mapping as ORM;

/**
 * Droppy\UserBundle\Entity\EventLikedByOther
 *
 * @ORM\Table(name="event_liked_by_other")
 * @ORM\Entity
 */

class EventLikedByOther
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
	* @ORM\ManyToOne(targetEntity="Droppy\UserBundle\Entity\User", inversedBy="likedByOtherEvents")
	* @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	*/
	private $user;
	
	/**
	 *  @var User $likingUser
	 *
	 * @ORM\ManyToOne(targetEntity="Droppy\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="liking_user_id", referencedColumnName="id", nullable=false)
	 */
	private $likingUser;


	/**
	*  @var Event $event
	*
	* @ORM\ManyToOne(targetEntity="Droppy\EventBundle\Entity\Event")
	* @ORM\JoinColumn(name="event_id", referencedColumnName="id", nullable=false)
	*/
	private $event;
	
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
	 * Set liking user
	 *
	 * @param Droppy\UserBundle\Entity\User $likingUser
	 */
	public function setLikingUser(\Droppy\UserBundle\Entity\User $likingUser)
	{
	    $this->likingUser = $likingUser;
	}
	
	/**
	 * Get liking user
	 *
	 * @return Droppy\UserBundle\Entity\User $likingUser
	 */
	public function getLikingUser()
	{
	    return $this->likingUser;
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
