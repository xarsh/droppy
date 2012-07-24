<?php

namespace Droppy\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Droppy\UserBundle\Entity\Language
 *
 * @ORM\Table(name="language")
 * @ORM\Entity
 */
class Language
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
     * @var string $englishName
     *
     * @ORM\Column(name="english_name", type="string", length=40)
     * @Assert\NotNull()
     */
    private $englishName;
    
    /**
     * @var string $locale
     *
     * @ORM\Column(name="locale", type="string", length=10)
     * @Assert\NotNull()
     */
    private $locale;
    
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
     * Set name
     *
     * @param string $englishName
     */
    public function setEnglishName($englishName)
    {
        $this->englishName = $englishName;
    }
    
    /**
     * Get name
     *
     * @return string
     */
    public function getEnglishName()
    {
        return $this->englishName;
    }
    
    /**
     * Set locale
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
    
    /**
     * Get locale
     *
     * @return locale
     */
    public function getLocale()
    {
        return $this->locale;
    }
    
    public function __toString()
    {
        return $this->getName() . ' - ' . $this->getEnglishName(); 
    }
    
}