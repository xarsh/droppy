<?php

namespace Droppy\WebApplicationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MakeEventIncludeFormType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder
			->add('name', 'text', array('label' => 'event.title'))
			->add('schedule', 'schedule_selector')
			->add('start_date_time', 'date_time_selector', array('label' => 'event.start_date_time', 'attr' => array('class' => 'datepicker txt_field')))
			->add('privacySettings', 'privacy_settings_type');
	}
	
	public function getName()
	{
		return 'make_event_include_type';
	}
	
	public function getDefaultOptions(array $options)
	{
		return array('data_class' => 'Droppy\WebApplicationBundle\Entity\Event');
	}
}
