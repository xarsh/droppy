<?php

namespace Droppy\EventBundle\Normalizer;

use Droppy\MainBundle\Normalizer\NormalizerHelper;
use Droppy\EventBundle\Entity\Tag;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TagNormalizer implements NormalizerInterface
{
    protected $helper;
    
    public function __construct(NormalizerHelper $helper)
    {
        $this->helper = $helper;
    }
    
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
		if(isset($data['id']) === true)
		{
		    $tag =  $this->helper->getFromId($data['id'], 'DroppyEventBundle:Tag');
		}
		else
		{
		    $tag = $this->helper->getFromField('name', $data['name'], 'DroppyEventBundle:Tag');
		    if($tag === null)
		    {
		        $tag = new Tag();
		    }
		}
		$tag->setName($data['name']);
		
		return $tag;
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof Tag;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return isset($data['id']) || isset($data['name']);
	}
}
