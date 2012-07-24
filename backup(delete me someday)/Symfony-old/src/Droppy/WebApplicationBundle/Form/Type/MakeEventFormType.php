<?php

namespace Droppy\WebApplicationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Droppy\WebApplicationBundle\Entity\GenreRepository;

class MakeEventFormType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder
			->add('name', 'text', array('label' => 'event.title'))
			->add('schedule', 'schedule_selector')
			->add('start_date_time', 'date_time_selector', array(
				'label' => 'event.start_date_time',
				'required' => true, 
				'attr' => array('class' => 'datepicker txt_field')))
			->add('end_date_time', 'date_time_selector', array(
				'label' => 'event.end_date_time',
				'required' => false, 
				'attr' => array('class' => 'datepicker txt_field')))
			->add('details', 'textarea', array('label' => 'event.details','required' => false))
			->add('location', 'text', array('label' => 'event.place','required' => false))
			->add('iconSet', 'icon_set_type', array('required' => false))
			->add('privacySettings', 'privacy_settings_type')
			->add('tags', 'tag_selector')
			->add('url', 'text', array('label' => 'event.url', 'required' => false));
			// ->add('add_date', 'date_time_selector', array(
				// 'label' => 'event.add_date_time',
				// 'required' => false, 
				// 'attr' => array('class' => 'datepicker txt_field')));
	}
	
	public function getName()
	{
		return 'make_event_type';
	}
	
	public function getDefaultOptions(array $options)
	{
		return array('data_class' => 'Droppy\WebApplicationBundle\Entity\Event');
	}
}
