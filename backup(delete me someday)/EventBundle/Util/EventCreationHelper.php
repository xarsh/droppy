<?php

namespace Droppy\EventBundle\Util;

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
        $settings = $this->getUserOrDie()->getSettings();
        $event = new Event();
        $event->setColor($settings->getColor());
        $event->setPrivacySettings($this->getDefaultPrivacySettings($settings->getPrivacySettings()));
        $event->setStartDateTime($this->getDefaultEventDateTime($settings->getTimezone()));
        return $event;
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
    
    protected function getDefaultEventDateTime(Timezone $timezone)
    {
        $eventDateTime = new EventDateTime();
        $eventDateTime->setAllDay(true);
        $eventDateTime->setTimezone($timezone);
        $eventDateTime->setDate(new \DateTime());
        $eventDateTime->setTime();
        return $eventDateTime;
    }
}