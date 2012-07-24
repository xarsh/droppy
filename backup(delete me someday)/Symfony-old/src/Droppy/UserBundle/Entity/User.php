<?php

namespace Droppy\UserBundle\Entity;
use Doctrine\DBAL\Types\SmallIntType;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Droppy\WebApplicationBundle\Util\DirUtils;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

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
     * @ORM\OneToOne(targetEntity="Droppy\WebApplicationBundle\Entity\Settings", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="settings_id", referencedColumnName="id", nullable=false)
     * @Assert\Valid()
     */
    protected $settings;

    /**
     * @var PersonalDatas $personalDatas
     *
     * @ORM\OneToOne(targetEntity="Droppy\WebApplicationBundle\Entity\PersonalDatas", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="personal_datas_id", referencedColumnName="id", nullable=false)
     * @Assert\Valid()
     */
    protected $personalDatas;

    /**
     * @var Group $group
     *
     * @ORM\ManyToOne(targetEntity="Droppy\WebApplicationBundle\Entity\UserGroup")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=true)
     * @Assert\Valid()
     */
    protected $group;

    /**
     * @var Schedule $droppedSchedules
     *
     * @ORM\OneToMany(targetEntity="Droppy\UserBundle\Entity\UserDropsScheduleRelation", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    protected $droppedSchedules;

    /**
     * @var Schedule $bookmarkedSchedules
     *
     * @ORM\ManyToMany(targetEntity="Droppy\WebApplicationBundle\Entity\Schedule", mappedBy="bookmarkingUsers")
     * @Assert\Valid()
     */
    protected $bookmarkedSchedules;

    /**
     * @var Schedule $createdSchedules
     *
     * @ORM\OneToMany(targetEntity="Droppy\WebApplicationBundle\Entity\Schedule", mappedBy="creator", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $createdSchedules;

    /**
     * @var Event $droppedEvents
     *
     * @ORM\OneToMany(targetEntity="Droppy\UserBundle\Entity\UserDropsEventRelation", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    protected $droppedEvents;

    /**
     * @var Event $createdEvents
     *
     * @ORM\OneToMany(targetEntity="Droppy\WebApplicationBundle\Entity\Event", mappedBy="creator", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $createdEvents;

    /**
     * @var Event $joinedEvents
     *
     * @ORM\ManyToMany(targetEntity="Droppy\WebApplicationBundle\Entity\Event", mappedBy="participatingUsers")
     * @Assert\Valid()
     */
    protected $joinedEvents;

    /**
     * @var ArrayCollection<Circle> $circles
     *
     * @ORM\ManyToMany(targetEntity="Droppy\WebApplicationBundle\Entity\Circle", inversedBy="users")
     * @ORM\JoinTable(name="circle_has_user")
     * @Assert\Valid()
     */
    private $circles;

    /**
     * @var int $gender
     *
     * @ORM\Column(name="gender", type="smallint", nullable=false)
     * 
     * @Assert\NotBlank(message="error.gender.blank")
     * @Assert\Choice(choices = {0, 1, 2}, message= "error.gender.invalid")
     */
    private $gender;

    public function __construct()
    {
        parent::__construct();
        $this->settings = new \Droppy\WebApplicationBundle\Entity\Settings();
        $this->personalDatas = new \Droppy\WebApplicationBundle\Entity\PersonalDatas();
        $this->droppedSchedules = new \Doctrine\Common\Collections\ArrayCollection();
        $this->bookmarkedSchedules = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdSchedules = new \Doctrine\Common\Collections\ArrayCollection();
        $this->droppedEvents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdEvents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->joinedEvents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->circles = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set settings
     *
     * @param Droppy\WebApplicationBundle\Entity\Settings $settings
     */
    public function setSettings(
            \Droppy\WebApplicationBundle\Entity\Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get settings
     *
     * @return Droppy\WebApplicationBundle\Entity\Settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Set personalDatas
     *
     * @param Droppy\WebApplicationBundle\Entity\PersonalDatas $personalDatas
     */
    public function setPersonalDatas(
            \Droppy\WebApplicationBundle\Entity\PersonalDatas $personalDatas)
    {
        $this->personalDatas = $personalDatas;
    }

    /**
     * Get personalDatas
     *
     * @return Droppy\WebApplicationBundle\Entity\PersonalDatas
     */
    public function getPersonalDatas()
    {
        return $this->personalDatas;
    }

    /**
     * Set group
     *
     * @param Droppy\WebApplicationBundle\Entity\UserGroup $group
     */
    public function setGroup(
            \Droppy\WebApplicationBundle\Entity\UserGroup $group)
    {
        $this->group = $group;
    }

    /**
     * Get group
     *
     * @return Droppy\WebApplicationBundle\Entity\UserGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Add droppedSchedules
     *
     * @param \Droppy\UserBundle\Entity\UserDropsScheduleRelation
     */
    public function addDroppedSchedule(
            \Droppy\UserBundle\Entity\UserDropsScheduleRelation $droppedSchedule)
    {
        $this->droppedSchedules[] = $droppedSchedule;
    }

    /**
     * Get droppedSchedules (as UserDropsScheduleRelation)
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getDroppedSchedules()
    {
        return $this->droppedSchedules;
    }

    /**
     * Get droppedSchedules (as Schedule)
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getDroppedSchedulesEntities()
    {
        return $this->droppedSchedules
                ->map(
                        function ($scheduleRelation)
                        {
                            return $scheduleRelation->getSchedule();
                        });
    }

    /**
     * Add bookmarkedSchedule
     *
     * @param Droppy\WebApplicationBundle\Entity\Schedule $bookmarkedSchedule
     */
    public function addBookmarkedSchedule(
            \Droppy\WebApplicationBundle\Entity\Schedule $bookmarkedSchedule)
    {
        $this->bookmarkedSchedules[] = $bookmarkedSchedule;
    }

    /**
     * Get bookmarkedSchedules
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getBookmarkedSchedules()
    {
        return $this->bookmarkedSchedules;
    }

    /**
     * Add createdSchedule
     *
     * @param Droppy\WebApplicationBundle\Entity\Schedule $createdSchedule
     */

    public function addCreatedSchedule(
            \Droppy\WebApplicationBundle\Entity\Schedule $createdSchedule)
    {
        $this->createdSchedules[] = $createdSchedule;
    }

    /**
     * Get createdSchedules
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCreatedSchedules()
    {
        return $this->createdSchedules;
    }

    /**
     * Add droppedEvent
     *
     * @param Droppy\UserBundle\Entity\UserDropsEventRelation
     */
    public function addDroppedEvent(
            \Droppy\UserBundle\Entity\UserDropsEventRelation $droppedEvent)
    {
        $this->droppedEvents[] = $droppedEvent;
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
        return $this->droppedEvents
                ->map(
                        function ($eventRelation)
                        {
                            return $eventRelation->getEvent();
                        });
    }

    /**
    
     * Add createdEvent
     *
     * @param Droppy\WebApplicationBundle\Entity\Event $createdEvents
     */

    public function addCreatedEvent(
            \Droppy\WebApplicationBundle\Entity\Event $createdEvent)
    {
        $this->createdEvents->add($createdEvent);
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
     * Add joinedEvent
     *
     * @param Droppy\WebApplicationBundle\Entity\Event $joinedEvents
     */

    public function addJoinedEvent(
            \Droppy\WebApplicationBundle\Entity\Event $joinedEvent)
    {
        $this->joinedEvents->add($joinedEvent);
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
     * @param Droppy\WebApplicationBundle\Entity\Circle $circles
     */
    public function addCircle(
            \Droppy\WebApplicationBundle\Entity\Circle $circle)
    {
        $this->circles->add($circles);
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
     * Remove dropped schedule
     * 
     * @param \Droppy\WebApplicationBundle\Entity\Schedule $schedule
     */
    public function removeDroppedSchedule(
            \Droppy\UserBundle\Entity\UserDropsScheduleRelation $schedule)
    {
        $this->droppedSchedules->removeElement($schedule);
    }

    /**
     * Remove dropped event
     * 
     * @param \Droppy\WebApplicationBundle\Entity\Event $event
     */
    public function removeDroppedEvent(
            \Droppy\UserBundle\Entity\UserDropsEventRelation $event)
    {
        $this->droppedEvents->removeElement($event);
    }
    
    
    /**
     * @ORM\PostPersist()
     */
    public function createUserDirectory()
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
