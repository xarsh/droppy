<?php

namespace Droppy\MainBundle\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\UserBundle\Normalizer\UserNormalizer;
use Droppy\MainBundle\Normalizer\NormalizerHelper;
use Droppy\MainBundle\Entity\PrivacySettings;
use Droppy\UserBundle\Entity\IconSet;

class PrivacySettingsNormalizer implements NormalizerInterface
{
	protected $container;
	protected $helper;
	
	public function __construct($container, NormalizerHelper $helper)
	{
		$this->container = $container;
		$this->helper = $helper;
	}
	
	public function normalize($privacySettings, $format=null)
	{
	    $userNormalizer = $this->container->get('droppy_user.normalizer.user');
		$data = array();
		$data['id'] = $privacySettings->getId();
		$data['visibility'] = $privacySettings->getVisibility();
		$data['authorized_users'] = array();
		foreach($privacySettings->getAuthorizedUsers() as $user)
		{
		    $data['authorized_users'][] = $userNormalizer->normalize($user);
		}
		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
	    $userNormalizer = $this->container->get('droppy_user.normalizer.user');
	    $settings = $this->helper->getFromId($data['id'], 'DroppyMainBundle:PrivacySettings');
	    $this->helper->denormalizeScalars($settings, $data);
	    if(isset($data['authorized_users']))
	    {
	        $settings->setAuthorizedUsers(new ArrayCollection());
	        foreach($data['authorized_users'] as $user)
	        {
	            $settings->addAuthorizedUser($userNormalizer->denormalize($user));
	        }
	    }
		return $settings;
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof PrivacySettings;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return isset($data['id']);
	}
}