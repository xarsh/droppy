<?php

namespace Droppy\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Droppy\UserBundle\Entity\UserDropsUser
 *
 * @ORM\Table(name="user_drops_user")
 * @ORM\Entity
 */

class UserDropsUserRelation
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
	*  @var User $droppingUser
	*
	* @ORM\ManyToOne(targetEntity="Droppy\UserBundle\Entity\User", inversedBy="droppingUsers")
	* @ORM\JoinColumn(name="dropping_id", referencedColumnName="id", nullable=false)
	*/
	private $dropping;

	
	/**
	*  @var User $droppedUser
	*
	* @ORM\ManyToOne(targetEntity="Droppy\UserBundle\Entity\User", inversedBy="droppers")
	* @ORM\JoinColumn(name="dropped_id", referencedColumnName="id", nullable=false)
	*/
	private $dropped;
	
	/**
	 * @var datetime $date
	 *
	 * @ORM\Column(name="date", type="datetime", nullable=false)
	 */
	private $date;
	
	public function __construct(User $dropping=null, User $dropped=null)
	{
		$this->dropped = $dropped;
		$this->dropping = $dropping;
		$this->date = new \DateTime();
	}
	
	
	/**
	* Set user
	*
	* @param User $droppingUser
	*/
	public function setDropping(User $dropping)
	{
		$this->dropping = $dropping;
	}
	
	/**
	 * Get dropping user
	 *
	 * @return User $droppingUser
	 */
	public function getDropping()
	{
		return $this->dropping;
	}
	
	/**
	* Set dropped user
	*
	* @param User $dropped
	*/
	public function setDropped(User $dropped)
	{
		$this->dropped = $dropped;
	}
	
	/**
	 * Get dropped user
	 *
	 * @return User $dropped
	 */
	public function getDropped()
	{
		return $this->dropped;
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
}
