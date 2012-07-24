<?php

namespace Droppy\WebApplicationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Droppy\WebApplicationBundle\Form\DataTransformer\TagListTransformer;
use Doctrine\Common\Persistence\ObjectManager;

class TagFormType extends AbstractType 
{
	/**
	ObjectManager $om
	*/
	private $om;
	
	public function __construct(ObjectManager $om)
	{
		$this->om = $om;
	}
	
	public function buildForm(FormBuilder $builder, array $options) 
	{
		$transformer = new TagListTransformer($this->om);
		$builder->appendClientTransformer($transformer);
	}

	public function getName()
	{
		return 'tag_selector';
	}
	
	public function getParent(array $options)
	{
		return 'text';
	}

	public function getDefaultOptions(array $options) 
	{
		return array('data_class' => 'Droppy\WebApplicationBundle\Entity\Tag',
					'label' => 'tag.name',
					'required' => false);
	}
}
