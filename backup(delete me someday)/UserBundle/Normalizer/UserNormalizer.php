<?php

namespace Droppy\UserBundle\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\UserBundle\Entity\User;

class UserNormalizer implements NormalizerInterface
{
	protected $personalDatasNormalizer;
	protected $settingsNormalizer;
	
	public function __construct(PersonalDatasNormalizer $pdn, SettingsNormalizer $sn)
	{
		$this->personalDatasNormalizer = $pdn;
		$this->settingsNormalizer = $sn;
	}
	
	public function normalize($user, $format=null)
	{
		$data = array();
		$data['id'] = $user->getId();
		$data['username'] = $user->getUsername();
		$data['personal_datas'] = $this->personalDatasNormalizer->normalize($user->getPersonalDatas());
		$data['settings'] = $this->settingsNormalizer->normalize($user->getSettings());
		$data['gender'] = $user->getGender();
		$data['created_events_number'] = $user->getCreatedEventsNumber();
		$data['liked_events_number'] = $user->getLikedEventsNumber();
		$data['dropping_users_number'] = $user->getDroppingUsersNumber();
		$data['droppers_number'] = $user->getDroppersNumber();
		$data['dropped_events_number'] = $user->getDroppedEventsNumber();
		$data['creation_date'] = $user->getCreationDate()->format('Y-m-d H:i');
		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
		return new User();
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