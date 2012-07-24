<?php

namespace Droppy\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder
			->add('username', 'text', array('label' => 'infos.name', 'required' => true))
			->add('email', 'email', array('label' => 'infos.email', 'required' => true))
			->add('plainPassword', 'repeated', array('type' => 'password', 'required' => true))
			->add('gender', 'choice', array(
				'label' => 'infos.gender.gender',
				'choices' => array(
						'0' => 'infos.gender.male', 
						'1' => 'infos.gender.female',
						'2' => 'infos.gender.other'),
				'expanded' => true,
				'multiple' => false,
				'required' => true));
	}
	
	public function getName() 
	{
		return 'droppy_user_registration';
	}
}
