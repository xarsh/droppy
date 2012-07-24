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



class EventMinimalNormalizer implements NormalizerInterface
{
	protected $userNormalizer;
	protected $iconSetNormalizer;
	protected $eventDateTimeNormalizer;
	protected $locationNormalizer;
	protected $colorNormalizer;
	
	public function __construct(Container $container)
	{
		$this->userNormalizer = $container->get('droppy_user.normalizer.user_minimal');
		$this->eventDateTimeNormalizer = $container->get('droppy_event.normalizer.event_date_time');
		$this->iconSetNormalizer = $container->get('droppy_main.normalizer.icon_set');
		$this->colorNormalizer = $container->get('droppy_main.normalizer.color');
		$this->locationNormalizer = $container->get('droppy_event.normalizer.location');
	}
	
	public function normalize($event, $format=null)
	{
		$data = array();
		$data['id'] = $event->getId();
		$data['name'] = $event->getName();
		$data['creator'] = $this->userNormalizer->normalize($event->getCreator());
		$data['start_date_time'] = $this->eventDateTimeNormalizer->normalize($event->getStartDateTime());
	    $data['end_date_time'] = $this->eventDateTimeNormalizer->normalize($event->getEndDateTime());
		if($event->getLocation() !== null)
		{
			$data['location'] = $this->locationNormalizer->normalize($event->getLocation());
		}
		$data['color'] = $this->colorNormalizer->normalize($event->getColor());
		$data['icon_set'] = $this->iconSetNormalizer->normalize($event->getIconSet());
		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
	    return null;
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof Event;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return false;
	}
}