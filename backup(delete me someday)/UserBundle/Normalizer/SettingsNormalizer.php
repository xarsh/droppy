<?php

namespace Droppy\UserBundle\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\MainBundle\Normalizer\IconSetNormalizer;
use Droppy\MainBundle\Normalizer\TimeZoneNormalizer;
use Droppy\UserBundle\Entity\Settings;
use Droppy\MainBundle\Normalizer\PrivacySettingsNormalizer;

class SettingsNormalizer implements NormalizerInterface
{
	protected $timezoneNormalizer;
	protected $privacySettingsNormalizer;
	
	public function __construct(TimeZoneNormalizer $tzn, PrivacySettingsNormalizer $psn)
	{
		$this->timezoneNormalizer = $tzn;
		$this->privacySettingsNormalizer = $psn;
	}
	
	public function normalize($settings, $format=null)
	{
		$data = array();
		$data['id'] = $settings->getId();
		$data['privacy_settings'] = $this->privacySettingsNormalizer->normalize($settings->getPrivacySettings());
		$data['wallpaper'] = $settings->getWallpaper();
		$data['timezone'] = $this->timezoneNormalizer->normalize($settings->getTimezone());
		$data['language'] = $settings->getLanguage()->getName();
		$data['color'] = $settings->getColor()->getName();
		$data['first_day_of_week'] = $settings->getFirstDayOfWeek();

		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
		return new Settings();
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof Settings;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return false;
	}
}