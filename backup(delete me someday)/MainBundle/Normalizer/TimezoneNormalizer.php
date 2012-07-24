<?php

namespace Droppy\MainBundle\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\MainBundle\Entity\Timezone;

class TimezoneNormalizer implements NormalizerInterface
{
	public function normalize($timezone, $format=null)
	{
		$data = array();
		$data['id'] = $timezone->getId();
		$data['name'] = $timezone->getName();
		$data['difference'] = $timezone->getDifference();		

		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
		return new Timezone();
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof Timezone;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return false;
	}
}

