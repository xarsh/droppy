<?php

namespace Droppy\UserBundle\Normalizer;

use Droppy\MainBundle\Normalizer\NormalizerHelper;

use Droppy\UserBundle\Entity\UserManager;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\UserBundle\Entity\User;

class UserMinimalNormalizer implements NormalizerInterface
{
	protected $personalDatasNormalizer;
	
	public function __construct(PersonalDatasNormalizer $pdn)
	{
		$this->personalDatasNormalizer = $pdn;
	}
	
	public function normalize($user, $format=null)
	{
		$data = array();
		$data['id'] = $user->getId();
		$data['username'] = $user->getUsername();
		$data['personal_datas'] = $this->personalDatasNormalizer->normalize($user->getPersonalDatas());
		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
		return null;
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof User;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return false;
	}
}