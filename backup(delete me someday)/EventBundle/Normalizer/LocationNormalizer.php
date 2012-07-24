<?php

namespace Droppy\EventBundle\Normalizer;

use Droppy\MainBundle\Exception\NormalizationException;

use Doctrine\ORM\EntityManager;
use Droppy\EventBundle\Entity\Location;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LocationNormalizer implements NormalizerInterface
{
    protected $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
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
		if(isset($data['id']) === true)
		{
		    if(($location = $this->em->getRepository('DroppyEventBundle:Location')->find($data['id'])) !== null)
            {
	            return $location;	                
            }
            else
            {
                throw new NormalizationException('Location does not exist.');
            }
		}
		else
		{
		    if(($location = $this->em->getRepository('DroppyEventBundle:Location')->findOneByPlace($data['place'])) !== null)
		    {
		        return $location;
		    }
		    $location = new Location();
		    $location->setPlace($data['place']);
		    return $location;
		}
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
