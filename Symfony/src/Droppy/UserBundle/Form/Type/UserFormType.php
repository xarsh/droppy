<?php

namespace Droppy\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserFormType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('username', 'text', array('label' => 'user.username', 'required' => true))
				->add('email', 'email', array('label' => 'user.username', 'required' => true))
				->add('personalDatas', 'personal_datas_form_type');
	}

	public function getName()
	{
		return 'user_form_type';
	}

	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'Droppy\UserBundle\Entity\User',
		    'validation_groups' => array('Profile')
		);
	}

}
