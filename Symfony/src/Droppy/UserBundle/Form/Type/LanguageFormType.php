<?php

namespace Droppy\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class LanguageFormType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
	}
	
	public function getName()
	{
		return 'language_selector';
	}
	
	public function getParent(array $options)
	{
	    return 'entity';
	}
	
	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'Droppy\UserBundle\Entity\Language',
			'label' => 'language.name',
			'expanded' => false,
			'multiple' => false,
			'class' => 'DroppyUserBundle:Language',
			'query_builder' => function(EntityRepository $repository)
								{
									return $repository->createQueryBuilder('l')
											->orderBy('l.name', 'ASC');
								});
	}
}
