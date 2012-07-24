<?php

namespace Droppy\WebApplicationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PrivacySettingsFormType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder
		->add('visibility', 'choice', array(
				'label' => 'privacy_policy.settings',
				'choices' => array('public' => 'privacy_policy.public', 
				'private' => 'privacy_policy.private',
				'protected' => 'privacy_policy.protected'),
				'expanded' => true,
				'multiple' => false))
		->get('visibility')->setData('public');
	}

	public function getName()
	{
		return 'privacy_settings_type';
	}

	public function getDefaultOptions(array $options)
	{
		return array(
				'data_class' => 'Droppy\WebApplicationBundle\Entity\PrivacySettings');
	}
}
