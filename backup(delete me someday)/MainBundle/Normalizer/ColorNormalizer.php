<?php

namespace Droppy\MainBundle\Normalizer;

use Droppy\MainBundle\Exception\NormalizationException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\MainBundle\Entity\Color;


class ColorNormalizer implements NormalizerInterface
{
    protected $em;
    
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
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
	    return $this->getColor($data);
	}
	
	protected function checkData($data, $string, $method)
	{
	    $color = null;
	    if(isset($data[$string]) === true)
	    {
	        $color = $this->em->getRepository('DroppyMainBundle:Color')->$method($data[$string]);
	        if($color === null)
	        {
	            throw new NotFoundHttpException("Color does not exist.");
	        }
	    }
	    return $color;
	}
	
	protected function getColor($data)
	{
	    if((($color = $this->checkData($data, 'id', 'find')) !== null)
	            || ($color = $this->checkData($data, 'name', 'findOneByName')) !== null)
	    {
	        return $color;
	    }
	    else
	    {
	        throw new NormalizationException('Missing datas for color.');
	    }
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