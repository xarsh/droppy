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



class EventNormalizer implements NormalizerInterface
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
	}
	
	public function normalize($event, $format=null)
	{
		$data = array();
		$data['id'] = $event->getId();
		$data['name'] = $event->getName();
		$data['creator'] = $this->userNormalizer->normalize($event->getCreator());
		$data['creation_date'] = $event->getCreationDate()->format('Y-m-d H:i');
		$data['last_update'] = $event->getLastUpdate()->format('Y-m-d H:i');
		$data['start_date_time'] = $this->eventDateTimeNormalizer->normalize($event->getStartDateTime());
		if($event->getEndDateTime() !== null)
		{
		    $data['end_date_time'] = $this->eventDateTimeNormalizer->normalize($event->getEndDateTime());
		}
		if($event->getLocation() !== null)
		{
			$data['location'] = $event->getLocation()->getPlace();
		}
		$data['address'] = $event->getAddress();
		$data['color'] = $this->colorNormalizer->normalize($event->getColor());
		$data['details'] = nl2br($event->getDetails());
		$data['icon_set'] = $this->iconSetNormalizer->normalize($event->getIconSet());
		$data['dropping_users_number'] = $event->getDroppingUsersNumber();
		$data['liking_users_number'] = $event->getLikingUsersNumber();
		$data['participating_users_number'] = $event->getParticipatingUsersNumber();
		$data['privacy_settings'] = $this->privacySettingsNormalizer->normalize($event->getPrivacySettings());
		$data['url'] = $event->getUrl();
		$data['tags'] = array();
		foreach($event->getTags() as $tag) {
		    
            $data['tags'][] = $this->tagNormalizer->normalize($tag);
		}
		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
	    $event = $this->getEvent($data);
	    if(isset($data['name']))
	    {
	    	throw new XMLHttpRequestException('Event needs a name.');
	    }
	    $objectsToDenormalize = array(
	                'privacy_settings' => $this->privacySettingsNormalizer,
	                'icon_set' => $this->iconSetNormalizer,
	                'color' => $this->colorNormalizer,
	                'start_date_time' => $this->eventDateTimeNormalizer,
	                'end_date_time' => $this->eventDateTimeNormalizer
	            );
	    
	    $event->setLastUpdate(new \DateTime());
	    $this->normalizerHelper->denormalizeScalars($event, $data);
	    
		return $event;
	}
	
	protected function getEvent($data)
	{
	    $event = null;
	    if(isset($data['id']))
	    {
	        if(($event = $this->em->getRepository('DroppyEventBundle:Event')->find($data['id'])) === null)
	        {
	            throw new NotFoundHttpException(sprintf('Event %d does not exist.', $data['id']));
	        }
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