<?php

namespace Droppy\EventBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Droppy\EventBundle\Entity\Location;

class LocationTransformer implements DataTransformerInterface
{
	/**
		ObjectManager $om
	 */
	private $om;
	
	public function __construct(ObjectManager $om)
	{
		$this->om = $om;
	}
	
	/**
	 * Get place of Location instance 
	 *  
	 * @param Location $location
	 * @return string
	 */
	public function transform($location)
	{
		if($location === null || get_class($location) !== 'Location')
		{
		    return '';
		}
		return $location->getPlace();
	}
	
	/**
	 * Get location instance if exists, else create a new one
	 * 
	 * @param array $locationString
	 * @return Location
	 */
	public function reverseTransform($locationString)
	{
		if(is_string($locationString) === false || empty($locationString))
		{
			return null;
		}
		$location = $this->om->getRepository('DroppyEventBundle:Location')->findOneByPlace($locationString);
		if($location)
		{
		    return $location;
		}
        $location = new Location();
        $location->setPlace($locationString);
        return $location;
	}
}