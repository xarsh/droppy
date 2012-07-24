<?php

namespace Droppy\WebApplicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Droppy\WebApplicationBundle\Util\DirUtils;

/**
 * Droppy\WebApplicationBundle\Entity\Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="Droppy\WebApplicationBundle\Entity\EventRepository")
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
	 * @var datetime $startDateTime
	 *
	 * @ORM\Column(name="start_date_time", type="datetime", nullable=true)
	 * @Assert\DateTime(message="error.event.datetime.invalid")
	 */
	private $startDateTime;

	/**
	 * @var datetime $endDateTime
	 *
	 * @ORM\Column(name="end_date_time", type="datetime", nullable=true)
	 * @Assert\DateTime(message="error.event.datetime.invalid")
	 */
	private $endDateTime;

	/**
	 * @var string $location
	 *
	 * @ORM\Column(name="location", type="string", length=60, nullable=true)
	 * @Assert\MaxLength(limit=60, message="error.event.location.too_long")
	 */
	private $location;
	
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
	 * @ORM\Column(name="creation_date", type="date", nullable=false)
	 */
	private $creationDate;
	
	/**
	 * @var DateTime $lastUpdate
	 * 
	 * @ORM\Column(name="last_update", type="date", nullable=false)
	 */
	private $lastUpdate;

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
	 * @ORM\ManyToMany(targetEntity="Droppy\WebApplicationBundle\Entity\Tag", mappedBy="events", cascade={"persist"})
	 * @Assert\Valid()
	 */
	private $tags;

	/**
	 * @var User $participatingUsers
	 *
	 * @ORM\ManyToMany(targetEntity="Droppy\UserBundle\Entity\User", inversedBy="joinedEvents")
	 * @ORM\JoinTable(name="users_participate_events")
	 */
	private $participatingUsers;

	/**
	 * @var PrivacySettings $privacySettings
	 *
	 * @ORM\OneToOne(targetEntity="Droppy\WebApplicationBundle\Entity\PrivacySettings", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="privacy_settings_id", referencedColumnName="id", nullable=false)
	 * @Assert\Valid()
	 */
	private $privacySettings;

	/**
	 * @var ArrayCollection<Schedule> $schedules
	 *
	 * @ORM\ManyToOne(targetEntity="Droppy\WebApplicationBundle\Entity\Schedule", inversedBy="events")
	 * @ORM\JoinColumn(name="schedule_id", referencedColumnName="id", nullable=false)
	 */
	private $schedule;
	
	/**
	* @var string $iconSet
	*
	* @ORM\OneToOne(targetEntity="Droppy\WebApplicationBundle\Entity\IconSet", cascade={"persist", "remove"})
	* @ORM\JoinColumn(name="icon_set_id", referencedColumnName="id", nullable=false)
	* @Assert\Valid()
	*/
	private $iconSet;

	public function __construct()
	{
		$this->droppingUsers = new \Doctrine\Common\Collections\ArrayCollection();
		$this->tags = new \Doctrine\Common\Collections\ArrayCollection();
		$this->participatingUsers = new \Doctrine\Common\Collections\ArrayCollection();
		$this->lastUpdate = new \DateTime();
		$this->creationDate = new \DateTime();
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
	 * @param datetime $startDateTime
	 */
	public function setStartDateTime($startDateTime)
	{
		$this->startDateTime = new \DateTime($startDateTime);
	}

	/**
	 * Get startDateTime
	 *
	 * @return datetime
	 */
	public function getStartDateTime()
	{
		return $this->startDateTime;
	}

	/**
	 * Set endDateTime
	 *
	 * @param datetime $endDateTime
	 */
	public function setEndDateTime($endDateTime)
	{
		$this->endDateTime = new \DateTime($endDateTime);
	}

	/**
	 * Get endDateTime
	 *
	 * @return datetime
	 */
	public function getEndDateTime()
	{
		return $this->endDateTime;
	}

	/**
	 * Set location
	 *
	 * @param string $location
	 */
	public function setLocation($location)
	{
		$this->location = $location;
	}

	/**
	 * Get location
	 *
	 * @return string
	 */
	public function getLocation()
	{
		return $this->location;
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
	* @param Droppy\WebApplicationBundle\Entity\Tag $tag
	*/
	public function setTags(\Doctrine\Common\Collections\ArrayCollection $tags)
	{
		$this->tags = $tags;
	}

	/**
	 * Add tag
	 *
	 * @param Droppy\WebApplicationBundle\Entity\Tag $tag
	 */
	public function addTag(\Droppy\WebApplicationBundle\Entity\Tag $tag)
	{
		$this->tags->add($tag);
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
		$this->participatingUsers->add($participatingUser);
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
	 * @return Droppy\WebApplicationBundle\Entity\Schedule
	 */
	public function getSchedule()
	{
		return $this->schedule;
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
     * @param \Droppy\WebApplicationBundle\Entity\IconSet $iconSet
     */
    public function setIconSet(\Droppy\WebApplicationBundle\Entity\IconSet $iconSet)
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
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function createEventDirectory()
    {
    	$directory = $this->getDirectory();
    	if(!is_dir($directory))
    	{
    		mkdir($directory, 0777, true);
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
    	return $this->getUploadRootDir() . $this->getCreator()->getId() . '/'
    	. $this->getSchedule()->getId()	. '/' . $this->getId();
    }
    
    public function getUploadRootDir()
    {
    	return __DIR__ . '/../../../../web/uploads/';
    }
}
