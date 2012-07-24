<?php

namespace Droppy\EventBundle\Util;

use Droppy\MainBundle\Entity\IconSet;

use Droppy\UserBundle\Entity\PersonalDatas;

use Symfony\Component\Security\Core\SecurityContextInterface;

use Droppy\UserBundle\Entity\Settings;
use Droppy\EventBundle\Entity\Event;
use Droppy\EventBundle\Entity\EventDateTime;
use Droppy\MainBundle\Entity\PrivacySettings;
use Droppy\MainBundle\Entity\Timezone;

class EventCreationHelper
{
    protected $securityContext;
    
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }
    
    protected function getUserOrDie()
    {
        if($this->securityContext->isGranted('ROLE_USER') === false)
        {
            throw new \Exception('User not logged in.');
        }
        return $this->securityContext->getToken()->getUser();
    }
    
    public function getDefaultEvent()
    {
        $user = $this->getUserOrDie();
        $settings = $user->getSettings();
        $event = new Event();
        $event->setColor($settings->getColor());
        $timezone = $settings->getTimezone();
        $startDateTime = $this->getDefaultEventDateTime($timezone, true);
        $endDateTime = $this->getDefaultEventDateTime($timezone, false);
        $event->setPrivacySettings($this->getDefaultPrivacySettings($settings->getPrivacySettings()));
        $event->setStartDateTime($startDateTime);
        $event->setEndDateTime($endDateTime);
        $event->setIconSet($this->getIconSet($user->getPersonalDatas()->getIconSet()));
        return $event;
    }
    
    protected function getIconSet(IconSet $default)
    {
    	$iconSet = new IconSet();
    	$iconSet->setSmallIconPath($default->getSmallIconPath());
    	$iconSet->setThumbnailPath($default->getThumbnailPath());
    	$iconSet->setUploaded($default->isUploaded());
    	return $iconSet;
    }
    
    protected function getDefaultPrivacySettings(PrivacySettings $default)
    {
        $privacySettings = new PrivacySettings();
        $privacySettings->setVisibility($default->getVisibility());
        foreach($default->getAuthorizedUsers() as $user)
        {
            $privacySettings->addAuthorizedUser($user);
        }
        foreach($default->getAuthorizedCircles() as $circle)
        {
            $privacySettings->addAuthorizedCircle($circle);
        }
        return $privacySettings;
    }
    
    protected function getDefaultEventDateTime(Timezone $timezone, $start)
    {
        $eventDateTime = new EventDateTime();
        $date = new \DateTime();
        $time = new \DateTime();
        $eventDateTime->setAllDay(true);
        $eventDateTime->setTimezone($timezone);
        $eventDateTime->setDate($date);
        $this->setTime($time, $start);
        $eventDateTime->setTime($time);
        return $eventDateTime;
    }
    
    protected function setTime(\DateTime $time, $start)
    {
        if($start === false)
        {
            $time->modify('+1 hour');
        }
        if($time->format('i') > 30)
        {
            $time->modify('+1 hour');
            $time->setTime($time->format('H'), 0);
        }
        else 
        {
            $time->setTime($time->format('H'), 30);
        }
    }
}