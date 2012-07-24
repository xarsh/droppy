<?php

namespace Droppy\UserBundle\Normalizer;

use Droppy\MainBundle\Normalizer\NormalizerHelper;

use Droppy\UserBundle\Entity\UserManager;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\UserBundle\Entity\User;

class UserNormalizer implements NormalizerInterface
{
	protected $personalDatasNormalizer;
	protected $settingsNormalizer;
	protected $userManager;
	protected $helper;
	
	public function __construct(PersonalDatasNormalizer $pdn, SettingsNormalizer $sn, 
	        UserManager $userManager, NormalizerHelper $helper)
	{
		$this->personalDatasNormalizer = $pdn;
		$this->settingsNormalizer = $sn;
		$this->userManager = $userManager;
		$this->helper = $helper;
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
		$data['has_started'] = $user->getHasStarted();
		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
	    if(isset($data['id']))
	    {
	        $user = $this->helper->getFromId($data['id'], 'DroppyUserBundle:User');
	    }
	    else
	    {
	        $user = $this->userManager->createUser();
	    }
	    $this->helper->denormalizeScalars($user, $data);
	    if(isset($data['personal_datas']) === true)
	    {
	        $personalDatas = $this->personalDatasNormalizer->
	                denormalize($data['personal_datas'], 'DroppyUserBundle:PersonalDatas');
	        $user->setPersonalDatas($personalDatas);
	    }
	    
	    if(isset($data['settings']) === true)
	    {
	        $settings = $this->settingsNormalizer->
	            denormalize($data['settings'], 'DroppyUserBundle:Settings');
	        $user->setSettings($settings);
	    }
		return $user;
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof User;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return isset($data['username']);
	}
}