<?php

namespace Droppy\EventBundle\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\EventBundle\Entity\EventDateTime;
use Droppy\MainBundle\Normalizer\TimezoneNormalizer;

class EventDateTimeNormalizer implements NormalizerInterface
{
	protected $timezoneNormalizer;
	
	public function __construct(TimezoneNormalizer $tn)
	{
		$this->timezoneNormalizer = $tn;
	}
	
	public function normalize($eventDateTime, $format=null)
	{
		$data = array();
		$data['id'] = $eventDateTime->getId();
		$data['date'] = $eventDateTime->getDate()->format('Y-m-d');
		if($eventDateTime->getTime() !== null)
		{
			$data['time'] = $eventDateTime->getTime()->format('H:i');
		}
		$data['timezone'] = $this->timezoneNormalizer->normalize($eventDateTime->getTimezone());
		$data['is_all_day'] = $eventDateTime->isAllDay();

		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
		return new EventDateTime();
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof EventDateTime;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return isset($data['date']) && isset($data['timezone']) && isset($data['is_all_day']);
	}
}
