<?php

namespace Droppy\MainBundle\Normalizer;

use Droppy\MainBundle\Exception\ObjectNotFoundException;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\MainBundle\Entity\Timezone;

class TimezoneNormalizer implements NormalizerInterface
{
    protected $helper;
    
    public function __construct(NormalizerHelper $helper)
    {
        $this->helper = $helper;
    }
    
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
	    if(isset($data['id']))
	    {
	        $timezone = $this->helper->getFromId($data['id'], 'DroppyMainBundle:Timezone');
	    }
	    if(($timezone = $this->helper->getFromField('name', $data['name'], 'DroppyMainBundle:Timezone')) === null)
	    {
	        throw new ObjectNotFoundException('Non valid timezone');
	    }
		return $timezone;
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof Timezone;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return isset($data['id']) || isset($data['name']);
	}
}

