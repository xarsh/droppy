<?php

namespace Droppy\UserBundle\Form\Type;

use Droppy\UserBundle\Form\Type\SettingsFormType;

use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('personalDatas', new PersonalDatasFormType(false))
			    ->add('username', 'text', array('label' => 'infos.name', 'required' => true))
			    ->add('email', 'email', array('label' => 'infos.email', 'required' => true))
			    ->add('plainPassword', 'repeated', array('type' => 'password', 'required' => true));
	}
	
	public function getName() 
	{
		return 'droppy_user_registration_type';
	}
}
