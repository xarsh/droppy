<?php

namespace Droppy\EventBundle\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\EventBundle\Entity\EventDateTime;
use Droppy\MainBundle\Normalizer\TimezoneNormalizer;

class EventDateTimeMinimalNormalizer implements NormalizerInterface
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
		$data['date'] = $this->getDate($eventDateTime->getDate());
		if($eventDateTime->isAllDay() === false && $eventDateTime->getTime() !== null)
		{
			$data['time'] = $this->getTime($eventDateTime->getTime());
		}
		$data['all_day'] = $eventDateTime->isAllDay();
		$data['lol'] = false;

		return $data;
	}
	
	protected function getDate(\DateTime $date) {
	    $dateArray = array();
	    $dateArray['year'] = intval($date->format('Y'));
	    $dateArray['month'] = intval($date->format('m'));
	    $dateArray['day'] = intval($date->format('d'));
	    return $dateArray;	   	           
	}
	
	protected function getTime(\DateTime $time) {
	    $timeArray = array();
	    $timeArray['hour'] = intval($time->format('H'));
	    $timeArray['minute'] = intval($time->format('i'));
	    return $timeArray;
	}
	
	public function denormalize($data, $classOrObject, $format=null)
	{
        return null;
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof EventDateTime;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return false;
	}
}
