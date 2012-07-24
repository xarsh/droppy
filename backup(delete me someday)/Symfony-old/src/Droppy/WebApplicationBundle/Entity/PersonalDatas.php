<?php

namespace Droppy\WebApplicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Droppy\WebApplicationBundle\Entity\PersonalDatas
 *
 * @ORM\Table(name="personal_datas")
 * @ORM\Entity
 */
class PersonalDatas
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
	 * @var string $displayedName
	 *
	 * @ORM\Column(name="displayed_name", type="string", length=30, nullable=false)
	 * @Assert\MinLength(limit=3, message="error.personal_datas.displayed_name.too_short")
	 * @Assert\MaxLength(limit=30, message="error.personal_datas.displayed_name.too_long")
	 */
	private $displayedName;

	/**
	 * @var string $introduction
	 *
	 * @ORM\Column(name="introduction", type="text", nullable=true)
	 */
	private $introduction;

	/**
	* @var string $iconSet
	*
	* @ORM\OneToOne(targetEntity="Droppy\WebApplicationBundle\Entity\IconSet", cascade={"persist", "remove"})
	* @ORM\JoinColumn(name="icon_set_id", referencedColumnName="id", nullable=false)
	* @Assert\Valid()
	*/
	private $iconSet;

	/**
	 * @var CurrentLocation $currentLocation
	 *
	 * @ORM\OneToOne(targetEntity="Droppy\WebApplicationBundle\Entity\CurrentLocation")
	 * @ORM\JoinColumn(name="current_location_id", referencedColumnName="id")
	 * @Assert\Valid()
	 */
	private $currentLocation;

	/**
	 * @var CurrentLocation $birthDate
	 *
	 * @ORM\OneToOne(targetEntity="Droppy\WebApplicationBundle\Entity\BirthDate")
	 * @ORM\JoinColumn(name="birth_date_id", referencedColumnName="id")
	 * @Assert\Valid()
	 */
	private $birthDate;

	/**
	 * @var CurrentLocation $birthPlace
	 *
	 * @ORM\OneToOne(targetEntity="Droppy\WebApplicationBundle\Entity\BirthPlace")
	 * @ORM\JoinColumn(name="birth_place_id", referencedColumnName="id")
	 * @Assert\Valid()
	 */
	private $birthPlace;
	
	public function __construct()
	{
		$this->iconSet = new \Droppy\WebApplicationBundle\Entity\IconSet(); 
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
	 * Set displayedName
	 *
	 * @param string $displayedName
	 */
	public function setDisplayedName($displayedName)
	{
		$this->displayedName = $displayedName;
	}

	/**
	 * Get displayedName
	 *
	 * @return string
	 */
	public function getDisplayedName()
	{
		return $this->displayedName;
	}

	/**
	 * Set introduction
	 *
	 * @param text $introduction
	 */
	public function setIntroduction($introduction)
	{
		$this->introduction = $introduction;
	}

	/**
	 * Get introduction
	 *
	 * @return text
	 */
	public function getIntroduction()
	{
		return $this->introduction;
	}

	/**
	 * Set avatar
	 *
	 * @param string $avatar
	 */
	public function setAvatar($avatar)
	{
		$this->avatar = $avatar;
	}

	/**
	 * Get avatar
	 *
	 * @return string
	 */
	public function getAvatar()
	{
		return $this->avatar;
	}

	/**
	 * Set currentLocation
	 *
	 * @param Droppy\WebApplicationBundle\Entity\CurrentLocation $currentLocation
	 */
	public function setCurrentLocation(\Droppy\WebApplicationBundle\Entity\CurrentLocation $currentLocation)
	{
		$this->currentLocation = $currentLocation;
	}

	/**
	 * Get currentLocation
	 *
	 * @return Droppy\WebApplicationBundle\Entity\CurrentLocation
	 */
	public function getCurrentLocation()
	{
		return $this->currentLocation;
	}

	/**
	 * Set birthDate
	 *
	 * @param Droppy\WebApplicationBundle\Entity\BirthDate $birthDate
	 */
	public function setBirthDate(\Droppy\WebApplicationBundle\Entity\BirthDate $birthDate)
	{
		$this->birthDate = $birthDate;
	}

	/**
	 * Get birthDate
	 *
	 * @return Droppy\WebApplicationBundle\Entity\BirthDate
	 */
	public function getBirthDate()
	{
		return $this->birthDate;
	}

	/**
	 * Set birthPlace
	 *
	 * @param Droppy\WebApplicationBundle\Entity\BirthPlace $birthPlace
	 */
	public function setBirthPlace(\Droppy\WebApplicationBundle\Entity\BirthPlace $birthPlace)
	{
		$this->birthPlace = $birthPlace;
	}

	/**
	 * Get birthPlace
	 *
	 * @return Droppy\WebApplicationBundle\Entity\BirthPlace
	 */
	public function getBirthPlace()
	{
		return $this->birthPlace;
	}
	
	/**
	* returns the icon set
	*
	* @return IconSet
	*/
	public function getIconSet()
	{
		return $this->iconSet;
	}
	
	/**
	 * set the icon set
	 *
	 * @param \Droppy\WebApplicationBundle\Entity\IconSet $iconSet
	 */
	public function setIconSet(\Droppy\WebApplicationBundle\Entity\IconSet $iconSet)
	{
		$this->iconSet = $iconSet;
	}
}
