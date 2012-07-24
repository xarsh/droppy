<?php

namespace Droppy\WebApplicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Droppy\WebApplicationBundle\Entity\BirthDate
 *
 * @ORM\Table(name="birth_date")
 * @ORM\Entity
 */
class BirthDate
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
	 * @var date $date
	 *
	 * @ORM\Column(name="date", type="date")
	 * @Assert\Date(message="error.birth_date.date.invalid")
	 */
	private $date;

	/**
	 * @var PrivacySettings privacySettings
	 *
	 * @ORM\OneToOne(targetEntity="Droppy\WebApplicationBundle\Entity\PrivacySettings", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="privacy_settings_id", referencedColumnName="id", nullable=false)
	 * @Assert\Valid()
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
	 * Set date
	 *
	 * @param date $date
	 */
	public function setDate($date)
	{
		$this->date = $date;
	}

	/**
	 * Get date
	 *
	 * @return date
	 */
	public function getDate()
	{
		return $this->date;
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
