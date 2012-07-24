<?php

namespace Droppy\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Droppy\MainBundle\Entity\PrivacySettings;

/**
 * Droppy\UserBundle\Entity\CurrentLocation
 *
 * @ORM\Table(name="current_location")
 * @ORM\Entity
 */
class CurrentLocation
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
	 * @ORM\Column(name="name", type="string", length=50, nullable=true)
	 */
	private $name;

	/**
	 * @var PrivacySettings privacySettings
	 *
	 * @ORM\OneToOne(targetEntity="Droppy\MainBundle\Entity\PrivacySettings", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="privacy_settings_id", referencedColumnName="id", nullable=false)
	 */
	private $privacySettings;
	
	public function __construct()
	{
	    $this->privacySettings = new PrivacySettings();
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
	 * Set privacySettings
	 *
	 * @param Droppy\MainBundle\Entity\PrivacySettings $privacySettings
	 */
	public function setPrivacySettings(\Droppy\MainBundle\Entity\PrivacySettings $privacySettings)
	{
		$this->privacySettings = $privacySettings;
	}

	/**
	 * Get privacySettings
	 *
	 * @return Droppy\MainBundle\Entity\PrivacySettings
	 */
	public function getPrivacySettings()
	{
		return $this->privacySettings;
	}
}
