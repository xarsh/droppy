<?php

namespace Droppy\WebApplicationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class IconSetFormType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('file', 'file', 
				array('label' => 'icon.name',
					'required' => false));
	}
	
	public function getName()
	{
		return 'icon_set_type';
	}
	
	public function getDefaultOptions(array $options)
	{
		return array('data_class' => 'Droppy\WebApplicationBundle\Entity\IconSet');
	}
}
