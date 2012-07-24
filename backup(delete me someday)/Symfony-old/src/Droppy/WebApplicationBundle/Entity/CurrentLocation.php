<?php

namespace Droppy\WebApplicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Droppy\WebApplicationBundle\Entity\CurrentLocation
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
	 * @ORM\Column(name="name", type="string", length=50)
	 */
	private $name;

	/**
	 * @var PrivacySettings privacySettings
	 *
	 * @ORM\OneToOne(targetEntity="Droppy\WebApplicationBundle\Entity\PrivacySettings", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="privacy_settings_id", referencedColumnName="id", nullable=false)
	 */
	private $privacySettings;

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
	 * @param Droppy\WebApplicationBundle\Entity\PrivacySettings $privacySettings
	 */
	public function setPrivacySettings(\Droppy\WebApplicationBundle\Entity\PrivacySettings $privacySettings)
	{
		$this->privacySettings = $privacySettings;
	}

	/**
	 * Get privacySettings
	 *
	 * @return Droppy\WebApplicationBundle\Entity\PrivacySettings
	 */
	public function getPrivacySettings()
	{
		return $this->privacySettings;
	}
}
