<?php

namespace Droppy\UserBundle\Normalizer;

use Droppy\UserBundle\Entity\UserDropsEventRelation;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\EventBundle\Normalizer\EventNormalizer;

class UserDropsEventRelationNormalizer implements NormalizerInterface
{
	protected $eventNormalizer;
	
	public function __construct(EventNormalizer $en)
	{
		$this->eventNormalizer = $en;
	}
	
	public function normalize($relation, $format=null)
	{
		$data = array();
		$data['id'] = $relation->getId();
		$data['event'] = $this->eventNormalizer->normalize($relation->getEvent());
		$data['date'] = $relation->getDate()->format('Y-m-d');
		$data['time'] = $relation->getDate()->format('H:i');
		$data['liked'] = $relation->isEventLiked();
		$data['in_calendar'] = $relation->isInCalendar();
		$data['user_is_creator'] = $relation->isUserIsCreator();

		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
		return new Settings();
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof UserDropsEventRelation;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return false;
	}
}