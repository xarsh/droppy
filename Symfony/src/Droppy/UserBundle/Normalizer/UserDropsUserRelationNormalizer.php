<?php

namespace Droppy\UserBundle\Normalizer;

use Droppy\UserBundle\Entity\UserDropsUserRelation;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\EventBundle\Normalizer\EventNormalizer;

class UserDropsUserRelationNormalizer implements NormalizerInterface
{
	protected $userNormalizer;
	
	public function __construct(UserNormalizer $userNormalizer)
	{
		$this->userNormalizer = $userNormalizer;
	}
	
	public function normalize($relation, $format=null)
	{
		$data = array();
		$data['id'] = $relation->getId();
		$data['dropped'] = $this->userNormalizer->normalize($relation->getDropped());
		$data['dropping'] = $this->userNormalizer->normalize($relation->getDropping());
		$data['date'] = $relation->getDate()->format('Y-m-d');
		$data['time'] = $relation->getDate()->format('H:i');

		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
		return new UserDropsUserRelation();
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof UserDropsUserRelation;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return false;
	}
}