<?php

namespace Droppy\UserBundle\Normalizer;

use Droppy\MainBundle\Normalizer\NormalizerHelper;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\MainBundle\Normalizer\IconSetNormalizer;
use Droppy\UserBundle\Entity\PersonalDatas;

class PersonalDatasNormalizer implements NormalizerInterface
{
	protected $iconSetNormalizer;
	protected $generalDatasNormalizer;
	protected $helper;
	
	public function __construct(IconSetNormalizer $isn, GeneralDatasNormalizer $gdn, NormalizerHelper $helper)
	{
		$this->iconSetNormalizer = $isn;
		$this->generalDatasNormalizer = $gdn;
		$this->helper = $helper;
	}
	
	public function normalize($personalDatas, $format=null)
	{
		$data = array();
		$data['id'] = $personalDatas->getId();
		$data['displayed_name'] = $personalDatas->getDisplayedName();
		$data['introduction'] = $personalDatas->getIntroduction();
		$data['icon_set'] = $this->iconSetNormalizer->normalize($personalDatas->getIconSet());
/*		$data['birth_date'] = $this->generalDatasNormalizer->normalize($personalDatas->getBirthDate());
		$data['birth_place'] = $this->generalDatasNormalizer->normalize($personalDatas->getBirthPlace());
		$data['current_location'] = $this->generalDatasNormalizer->normalize($personalDatas->getCurrentLocation());*/

		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
		$personalDatas = $this->helper->getFromId($data['id'], 'DroppyUserBundle:PersonalDatas');
		$this->helper->denormalizeScalars($personalDatas, $data);
		if(isset($data['icon_set']))
		{
		    $iconSet = $this->iconSetNormalizer->denormalize($data['icon_set'], 'DroppyMainBundle:IconSet');
		    $personalDatas->setIconSet($iconSet);
		}
		$generalDatas = array('birth_date', 'birth_place', 'current_location');
		foreach($generalDatas as $field)
		{
		    if(isset($data[$field]))
		    {
		        $setter = $this->helper->getSetter($field);
		        $fieldClass = 'DroppyUserBundle:' . $this->helper->getClass($variableName);
		        $fieldData = $this->generalDatasNormalizer->denormalize($data[$field], $fieldClass); 
		        $personalDatas->$setter($fieldData);
		    }
		}
		return $personalDatas;
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof PersonalDatas;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return isset($data['id']) === true;
	}
}