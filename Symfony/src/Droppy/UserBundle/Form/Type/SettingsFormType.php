<?php

namespace Droppy\UserBundle\Form\Type;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;

use Droppy\MainBundle\Form\Type\PrivacySettingsFormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SettingsFormType extends AbstractType
{
	protected $displayAll;
	protected $translator;

	public function __construct(Translator $translator, $displayAll=false)
	{
		$this->displayAll = $displayAll;
		$this->translator = $translator;
	}

	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('privacySettings', new PrivacySettingsFormType($this->translator, true));
		if($this->displayAll)
		{
			$builder->add('firstDayOfWeek', 'choice', array(
				'choices' => array(
					0 => $this->translate('settings.droppy.first_day_of_week.sunday'),
					1 => $this->translate('settings.droppy.first_day_of_week.monday')),
				'expanded' => true,
				'required' => false
			));
			$builder->add('timezone', 'timezone_selector', array('required' => false))
				//->add('color', 'color_selector', array('required' => false))
				->add('language', 'language_selector', array('required' => false));
		}
	}

	public function setDisplayAll($displayAll) 
	{
		$this->displayAll = $displayAll;    
	}

	public function getName()
	{
		return 'settings_form_type';
	}

	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'Droppy\UserBundle\Entity\Settings'
		);
	}
	protected function translate($message)
	{
		return $this->translator->trans($message, array(), 'DroppyMainBundle');
	}

}
