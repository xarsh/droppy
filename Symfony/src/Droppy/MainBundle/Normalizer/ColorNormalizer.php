<?php

namespace Droppy\MainBundle\Normalizer;

use Droppy\MainBundle\Exception\NormalizationException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\MainBundle\Entity\Color;


class ColorNormalizer implements NormalizerInterface
{
    protected $helper;
    
	public function __construct(NormalizerHelper $helper)
	{
		$this->helper = $helper;
	}
	
	public function normalize($color, $format=null)
	{
		$data = array();
		$data['id'] = $color->getId();
		$data['name'] = $color->getName();
		$data['code'] = $color->getCode();
		
		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
	    if(isset($data['id']))
	    {
	        $this->helper->getFromId($data['id'], 'DroppyMainBundle:Color');
	    }
	    if(($color = $this->helper->getFromField('name', $data['name'], 'DroppyMainBundle:Color')) === null)
	    {
	        throw new NotFoundHttpException("Color does not exist.");
	    }
	    return $color;
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof Color;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return isset($data['id']) === true || isset($data['name']) === true;
	}
}