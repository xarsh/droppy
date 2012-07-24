<?php

namespace Droppy\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Droppy\MainBundle\Entity\Timezone;
use Droppy\MainBundle\Entity\Color;

/**
 * Droppy\UserBundle\Entity\Settings
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity
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
     * @ORM\Column(name="wallpaper", type="string", length=30, nullable=true)
     */
    private $wallpaper;
    
    /**
     * @var PrivacySettings $privacySettings
     *
     * @ORM\OneToOne(targetEntity="Droppy\MainBundle\Entity\PrivacySettings", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="privacy_settings_id", referencedColumnName="id", nullable=false)
     * @Assert\Valid()
     */
    private $privacySettings;
    
    /**
     * @var Droppy\UserBundle\Entity\Language $language
     *
     * @ORM\ManyToOne(targetEntity="Droppy\UserBundle\Entity\Language")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id", nullable=false)
     */
    private $language;
    
    /**
     * @var Timezone $timezone
     *
     * @ORM\ManyToOne(targetEntity="Droppy\MainBundle\Entity\Timezone")
     * @ORM\JoinColumn(name="timezone_id", referencedColumnName="id", nullable=false)
     * @Assert\Valid()
     */
    private $timezone;
    
    /**
     * @var Color $color
     *
     * @ORM\ManyToOne(targetEntity="Droppy\MainBundle\Entity\Color")
     * @ORM\JoinColumn(name="color_id", referencedColumnName="id", nullable=false)
     * @Assert\Valid()
     */
    private $color;
    
    /**
     * @var int $firstDayOfWeek
     * 
     * @ORM\Column(name="first_day_of_week", type="integer", nullable=false)
     */
    private $firstDayOfWeek;
    
    public function __construct()
    {
        $this->firstDayOfWeek = 0;
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
     * Set privacy settings
     *
     * @param PrivacySettings $settings
     */
    public function setPrivacySettings(\Droppy\MainBundle\Entity\PrivacySettings $privacySettings)
    {
        $this->privacySettings = $privacySettings;
    }
    
    /**
     * Get privacy settings
     *
     * @return \Droppy\MainBundle\Entity\PrivacySettings $privacySettings
     */
    public function getPrivacySettings()
    {
        return $this->privacySettings;
    }
    
    /**
     * Set language
     *
     * @param Language $language
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;
    }
    
    /**
     * Get language
     *
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }
    
    /**
     * Set timezone
     *
     * @param Timezone $timezone
     */
    public function setTimezone(Timezone $timezone)
    {
        $this->timezone = $timezone;
    }
    
    /**
     * Get timezone
     *
     * @return Timezone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }
    
    /**
     * Set Color
     *
     * @param Color $color
     */
    public function setColor(Color $color)
    {
        $this->color = $color;
    }
    
    /**
     * Get color
     *
     * @return Color
     */
    public function getColor()
    {
        return $this->color;
    }
    
    /**
     * Set first day of week
     *
     * @param int $firstDayOfWeek
     */
    public function setFirstDayOfWeek($firstDayOfWeek)
    {
        $this->firstDayOfWeek = $firstDayOfWeek;
    }
    
    /**
     * Get first day of week
     *
     * @return int
     */
    public function getFirstDayOfWeek()
    {
        return $this->firstDayOfWeek;
    }
    
    
    
}