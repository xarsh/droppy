<?php

namespace Droppy\WebApplicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Droppy\WebApplicationBundle\Util\DirUtils;

/**
 * Droppy\WebApplicationBundle\Entity\Schedule
 *
 * @ORM\Table(name="schedule")
 * @ORM\Entity(repositoryClass="Droppy\WebApplicationBundle\Entity\ScheduleRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Schedule
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
	 * @ORM\Column(name="name", type="string", length=40, nullable=false)
	 * @Assert\NotBlank(message="error.schedule.name.blank")
	 * @Assert\MaxLength(limit=40, message="error.schedule.name.too_long")
	 */
	private $name;

	/**
	 * @var string $description
	 *
	 * @ORM\Column(name="description", type="text", nullable=true)
	 */
	private $description;

	/**
	 * @var string $wallpaper
	 *
	 * @ORM\Column(name="wallpaper", type="string", length=30, nullable=false)
	 */
	private $wallpaper;
	
	/**
	 * @var boolean $creatorVisible
	 * 
	 * @ORM\Column(name="creator_visible", type="boolean", nullable=false)
	 * @Assert\NotBlank(message="error.schedule.creatorVisible.blank")
	 */
	private $creatorVisible;
	
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
	* @var User $droppingUsers
	*
	* @ORM\OneToMany(targetEntity="Droppy\UserBundle\Entity\UserDropsScheduleRelation", mappedBy="schedule")
	*/
	protected $droppingUsers;

	/**
	 * @var User $bookmarkingUsers
	 *
	 * @ORM\ManyToMany(targetEntity="Droppy\UserBundle\Entity\User", inversedBy="bookmarkedSchedules")
	 * @ORM\JoinTable(name="user_bookmarks_schedule")
	 */
	private $bookmarkingUsers;

	/**
	 * @var User $creator
	 *
	 * @ORM\ManyToOne(targetEntity="Droppy\UserBundle\Entity\User", inversedBy="createdSchedules")
	 * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=false)
	 */
	private $creator;

	/**
	 * @var Tag $tags
	 *
	 * @ORM\ManyToMany(targetEntity="Droppy\WebApplicationBundle\Entity\Tag", mappedBy="schedules", cascade={"persist"})
	 * @Assert\Valid()
	 */
	private $tags;

	/**
	 * @var Genre $genre
	 *
	 * @ORM\ManyToOne(targetEntity="Droppy\WebApplicationBundle\Entity\Genre", inversedBy="schedules")
	 * @ORM\JoinColumn(name="genre_id", referencedColumnName="id", nullable=false)
	 * @Assert\Valid()
	 */
	private $genre;

	/**
	 * @var ArrayCollection<Event> $events
	 *
	 * @ORM\OneToMany(targetEntity="Droppy\WebApplicationBundle\Entity\Event", mappedBy="schedule", cascade={"persist", "remove"})
	 */
	private $events;

	/**
	* @var string $iconSet
	*
	* @ORM\OneToOne(targetEntity="Droppy\WebApplicationBundle\Entity\IconSet", cascade={"persist", "remove"})
 	* @ORM\JoinColumn(name="icon_set_id", referencedColumnName="id", nullable=false)
 	* @Assert\Valid()
	*/
	private $iconSet;
	
	/**
	 * @var PrivacySettings privacySettings
	 *
	 * @ORM\OneToOne(targetEntity="Droppy\WebApplicationBundle\Entity\PrivacySettings", cascade={"persist", "remove"})
	 * @ORM\JoinColumn(name="privacy_settings_id", referencedColumnName="id", nullable=false)
	 * @Assert\Valid()
	 */
	private $privacySettings;

	public function __construct()
	{
		$this->droppingUsers = new \Doctrine\Common\Collections\ArrayCollection();
		$this->bookmarkingUsers = new \Doctrine\Common\Collections\ArrayCollection();
		$this->tags = new \Doctrine\Common\Collections\ArrayCollection();
		$this->events = new \Doctrine\Common\Collections\ArrayCollection();
		$this->privacySettings = new \Droppy\WebApplicationBundle\Entity\PrivacySettings();
		$this->privacySettings->setVisibility('public');
		$this->creatorVisible = true;
		$this->lastUpdate = new \DateTime();
		$this->creationDate = new \DateTime();
		$this->iconSet = new \Droppy\WebApplicationBundle\Entity\IconSet();
		$this->iconSet->setUploaded(false);
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
	 * Set description
	 *
	 * @param text $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * Get description
	 *
	 * @return text
	 */
	public function getDescription()
	{
		return $this->description;
	}
	
	/**
	 * Set creatorVisible
	 * 
	 * @param boolean $creatorVisible
	 */
	public function setCreatorVisible($creatorVisible)
	{
		$this->creatorVisible = $creatorVisible;
	}
	
	/**
	 * Is creatorVisible
	 * 
	 * @return boolean creatorVisible
	 */
	public function isCreatorVisible()
	{
		return $this->creatorVisible;
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
	 * Add droppingUsers
	 *
	 * @param Droppy\UserBundle\Entity\User $droppingUser
	 */
	public function addDroppingUser(\Droppy\UserBundle\Entity\UserDropsScheduleRelation $userScheduleRelation)
	{
		$this->droppingUsers[] = $userScheduleRelation;
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
	 * Add bookmarkingUsers
	 *
	 * @param Droppy\UserBundle\Entity\User $bookmarkingUser
	 */
	public function addBookmarkingUser(\Droppy\UserBundle\Entity\User $bookmarkingUser)
	{
		$this->bookmarkingUsers->add($bookmarkingUser);
	}
	
	/**
	 * Get bookmarkingUsers
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getBookmarkingUsers()
	{
		return $this->bookmarkingUsers;
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
	 * Set genre
	 *
	 * @param Droppy\WebApplicationBundle\Entity\Genre $genre
	 */
	public function setGenre(\Droppy\WebApplicationBundle\Entity\Genre $genre)
	{
		$this->genre = $genre;
	}

	/**
	 * Get genre
	 *
	 * @return Droppy\WebApplicationBundle\Entity\Genre
	 */
	public function getGenre()
	{
		return $this->genre;
	}

	/**
	 * Add event
	 *
	 * @param Droppy\WebApplicationBundle\Entity\Event $event
	 */
	public function addEvent(\Droppy\WebApplicationBundle\Entity\Event $event)
	{
		$this->events->add($events);
	}

	/**
	 * Get events
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getEvents()
	{
		return $this->events;
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
	
	public function removeDroppingUser(\Droppy\UserBundle\Entity\UserDropsScheduleRelation $userScheduleRelation)
	{
		$this->droppingUsers->removeElement($userScheduleRelation);
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
	public function createScheduleDirectory()
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
	public function removeScheduleDirectory()
	{
		DirUtils::recursiveDirRemove($this->getDirectory());
	}
	
	public function getDirectory()
	{
		return $this->getUploadRootDir() . $this->getCreator()->getId() . '/'
			. $this->getId();
	}
	
	public function getUploadRootDir()
	{
		return __DIR__ . '/../../../../web/uploads/';
	}
}
