<?php

namespace Droppy\EventBundle\Normalizer;

use Symfony\Component\DependencyInjection\Container;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Droppy\EventBundle\Exception\XMLHttpRequestException;
use Droppy\EventBundle\Util\EventCreationHelper;
use Droppy\MainBundle\Normalizer\IconSetNormalizer;
use Droppy\MainBundle\Normalizer\ColorNormalizer;
use Droppy\UserBundle\Normalizer\UserNormalizer;
use Droppy\MainBundle\Normalizer\PrivacySettingsNormalizer;
use Droppy\MainBundle\Normalizer\NormalizerHelper;
use Droppy\EventBundle\Entity\Event;
use Droppy\EventBundle\Entity\Tag;



class EventBaseNormalizer implements NormalizerInterface
{
	protected $userNormalizer;
	protected $iconSetNormalizer;
	protected $privacySettingsNormalizer;
	protected $eventDateTimeNormalizer;
	protected $tagNormalizer;
	protected $locationNormalizer;
	protected $colorNormalizer;
	protected $eventCreationHelper;
	protected $normalizerHelper;
	protected $em;
	protected $user;
	
	public function __construct(Container $container)
	{
		$this->userNormalizer = $container->get('droppy_user.normalizer.user');
		$this->eventDateTimeNormalizer = $container->get('droppy_event.normalizer.event_date_time');
		$this->iconSetNormalizer = $container->get('droppy_main.normalizer.icon_set');
		$this->privacySettingsNormalizer = $container->get('droppy_main.normalizer.privacy_settings');
		$this->tagNormalizer = $container->get('droppy_event.normalizer.tag');
		$this->eventCreationHelper = $container->get('droppy_event.event_creation_helper');
		$this->colorNormalizer = $container->get('droppy_main.normalizer.color');
		$this->locationNormalizer = $container->get('droppy_event.normalizer.location');
		$this->normalizerHelper = $container->get('droppy_main.normalizer.helper');
		$this->em = $container->get('doctrine.orm.entity_manager');
		$this->user = $container->get('droppy_main.controller_tools')->getUser();
	}
	
	public function normalize($event, $format=null)
	{
		$data = array();
		$data['id'] = $event->getId();
		$data['name'] = $event->getName();
		$data['creation_date'] = $event->getCreationDate()->format('Y-m-d H:i:s');
		$data['last_update'] = $event->getLastUpdate()->format('Y-m-d H:i:s');
		$data['start_date_time'] = $this->eventDateTimeNormalizer->normalize($event->getStartDateTime());
	    $data['end_date_time'] = $this->eventDateTimeNormalizer->normalize($event->getEndDateTime());
		if($event->getLocation() !== null)
		{
			$data['location'] = $this->locationNormalizer->normalize($event->getLocation());
		}
		$data['address'] = $event->getAddress();
		$data['color'] = $this->colorNormalizer->normalize($event->getColor());
		$data['details'] = $event->getDetails();
		$data['icon_set'] = $this->iconSetNormalizer->normalize($event->getCreator()->getIconSet());
		$data['dropping_users_number'] = $event->getDroppingUsersNumber();
		$data['liking_users_number'] = $event->getLikingUsersNumber();
		$data['participating_users_number'] = $event->getParticipatingUsersNumber();
		$data['privacy_settings'] = $this->privacySettingsNormalizer->normalize($event->getPrivacySettings());
		$data['url'] = $event->getUrl();
		$data['tags'] = array();
		$data['locked'] = $event->isLocked();
		foreach($event->getTags() as $tag) 
		{
            $data['tags'][] = $this->tagNormalizer->normalize($tag);
		}
		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
	    $event = $this->getEvent($data);
	    $objectsToDenormalize = array(
	                'privacy_settings' => array($this->privacySettingsNormalizer, $event->getPrivacySettings()),
	                //'icon_set' => $this->iconSetNormalizer,
	                'color' => array($this->colorNormalizer, $event->getColor()),
	                'location' => array($this->locationNormalizer, $event->getLocation()),
	                'start_date_time' => array($this->eventDateTimeNormalizer, $event->getStartDateTime()),
	                'end_date_time' => array($this->eventDateTimeNormalizer, $event->getEndDateTime())
	            );
	    if(!isset($data['creator']) || empty($data['creator'])) {
	    	$event->setCreator($this->user);
	    }
	    $this->normalizerHelper->denormalizeScalars($event, $data);
	    foreach($objectsToDenormalize as $field => $params)
	    {
	        list($normalizer, $object) = $params; 
	        if(isset($data[$field]) === true)
	        {
	            $setter = $this->normalizerHelper->getSetter($field);
	            if(method_exists($event, $setter) === true)
	            {
	                $obj = $normalizer->denormalize($data[$field], $object);
	                if($obj !== null)
	                {
	                    $event->$setter($obj);
	                }
	            }
	        }
	    }
	    if(isset($data['tags']) === true)
	    {
	        foreach($data['tags'] as $tag)
	        {
            	$tag = $this->tagNormalizer->denormalize($tag, $this->normalizerHelper->getClass('tag'));
                $event->addTag($tag);
	        }
	    }
		return $event;
	}
	
	protected function getEvent($data)
	{
	    $event = null;
	    if(isset($data['id']))
	    {
	        $event = $this->normalizerHelper->getFromId($data['id'], 'DroppyEventBundle:Event');
	    }
	    else
	    {
	        $event = $this->eventCreationHelper->getDefaultEvent();
	    }
	    return $event;
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof Event;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return isset($data['name']) && isset($data['start_date_time']);
	}
}
