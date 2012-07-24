<?php

namespace Droppy\EventBundle\Normalizer;

use Droppy\MainBundle\Normalizer\NormalizerHelper;

use Droppy\MainBundle\Exception\NormalizationException;

use Doctrine\ORM\EntityManager;
use Droppy\EventBundle\Entity\Location;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LocationNormalizer implements NormalizerInterface
{
    protected $helper;
    
    public function __construct(NormalizerHelper $helper)
    {
        $this->helper = $helper;
    }
    
	public function normalize($location, $format=null)
	{
		$data = array();
		$data['id'] = $location->getId();
		$data['place'] = $location->getPlace();

		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
	    if(empty($data['place']))
	    {
	        return null;
	    }
		if(isset($data['id']) === true)
		{
            $location = $this->helper->getFromId($data['id'], 'DroppyEventBundle:Location');
		}
		else
		{
		    $location = $this->helper->getFromField('place', $data['place'], 'DroppyEventBundle:Location');
		    if($location === null)
		    {
		         $location = new Location();
		    }
		}
		$this->helper->denormalizeScalars($location, $data);
		return $location;
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof Location;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return isset($data['id']) || isset($data['place']);
	}
}
