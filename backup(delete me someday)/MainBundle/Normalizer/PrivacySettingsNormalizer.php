<?php

namespace Droppy\MainBundle\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\UserBundle\Normalizer\UserNormalizer;

use Droppy\MainBundle\Entity\PrivacySettings;
use Droppy\UserBundle\Entity\IconSet;

class PrivacySettingsNormalizer implements NormalizerInterface
{
	protected $userNormalizer;
	protected $container;
	
	public function __construct($container)
	{
		$this->container = $container;
	}
	
	public function normalize($privacySettings, $format=null)
	{
		$un = $this->container->get('droppy_user.normalizer.user');
		$data = array();
		$data['id'] = $privacySettings->getId();
		$data['visibility'] = $privacySettings->getVisibility();
		$data['authorized_user'] = $privacySettings->getAuthorizedUsers()->map(
			function(User $user) use ($un)
			{
				return $un->normalize($user);
			})->toArray();
		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
		return new PrivacySettings();
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof PrivacySettings;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return false;
	}
}