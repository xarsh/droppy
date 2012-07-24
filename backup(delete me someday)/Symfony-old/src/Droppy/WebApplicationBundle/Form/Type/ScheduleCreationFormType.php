<?php

namespace Droppy\WebApplicationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Droppy\WebApplicationBundle\Entity\GenreRepository;

class ScheduleCreationFormType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder
			->add('name', 'text', array('label' => 'schedule.name'))
			->add('genre', 'genre_selector')
			->add('iconSet', 'icon_set_type')
			->add('description', 'textarea', array('label' => 'schedule.details', 'required' => false))
			->add('privacySettings', 'privacy_settings_type')
			->add('creatorVisible', 'creator_visible_selector')
			->add('tags', 'tag_selector');
	}
	
	public function getName()
	{
		return 'schedule_creation_type';
	}
	
	public function getDefaultOptions(array $options)
	{
		return array('data_class' => 'Droppy\WebApplicationBundle\Entity\Schedule');
	}
}
