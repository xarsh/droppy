<?php

namespace Droppy\EventBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class DefaultTimeFormType extends AbstractType 
{
	public function buildForm(FormBuilder $builder, array $options) 
	{
	}

	public function getName()
	{
		return 'time_selector';
	}
	
	public function getParent(array $options)
	{
		return 'time';
	}

	public function getDefaultOptions(array $options) 
	{
		return array(
			'widget' => 'single_text',
            'input' => 'datetime',
            'with_seconds' => false,
			'required' => false
			);
	}
}