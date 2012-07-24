<?php

namespace Droppy\UserBundle\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\MainBundle\Normalizer\IconSetNormalizer;
use Droppy\UserBundle\Entity\PersonalDatas;

class PersonalDatasNormalizer implements NormalizerInterface
{
	protected $iconSetNormalizer;
	protected $generalDatasNormalizer;
	
	public function __construct(IconSetNormalizer $isn, GeneralDatasNormalizer $gdn)
	{
		$this->iconSetNormalizer = $isn;
		$this->generalDatasNormalizer = $gdn;
	}
	
	public function normalize($personalDatas, $format=null)
	{
		$data = array();
		$data['id'] = $personalDatas->getId();
		$data['displayed_name'] = $personalDatas->getDisplayedName();
		$data['introduction'] = $personalDatas->getIntroduction();
		$data['icon_set'] = $this->iconSetNormalizer->normalize($personalDatas->getIconSet());
		$data['birth_date'] = $this->generalDatasNormalizer->normalize($personalDatas->getBirthDate());
		$data['birth_place'] = $this->generalDatasNormalizer->normalize($personalDatas->getBirthPlace());
		$data['current_location'] = $this->generalDatasNormalizer->normalize($personalDatas->getCurrentLocation());

		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
		return new PersonalDatas();
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof PersonalDatas;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return false;
	}
}