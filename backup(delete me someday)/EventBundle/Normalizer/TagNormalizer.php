<?php

namespace Droppy\EventBundle\Normalizer;

use Droppy\EventBundle\Entity\Tag;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TagNormalizer implements NormalizerInterface
{
	public function normalize($tag, $format=null)
	{
		$data = array();
		$data['id'] = $tag->getId();
		$data['name'] = $tag->getName();
		$data['level'] = $tag->getLevel();

		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
		return new Tag();
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof Tag;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return false;
	}
}
