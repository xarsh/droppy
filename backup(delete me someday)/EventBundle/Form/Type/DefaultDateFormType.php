<?php

namespace Droppy\EventBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class DefaultDateFormType extends AbstractType 
{
	public function buildForm(FormBuilder $builder, array $options) 
	{
	}

	public function getName()
	{
		return 'date_selector';
	}
	
	public function getParent(array $options)
	{
		return 'date';
	}

	public function getDefaultOptions(array $options) 
	{
		return array(
			'widget' => 'single_text',
            'input' => 'datetime',
			'format' => 'yyyy/MM/dd',
			'required' => false
			);
	}
}