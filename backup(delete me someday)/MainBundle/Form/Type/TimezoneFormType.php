<?php

namespace Droppy\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class TimezoneFormType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
	}
	
	public function getName()
	{
		return 'timezone_selector';
	}
	
	public function getParent(array $options)
	{
	    return 'entity';
	}
	
	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'Droppy\MainBundle\Entity\Timezone',
			'label' => 'timezone.name',
			'expanded' => false,
			'multiple' => false,
			'class' => 'DroppyMainBundle:Timezone',
			'query_builder' => function(EntityRepository $repository)
								{
									return $repository->createQueryBuilder('t')
											->orderBy('t.difference', 'ASC');
								}
	        );
	}
}
