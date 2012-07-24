<?php

namespace Droppy\WebApplicationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\Common\Persistence\ObjectManager;

class DateTimeFormType extends AbstractType 
{
	public function buildForm(FormBuilder $builder, array $options) 
	{
		// $transformer = new TagListTransformer($this->om);
		// $builder->appendClientTransformer($transformer);
	}

	public function getName()
	{
		return 'date_time_selector';
	}
	
	public function getParent(array $options)
	{
		return 'text';
	}

	public function getDefaultOptions(array $options) 
	{
		return array();
	}
}
