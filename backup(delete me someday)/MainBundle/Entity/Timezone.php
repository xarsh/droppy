<?php

namespace Droppy\MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Droppy\MainBundle\Entity\Timezone
 *
 * @ORM\Table(name="timezone")
 * @ORM\Entity
 */
class Timezone
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
	 * @ORM\Column(name="name", type="string", length=40)
	 * @Assert\NotNull()
	 */
	private $name;
	
	/**
	 * @var integer $difference
	 * 
	 * @ORM\Column(name="difference", type="integer")
	 * @Assert\NotNull()
	 */
	private $difference;
	
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
	 * Set difference
	 *
	 * @param int $difference
	 */
	public function setDifference($difference)
	{
	    $this->difference = $difference;
	}
	
	/**
	 * Get difference
	 *
	 * @return difference
	 */
	public function getDifference()
	{
	    return $this->difference;
	}
	
	public function getHourDifference()
	{
	    $sign = $this->difference >= 0 ? '+' : '-';
	    $difference = abs($this->difference);
	    $hours = floor($difference / 3600);
	    $minutes = ($difference % 3600) / 60;
	    $hourStr = ($hours < 10) ? ('0' . $hours) : $hours;
	    $minuteStr = ($minutes < 10) ? ('0' . $minutes) : $minutes;
	    return ($hours == 0 && $minutes == 0) ? '' : ($sign . $hourStr . ':' . $minuteStr);  
	}
	
	/**
	 * Returns the GMT difference in hour
	 * and the name of the city
	 * 
	 * @return string
	 */
	public function __toString()
	{
	    return '(GMT' . $this->getHourDifference() . ') ' . $this->name;
	}
}