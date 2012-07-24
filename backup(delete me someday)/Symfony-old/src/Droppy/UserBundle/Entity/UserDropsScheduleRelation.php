<?php

namespace Droppy\UserBundle\Entity;

use Droppy\WebApplicationBundle\Entity\Schedule;

use Doctrine\ORM\Mapping as ORM;

/**
 * Droppy\UserBundle\Entity\UserDropsScheduleRelation
 *
 * @ORM\Table(name="user_drops_schedule_relation")
 * @ORM\Entity
 */

class UserDropsScheduleRelation
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
	* @ORM\ManyToOne(targetEntity="Droppy\UserBundle\Entity\User", inversedBy="droppedSchedules")
	* @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	*/
	private $user;


	/**
	*  @var Schedule $schedule
	*
	* @ORM\ManyToOne(targetEntity="Droppy\WebApplicationBundle\Entity\Schedule", inversedBy="droppingUsers")
	* @ORM\JoinColumn(name="schedule_id", referencedColumnName="id", nullable=false)
	*/
	private $schedule;
	
	/**
	 * @var datetime $date
	 *
	 * @ORM\Column(name="date", type="datetime", nullable=false)
	 */
	private $date;
	
	public function __construct(User $user=null, Schedule $schedule=null)
	{
		$this->user = $user;
		$this->schedule = $schedule;
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
	* Set schedule
	*
	* @param Droppy\WebApplicationBundle\Entity\Schedule $schedule
	*/
	public function setSchedule(\Droppy\WebApplicationBundle\Entity\Schedule $schedule)
	{
		$this->schedule = $schedule;
	}
	
	/**
	 * Get schedule
	 *
	 * @return Droppy\WebApplicationBundle\Entity\Schedule $schedule
	 */
	public function getSchedule()
	{
		return $this->schedule;
	}
}
