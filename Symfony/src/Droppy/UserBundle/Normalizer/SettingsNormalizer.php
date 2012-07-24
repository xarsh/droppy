<?php

namespace Droppy\UserBundle\Normalizer;

use Droppy\MainBundle\Normalizer\ColorNormalizer;

use Droppy\MainBundle\Normalizer\NormalizerHelper;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Droppy\MainBundle\Normalizer\IconSetNormalizer;
use Droppy\MainBundle\Normalizer\TimeZoneNormalizer;
use Droppy\UserBundle\Entity\Settings;
use Droppy\MainBundle\Normalizer\PrivacySettingsNormalizer;

class SettingsNormalizer implements NormalizerInterface
{
	protected $timezoneNormalizer;
	protected $privacySettingsNormalizer;
	protected $colorNormalizer;
	protected $languageNormalizer;
	protected $helper;
	
	
	public function __construct(TimeZoneNormalizer $tzn, PrivacySettingsNormalizer $psn, 
	        ColorNormalizer $cn, NormalizerHelper $helper)
	{
		$this->timezoneNormalizer = $tzn;
		$this->privacySettingsNormalizer = $psn;
		$this->colorNormalizer = $cn;
		$this->helper = $helper;
	}
	
	public function normalize($settings, $format=null)
	{
		$data = array();
		$data['id'] = $settings->getId();
		$data['privacy_settings'] = $this->privacySettingsNormalizer->normalize($settings->getPrivacySettings());
		$data['wallpaper'] = $settings->getWallpaper();
		$data['timezone'] = $this->timezoneNormalizer->normalize($settings->getTimezone());
		$data['language'] = $settings->getLanguage()->getName();
		$data['color'] = $this->colorNormalizer->normalize($settings->getColor());
		$data['first_day_of_week'] = $settings->getFirstDayOfWeek();

		return $data;
	}
	
	public function denormalize($data, $class, $format=null)
	{
	    $settings = $this->helper->getFromId($data['id'], 'DroppyUserBundle:Settings');
	    $this->helper->denormalizeScalars($settings, $data);
	    $objects = array(
	            'privacy_settings' => array($this->privacySettingsNormalizer, 'DroppyUserBundle:PrivacySettings'),
	            'timezone' => array($this->timezoneNormalizer, 'DroppyMainBundle:Timezone'),
	            'language' => array($this->languageNormalizer, 'DroppyMainBundle:Language'),
	            'color' => array($this->colorNormalizer, 'DroppyMainBundle:Color'),
	            );
	    foreach($objects as $field => $value)
	    {
	        if(isset($data[$field]) === true)
	        {
	            $setter = $this->helper->getSetter($field);
	            if(method_exists($settings, $setter))
	            {
	                $denormalizer = $value[0];
	                $fieldClass = $value[1];
	                $settings->$setter($denormalizer->denormalize($data[$field], $fieldClass));
	            }
	        }
	    }
		return $settings;
	}
	
	public function supportsNormalization($data, $format=null)
	{
		return $data instanceof Settings;
	}
	
	public function supportsDenormalization($data, $type, $format=null)
	{
		return isset($data['id']);
	}
}