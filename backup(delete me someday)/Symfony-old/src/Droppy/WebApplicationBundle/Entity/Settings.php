<?php

namespace Droppy\WebApplicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Droppy\WebApplicationBundle\Entity\Settings
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="Droppy\WebApplicationBundle\Entity\settingsRepository")
 */
class Settings
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
	 * @var string $wallpaper
	 *
	 * @ORM\Column(name="wallpaper", type="string", length=30, nullable=false)
	 */
	private $wallpaper;

	/**
	 * @var PrivacySettings $privacySettings
	 *
	 * @ORM\OneToOne(targetEntity="Droppy\WebApplicationBundle\Entity\PrivacySettings", cascade={"persist","remove"})
	 * @ORM\JoinColumn(name="privacy_settings_id", referencedColumnName="id", nullable=false)
	 * @Assert\Valid()
	 */
	private $privacySettings;
	
	public function __construct()
	{
		$this->privacySettings = new \Droppy\WebApplicationBundle\Entity\PrivacySettings();
		$this->wallpaper = "default";
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
	 * Set wallpaper
	 *
	 * @param string $wallpaper
	 */
	public function setWallpaper($wallpaper)
	{
		$this->wallpaper = $wallpaper;
	}

	/**
	 * Get wallpaper
	 *
	 * @return string
	 */
	public function getWallpaper()
	{
		return $this->wallpaper;
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
