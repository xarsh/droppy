<?php

namespace Droppy\EventBundle\Entity;

use Droppy\MainBundle\Entity\Color;
use Droppy\UserBundle\Entity\UserDropsEventRelation;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Droppy\MainBundle\Util\DirUtils;

/**
 * Droppy\EventBundle\Entity\Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="Droppy\EventBundle\Entity\EventRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Event
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
	 * @Assert\NotBlank(message="error.event.name.blank")
	 * @Assert\MaxLength(limit=40, message="error.event.name.too_long")
	 */
	private $name;
	
	/**
	* @var EventDateTime $startDateTime
	*
	* @ORM\OneToOne(targetEntity="Droppy\EventBundle\Entity\EventDateTime", cascade={"persist","remove"})
	* @ORM\JoinColumn(name="start_date_time_id", referencedColumnName="id", nullable=false)
	* @Assert\Valid()
	*/
	private $startDateTime;
	
	/**
	* @var EventDateTime $endDateTime
	*
	* @ORM\OneToOne(targetEntity="Droppy\EventBundle\Entity\EventDateTime", cascade={"persist","remove"})
	* @ORM\JoinColumn(name="end_date_time_id", referencedColumnName="id", nullable=true)
	*/
	private $endDateTime;
	
	/**
	 * @var string $details
	 * 
	 * @ORM\Column(name="details", type="text", nullable=true)
	 */
	private $details;
	
	/**
	 * @var string $url
	 * 
	 * @ORM\Column(name="url", type="string", length=255, nullable=true)
	 * @Assert\Url(message="error.event.url.invalid")
	 */
	private $url;
	
	/**
	 * @var DateTime $creationDate
	 * 
	 * @ORM\Column(name="creation_date", type="datetime", nullable=false)
	 */
	private $creationDate;
	
	/**
	 * @var DateTime $lastUpdate
	 * 
	 * @ORM\Column(name="last_update", type="datetime", nullable=false)
	 */
	private $lastUpdate;
	
	/**
	* @var DateTime $repetition
	*
	* @ORM\Column(name="repetition", type="date", nullable=true)
	*/
	private $repetition;
	
	/**
	* @var string $address
	*
	* @ORM\Column(name="address", type="string", length=200, nullable=true)
	* @Assert\MinLength(limit=2, message="error.event.address.too_short")
	* @Assert\MaxLength(limit=200, message="error.event.address.too_long")
	*/
	private $address;
	
	/**
	* @var Location $location
	*
	* @ORM\ManyToOne(targetEntity="Droppy\EventBundle\Entity\Location", inversedBy="events", cascade={"persist"})
	* @ORM\JoinColumn(name="location_id", referencedColumnName="id", nullable=true)
	*/
	private $location;

	/**
	 * @var User $creator
	 *
	 * @ORM\ManyToOne(targetEntity="Droppy\UserBundle\Entity\User", inversedBy="createdEvents")
	 * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=false)
	 */
	private $creator;

	/**
	* @var User $droppingUsers
	*
	* @ORM\OneToMany(targetEntity="Droppy\UserBundle\Entity\UserDropsEventRelation", mappedBy="event", cascade={"remove"})
	*/
	protected $droppingUsers;

	/**
	 * @var Tag $tags
	 *
	 * @ORM\ManyToMany(targetEntity="Droppy\EventBundle\Entity\Tag", mappedBy="events", cascade={"persist"})
	 * @Assert\Valid()
	 */
	private $tags;
	
	/**
	* @var User $likingUsers
	*
	* @ORM\ManyToMany(targetEntity="Droppy\UserBundle\Entity\User", inversedBy="likedEvents")
	* @ORM\JoinTable(name="user_likes_event")
	*/
	private $likingUsers;

	/**
	 * @var User $participatingUsers
	 *
	 * @ORM\ManyToMany(targetEntity="Droppy\UserBundle\Entity\User", inversedBy="joinedEvents")
	 * @ORM\JoinTable(name="user_participates_event")
	 */
	private $participatingUsers;

	
	/**
	 * @var PrivacySettings $privacySettings
	 *
	 * @ORM\OneToOne(targetEntity="Droppy\MainBundle\Entity\PrivacySettings", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="privacy_settings_id", referencedColumnName="id", nullable=false)
	 * @Assert\Valid()
	 */
	private $privacySettings;

	
	/**
	* @var string $iconSet
	*
	* @ORM\OneToOne(targetEntity="Droppy\MainBundle\Entity\IconSet", cascade={"persist", "remove"})
	* @ORM\JoinColumn(name="icon_set_id", referencedColumnName="id", nullable=false)
	* @Assert\Valid()
	*/
	private $iconSet;
	
	/**
	 * @var Genre $genre
	 *
	 * @ORM\ManyToOne(targetEntity="Droppy\MainBundle\Entity\Color")
	 * @ORM\JoinColumn(name="color_id", referencedColumnName="id", nullable=false)
	 * @Assert\Valid()
	 */
	private $color;
	
	
	/**
	* @var int droppingUsersNumber
	*
	* @ORM\Column(name="dropping_users_number", type="integer", nullable=false)
	*/
	private $droppingUsersNumber;
	
	/**
	* @var int likingUsersNumber
	*
	* @ORM\Column(name="liking_users_number", type="integer", nullable=false)
	*/
	private $likingUsersNumber;
	
	
	/**
	* @var int participatingUsersNumber
	*
	* @ORM\Column(name="participating_users_number", type="integer", nullable=false)
	*/
	private $participatingUsersNumber;

	public function __construct()
	{
		$this->droppingUsers = new \Doctrine\Common\Collections\ArrayCollection();
		$this->tags = new \Doctrine\Common\Collections\ArrayCollection();
		$this->participatingUsers = new \Doctrine\Common\Collections\ArrayCollection();
		$this->lastUpdate = new \DateTime();
		$this->creationDate = new \DateTime();
		$this->startDateTime = new \DateTime();
		$this->droppingUsersNumber = 0;
		$this->likingUsersNumber = 0;
		$this->participatingUsersNumber = 0;
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
	 * Set startDateTime
	 *
	 * @param eventDateTime $startDateTime
	 */
	public function setStartDateTime(EventDateTime $startDateTime)
	{
		$this->startDateTime = $startDateTime;
	}

	/**
	 * Get startDateTime
	 *
	 * @return eventDateTime
	 */
	public function getStartDateTime()
	{
		return $this->startDateTime;
	}

	/**
	 * Set endDateTime
	 *
	 * @param eventDateTime $endDateTime
	 */
	public function setEndDateTime(EventDateTime $endDateTime = null)
	{
		$this->endDateTime = $endDateTime;
	}

	/**
	 * Get endDateTime
	 *
	 * @return eventDateTime
	 */
	public function getEndDateTime()
	{
		return $this->endDateTime;
	}
	
	/**
	 * Set location
	 *
	 * @param Location $location
	 */
	public function setLocation(Location $location)
	{
		$this->location = $location;
	}

	/**
	 * Get location
	 *
	 * @return Location
	 */
	public function getLocation()
	{
		return $this->location;
	}
	
	/**
	* Set name
	*
	* @param string $name
	*/
	public function setAddress($address)
	{
		$this->address = $address;
	}
	
	/**
	 * Get address
	 *
	 * @return string
	 */
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * Set creator
	 *
	 * @param Droppy\UserBundle\Entity\User $creator
	 */
	public function setCreator(\Droppy\UserBundle\Entity\User $creator)
	{
		$this->creator = $creator;
	}

	/**
	 * Get creator
	 *
	 * @return Droppy\UserBundle\Entity\User
	 */
	public function getCreator()
	{
		return $this->creator;
	}

	/**
	 * Add droppingUsers
	 *
	 * @param Droppy\UserBundle\Entity\User $droppingUsers
	 */
	public function addDroppingUser(\Droppy\UserBundle\Entity\UserDropsEventRelation $userEventRelation)
	{
		$this->droppingUsers[] = $userEventRelation;
		$this->incrementDroppingUsersNumber();
	}

	/**
	 * Get droppingUsers
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getDroppingUsers()
	{
		return $this->droppingUsers;
	}
	
	/**
	* Set tags
	*
	* @param Droppy\EventBundle\Entity\Tag $tag
	*/
	public function setTags(\Doctrine\Common\Collections\ArrayCollection $tags)
	{
		$this->tags = $tags;
	}


	/**
	 * Add tag
	 *
	 * @param Tag $tag
	 */
	public function addTag(Tag $tag)
	{
		$this->tags->add($tag);
		$tag->addEvent($this);
	}

	/**
	 * Get tags
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getTags()
	{
		return $this->tags;
	}
	
	/**
	 * Add participatingUser
	 *
	 * @param Droppy\UserBundle\Entity\User $participatingUser
	 */
	public function addParticipatingUser(\Droppy\UserBundle\Entity\User $participatingUser)
	{
		$this->participatingUsers[] = $participatingUser;
		$this->incrementParticipatingUsersNumber();
	}

	/**
	 * Get participatingUsers
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getParticipatingUsers()
	{
		return $this->participatingUsers;
	}
	
	/**
	* Add likingUser
	*
	* @param Droppy\UserBundle\Entity\User $likingUser
	*/
	public function addLikingUser(\Droppy\UserBundle\Entity\User $likingUser)
	{
		$this->likingUsers[] = $likingUser;
		$this->incrementLikingUsersNumber();
	}
	
	/**
	 * Get likingUsers
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getLikingUsers()
	{
		return $this->likingUsers;
	}
	
	/**
	 * Remove liking user
	 *
	 * @param \Droppy\UserBundle\Entity\User $likingUser
	 */
	public function removeLikingUser(\Droppy\UserBundle\Entity\User $likingUser)
	{
		$this->likingUsers->removeElement($likingUser);
		$this->decrementLikingUsersNumber();
	}
	
	/**
	* Remove dropping user
	*
	* @param \Droppy\UserBundle\Entity\UserDropsEventRelation $droppingUser
	*/
	public function removeDroppingUser(UserDropsEventRelation $droppingUser)
	{
		$this->droppingUsers->removeElement($droppingUser);
		$this->decrementDroppingUsersNumber();
	}
	
	/**
	* Remove dropping user
	*
	* @param \Droppy\UserBundle\Entity\User $participatingUser
	*/
	public function removeParticipatingUser(\Droppy\UserBundle\Entity\User $participatingUser)
	{
		$this->participatingUsers->removeElement($participatingUser);
		$this->decrementParticipatingUsersNumber();
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

    /**
     * Set details
     *
     * @param text $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * Get details
     *
     * @return text 
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
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
     * @param \Droppy\MainBundle\Entity\IconSet $iconSet
     */
    public function setIconSet(\Droppy\MainBundle\Entity\IconSet $iconSet)
    {
    	$this->iconSet = $iconSet;
    }
    
    /**
     * Set last update
     * 
     * @param DateTime $lastUpdate
     */
    public function setLastUpdate(\DateTime $lastUpdate)
    {
    	$this->lastUpdate = $lastUpdate;
    }
    
    /**
     * Get last update
     * 
     * @return DateTime
     */
    public function getLastUpdate()
    {
    	return $this->lastUpdate;
    }
    
    /**
    * Set repetition
    *
    * @param DateTime $repetition
    */
    public function setRepetition(\DateTime $repetition)
    {
    	$this->repetition = $repetition;
    }
    
    /**
     * Get repetition
     *
     * @return DateTime
     */
    public function getRepetition()
    {
    	return $this->repetition;
    }
    
    /**
    * Set creation date
    *
    * @param DateTime $creationDate
    */
    public function setCreationDate(\DateTime $creationDate)
    {
    	$this->creationDate = $creationDate;
    }
    
    /**
     * Get creation date
     * @return DateTime
     */
    public function getCreationDate()
    {
    	return $this->creationDate;
    }
    
    /**
     * Get $color
     * 
     * @return \Droppy\MainBundle\Entity\Color
     */
    public function getColor()
    {
        return $this->color;
    }
    
    /**
     * Set $color
     * 
     * @param Color $color
     */
    public function setColor(Color $color)
    {
        $this->color = $color;
    }
    
    /**
    * Get dropping users number
    *
    * @return integer
    */
    public function getDroppingUsersNumber()
    {
    	return $this->droppingUsersNumber;
    }
    
    /**
     * Set dropping users number
     *
     * @param integer $droppingUsersNumber
     */
    public function setDroppingUsersNumber($droppingUsersNumber)
    {
    	$this->droppingUsersNumber = $droppingUsersNumber;
    }
    
    /**
     * Increments dropping users number
     */
    public function incrementDroppingUsersNumber()
    {
    	$this->droppingUsersNumber++;
    }
    
    /**
     * Decrements dropping users number
     */
    public function decrementDroppingUsersNumber()
    {
    	$this->droppingUsersNumber--;
    }
    
    /**
    * Get liking users number
    *
    * @return integer
    */
    public function getLikingUsersNumber()
    {
    	return $this->likingUsersNumber;
    }
    
    /**
     * Set liking users number
     *
     * @param integer $likingUsersNumber
     */
    public function setLikingUsersNumber($likingUsersNumber)
    {
    	$this->likingUsersNumber = $likingUsersNumber;
    }
    
    /**
     * Increments liking users number
     */
    public function incrementLikingUsersNumber()
    {
    	$this->likingUsersNumber++;
    }
    
    /**
     * Decrements liking users number
     */
    public function decrementLikingUsersNumber()
    {
    	$this->likingUsersNumber--;
    }
    
    /**
    * Get participating users number
    *
    * @return integer
    */
    public function getParticipatingUsersNumber()
    {
    	return $this->participatingUsersNumber;
    }
    
    /**
     * Set participating users number
     *
     * @param integer $participatingUsersNumber
     */
    public function setParticipatingUsersNumber($participatingUsersNumber)
    {
    	$this->participatingUsersNumber = $participatingUsersNumber;
    }
    
    /**
     * Increments participating users number
     */
    public function incrementParticipatingUsersNumber()
    {
    	$this->participatingUsersNumber++;
    }
    
    /**
     * Decrements participating users number
     */
    public function decrementParticipatingUsersNumber()
    {
    	$this->participatingUsersNumber--;
    }
    
    
    /**
     * Compare to another events with id
     * 
     * @param Event $other
     * @return boolean
     */
    public function equals(Event $other)
    {
    	return $this->getId() == $other->getId();
    }
    
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function createEventDirectory()
    {
    	$directory = $this->getDirectory();
    	if(!is_dir($directory))
    	{
    	    $old = umask(0);
    		mkdir($directory, 0777, true);
    		umask($old);
    	}
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeEventDirectory()
    {
  		DirUtils::recursiveDirRemove($this->getDirectory());
    }
    
    public function getDirectory()
    {
    	return $this->getUploadRootDir() . $this->getCreator()->getId() . '/'. $this->getId();
    }
    
    public function getUploadRootDir()
    {
    	return __DIR__ . '/../../../../web/uploads/';
    }
}
