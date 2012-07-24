<?php

namespace Droppy\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Droppy\MainBundle\Util\DirUtils;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\DBAL\Types\SmallIntType;

/**
 * Droppy\UserBundle\Entity\User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Droppy\UserBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields="username",message="error.username.exists")
 * @UniqueEntity(fields="email", message="error.email.exists")
 */
class User extends BaseUser
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
    * @var PersonalDatas $personalDatas
    *
    * @ORM\OneToOne(targetEntity="Droppy\UserBundle\Entity\PersonalDatas", cascade={"persist", "remove"})
    * @ORM\JoinColumn(name="personal_datas_id", referencedColumnName="id", nullable=false)
    * @Assert\Valid(groups={"Profile"})
    */
    protected $personalDatas;
    
    /**
     * @var Settings $settings
     *
     * @ORM\OneToOne(targetEntity="Droppy\UserBundle\Entity\Settings", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="settings_id", referencedColumnName="id", nullable=false)
     * @Assert\Valid()
     */
    protected $settings;
    
    /**
    * @var ArrayCollection $droppers
    *
    * @ORM\OneToMany(targetEntity="Droppy\UserBundle\Entity\UserDropsUserRelation", mappedBy="dropped")
    */
    protected $droppers;
    
    /**
    * @var ArrayCollection $droppingUsers
    *
    * @ORM\OneToMany(targetEntity="Droppy\UserBundle\Entity\UserDropsUserRelation", mappedBy="dropping", cascade={"persist", "remove"}, orphanRemoval=true)
    */
    protected $droppingUsers;
    
    /**
    * @var ArrayCollection $droppedEvents
    *
    * @ORM\OneToMany(targetEntity="Droppy\UserBundle\Entity\UserDropsEventRelation", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
    * @Assert\Valid()
    */
    protected $droppedEvents;
    
    /**
     * @var ArrayCollection $likedByOtherEvents;
     *
     * @ORM\OneToMany(targetEntity="Droppy\UserBundle\Entity\EventLikedByOther", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    protected $likedByOtherEvents;
        
    
    /**
     * @var Event $createdEvents
     *
     * @ORM\OneToMany(targetEntity="Droppy\EventBundle\Entity\Event", mappedBy="creator", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $createdEvents;
    
    /**
     * @var Event $joinedEvents
     *
     * @ORM\ManyToMany(targetEntity="Droppy\EventBundle\Entity\Event", mappedBy="participatingUsers")
     * @Assert\Valid()
     */
    protected $joinedEvents;
    
    /**
    * @var Event $likedEvents
    *
    * @ORM\ManyToMany(targetEntity="Droppy\EventBundle\Entity\Event", mappedBy="likingUsers")
    * @Assert\Valid()
    */
    protected $likedEvents;
    
    /**
    * @var ArrayCollection<Circle> $circles
    *
    * @ORM\ManyToMany(targetEntity="Droppy\UserBundle\Entity\Circle", inversedBy="users")
    * @ORM\JoinTable(name="circle_has_user")
    * @Assert\Valid()
    */
    private $circles;
    
    
    /**
     * @var int $gender
     *
     * @ORM\Column(name="gender", type="smallint", nullable=true)
     *
     * @Assert\Choice(choices = {0, 1, 2}, message= "error.gender.invalid")
     */
    private $gender;
    
    /**
     * @var int $createdEventsNumber
     * 
     * @ORM\Column(name="created_events_number", type="integer", nullable=false)
     */
    private $createdEventsNumber;
    
    /**
    * @var int $likedEventsNumber
    *
    * @ORM\Column(name="liked_events_number", type="integer", nullable=false)
    */
    private $likedEventsNumber;
    
    /**
     * @var int droppingUsersNumber
     * 
     * @ORM\Column(name="dropping_users_number", type="integer", nullable=false)
     */
    private $droppingUsersNumber;
    
    /**
    * @var int droppersNumber
    *
    * @ORM\Column(name="droppers_number", type="integer", nullable=false)
    */
    private $droppersNumber;
    
    /**
    * @var int droppedEventsNumber
    *
    * @ORM\Column(name="dropped_events_number", type="integer", nullable=false)
    */
    private $droppedEventsNumber;
    
    /**
     * @var DateTime $creationDate
     *
     * @ORM\Column(name="creation_date", type="datetime", nullable=false)
     */
    private $creationDate;
    
    /**
     * @var boolean hasStarted
     * 
     * @ORM\Column(name="has_started", type="boolean", nullable=false)
     */
    private $hasStarted;
    
    
    public function __construct()
    {
    	parent::__construct();
    	$this->personalDatas = new PersonalDatas();
    	$this->droppedEvents = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->droppers = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->droppingUsers = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->createdEvents = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->joinedEvents = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->circles = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->creationDate = new \DateTime();
    	$this->settings = new Settings();
    	$this->hasStarted = false;
    	$this->createdEventsNumber = 0;
    	$this->likedEventsNumber = 0;
    	$this->droppersNumber = 0;
    	$this->droppedEventsNumber = 0;
    	$this->droppingUsersNumber = 0;
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
     * Set personalDatas
     *
     * @param PersonalDatas $personalDatas
     */
    public function setPersonalDatas(PersonalDatas $personalDatas)
    {
    	$this->personalDatas = $personalDatas;
    }
    
    /**
     * Get personalDatas
     *
     * @return PersonalDatas
     */
    public function getPersonalDatas()
    {
    	return $this->personalDatas;
    }
    
    /**
    * Add droppedEvent
    *
    * @param UserDropsEventRelation
    */
    public function addDroppedEvent(UserDropsEventRelation $droppedEvent)
    {
    	$this->droppedEvents[] = $droppedEvent;
    	$this->incrementDroppedEventsNumber();
    }
    
    /**
     * Get droppedEvents (as UserDropsEventRelation)
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getDroppedEvents()
    {
    	return $this->droppedEvents;
    }
    
    /**
     * Get droppedEvents (as Event)
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getDroppedEventsEntities()
    {
    	return $this->droppedEvents->map(
    	function ($eventRelation)
    	{
    		return $eventRelation->getEvent();
    	});
    }
    
    /**
     * Add likedByOtherEvent
     *
     * @param EventLikedByOther
     */
    public function addLikedByOtherEvent(EventLikedByOther $event)
    {
        $this->likedByOtherEvents[] = $event;
    }
    
    /**
     * Get EventLikedByOthers
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getLikedByOtherEvents()
    {
        return $this->likedByOtherEvents;
    }
    
    /**
    * Add createdEvent
    *
    * @param Droppy\EventBundle\Entity\Event $createdEvents
    */
    
    public function addCreatedEvent(\Droppy\EventBundle\Entity\Event $createdEvent)
    {
    	$this->createdEvents[] = $createdEvent;
    	$this->incrementCreatedEventsNumber();
    }
    
    /**
     * Get createdEvents
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCreatedEvents()
    {
    	return $this->createdEvents;
    }
    
    /**
    * Add dropping user
    *
    * @param Droppy\UserBundle\Entity\User $droppingUser
    */
    
    public function addDroppingUser(UserDropsUserRelation $droppingUser)
    {
    	$this->droppingUsers[] = $droppingUser;
    	$this->incrementDroppingUsersNumber();
    }
    
    /**
     * Get dropping users
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getDroppingUsers()
    {
    	return $this->droppingUsers;
    }
    
    /**
    * Get dropping users as User
    *
    * @return Doctrine\Common\Collections\Collection
    */
    public function getDroppingUsersAsUsers()
    {
    	return $this->droppingUsers->map(function(UserDropsUserRelation $user) {
    		return $user->getDropped();
    	});
    }
    
    /**
    * Add dropper
    *
    * @param Droppy\UserBundle\Entity\User $dropper
    */
    
    public function addDropper(UserDropsUserRelation $dropper)
    {
    	$this->droppers[] = $dropper;
    	$this->incrementDroppersNumber();
    }
    
    /**
     * Get droppers
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getDroppers()
    {
    	return $this->droppers;
    }
    
    /**
    * Get droppers as User
    *
    * @return Doctrine\Common\Collections\Collection
    */
    public function getDroppersAsUsers()
    {
    	return $this->droppers->map(function(UserDropsUserRelation $user) {
    		return $user->getDropping();
    	});
    }
    
    
    /**
    * Add likedEvent
    *
    * @param Droppy\EventBundle\Entity\Event $likedEvents
    */
    public function addLikedEvent(\Droppy\EventBundle\Entity\Event $likedEvent)
    {
    	$this->likedEvents[] = $likedEvent;
    	$this->incrementLikedEventsNumber();
    }
    
    /**
     * Get likeEvents
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getLikedEvents()
    {
    	return $this->likedEvents;
    }
    
    /**
     * Add joinedEvent
     *
     * @param Droppy\EventBundle\Entity\Event $joinedEvents
     */
    
    public function addJoinedEvent(\Droppy\EventBundle\Entity\Event $joinedEvent)
    {
    	$this->joinedEvents[] = $joinedEvent;
    }
    
    /**
     * Get joinedEvents
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getJoinedEvents()
    {
    	return $this->joinedEvents;
    }
    
    /**
    * Add circles
    *
    * @param Circle $circles
    */
    public function addCircle(Circle $circle)
    {
    	$this->circles[] = $circles;
    }
    
    /**
     * Get circles
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCircles()
    {
    	return $this->circles;
    }
    
    /**
     * Set gender
     *
     * @param smallint $gender
     */
    public function setGender($gender)
    {
    	$this->gender = $gender;
    }
    
    /**
     * Get gender
     *
     * @return smallint
     */
    public function getGender()
    {
    	return $this->gender;
    }
    
    /**
     * Set settings
     *
     * @param Settings $settings
     */
    public function setSettings(Settings $settings)
    {
        $this->settings = $settings;
    }
    
    /**
     * Get settings
     *
     * @return settings $settings
     */
    public function getSettings()
    {
        return $this->settings;
    }
    
  	/**
  	 * Get created events number
  	 * 
  	 * @return integer 
  	 */
    public function getCreatedEventsNumber()
    {
    	return $this->createdEventsNumber;
    }
    
    /**
     * Set created events number
     * 
     * @param integer $createdEventsNumber
     */
    public function setCreatedEventsNumber($createdEventsNumber)
    {
    	$this->createdEventsNumber = $createdEventsNumber;
    }
    
    /**
     * Increments created events number
     */
    public function incrementCreatedEventsNumber()
    {
    	$this->createdEventsNumber++;	
    }
    
    /**
    * Decrements created events number
    */
    public function decrementCreatedEventsNumber()
    {
    	$this->createdEventsNumber--;
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
    * Get dropped events number
    *
    * @return integer
    */
    public function getDroppedEventsNumber()
    {
    	return $this->droppedEventsNumber;
    }
    
    /**
     * Set dropped events number
     *
     * @param integer $droppedEventsNumber
     */
    public function setDroppedEventsNumber($droppedEventsNumber)
    {
    	$this->droppedEventsNumber = $droppedEventsNumber;
    }
    
    /**
     * Increments dropped events number
     */
    public function incrementDroppedEventsNumber()
    {
    	$this->droppedEventsNumber++;
    }
    
    /**
     * Decrements dropped events number
     */
    public function decrementDroppedEventsNumber()
    {
    	$this->droppedEventsNumber--;
    }
    
    /**
    * Get liked events number
    *
    * @return integer
    */
    public function getLikedEventsNumber()
    {
    	return $this->likedEventsNumber;
    }
    
    /**
     * Set liked events number
     *
     * @param integer $likedEventsNumber
     */
    public function setLikedEventsNumber($likedEventsNumber)
    {
    	$this->likedEventsNumber = $likedEventsNumber;
    }
    
    /**
     * Increments liked events number
     */
    public function incrementLikedEventsNumber()
    {
    	$this->likedEventsNumber++;
    }
    
    /**
     * Decrements liked events number
     */
    public function decrementLikedEventsNumber()
    {
    	$this->likedEventsNumber--;
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
    * Get droppers number
    *
    * @return integer
    */
    public function getDroppersNumber()
    {
    	return $this->droppersNumber;
    }
    
    /**
     * Set droppers number
     *
     * @param integer $droppersNumber
     */
    public function setDroppersNumber($droppersNumber)
    {
    	$this->droppersNumber = $droppersNumber;
    }
    
    /**
     * Increments droppers number
     */
    public function incrementDroppersNumber()
    {
    	$this->droppersNumber++;
    }
    
    /**
     * Decrements droppers number
     */
    public function decrementDroppersNumber()
    {
    	$this->droppersNumber--;
    }
    
    /**
     * Returns events created by user with set visibility
     * 
     * @param string $visibility
     * @return ArrayCollection
     */
    public function getCreatedEventsWithVisibility($visibility)
    {
        return $this->createdEvents->filter(
                function($createdEvent) use($visibility)
                {
                    return $createdEvent->getPrivacySettings()->getVisibility() === $visibility;
                });
    }
    
    /**
    * Remove dropped event
    *
    * @param \Droppy\UserBundle\Entity\UserDropsEventRelation $event
    */
    public function removeDroppedEvent(UserDropsEventRelation $event)
    {
    	$this->droppedEvents->removeElement($event);
    	$this->decrementDroppedEventsNumber();
    }
    
    /**
    * Remove created event
    *
    * @param \Droppy\EventBundle\Entity\Event $event
    */
    public function removeCreatedEvent(\Droppy\EventBundle\Entity\Event $event)
    {
    	$this->createdEvents->removeElement($event);
    	$this->decrementCreatedEventsNumber();
    }
    
    /**
    * Remove liked event
    *
    * @param \Droppy\EventBundle\Entity\Event $event
    */
    public function removeLikedEvent(\Droppy\EventBundle\Entity\Event $event)
    {
    	$this->likedEvents->removeElement($event);
    	$this->decrementLikedEventsNumber();
    }
    
    /**
    * Remove dropped user
    *
    * @param UserDropsUserRelation $dropper
    */
    public function removeDropper(UserDropsUserRelation $dropper)
    {
    	$this->droppers->removeElement($dropper);
    	$this->decrementDroppersNumber();
    }
    
    /**
    * Remove dropping user
    *
    * @param UserDropsUserRelation $droppingUser
    */
    public function removeDroppingUser(UserDropsUserRelation $droppingUser)
    {
    	$this->droppingUsers->removeElement($droppingUser);
    	$this->decrementDroppingUsersNumber();
    }
    
    /**
     * @param boolean $hasStarted
     */
    public function setHasStarted($hasStarted)
    {
        $this->hasStarted = $hasStarted;
    }
    
    /**
     * @return boolean
     */
    public function getHasStarted()
    {
        return $this->hasStarted;
    }    
    
    /**
     * @ORM\PostPersist()
     */
    public function createUserDirectory()
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
    public function removeUserDirectory()
    {
    	DirUtils::recursiveDirRemove($this->getDirectory());
    }
    
    public function getDirectory()
    {
    	return $this->getUploadRootDir() . $this->getId();
    }
    
    public function getUploadRootDir()
    {
    	return __DIR__ . '/../../../../web/uploads/';
    }
}