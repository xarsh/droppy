<?php

namespace Droppy\MainBundle\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\MainBundle\Entity\IconSet;


class IconSetNormalizer implements NormalizerInterface
{
	protected $imagesPath;
	protected $smallIconName;
	protected $thumbnailName;
	
	public function __construct($imagesPath, $smallIconName, $thumbnailName)
	{
		$this->imagesPath = $imagesPath;
		$this->smallIconName = $smallIconName;
		$this->thumbnailName = $thumbnailName;
	}
	
	public function normalize($iconSet, $format=null)
	{
		$data = array();
		$data['id'] = $iconSet->getId();
		if($iconSet->isUploaded())
		{
			$data['thumbnail'] = $iconSet->getThumbnailPath();
			$data['icon'] = $iconSet->getSmallIconPath();
		}
		else 
		{
			$data['thumbnail'] = $this->imagesPath . '/' . $this->thumbnailName;
			$data['icon'] = $this->imagesPath . '/' . $this->smallIconName;
		}
		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
	    $set = new IconSet();
	    $set->setUploaded(false);
	    return $set;
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof IconSet;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return false;
	}
}
