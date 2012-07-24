<?php

namespace Droppy\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class ColorFormType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
	}
	
	public function getName()
	{
		return 'color_selector';
	}
	
	public function getParent(array $options)
	{
	    return 'entity';
	}
	
	public function getDefaultOptions(array $options)
	{
		return array(
				//'data_class' => 'Droppy\MainBundle\Entity\Color',				
				'label' => 'color.name',
		        'expanded' => true,
		        'multiple' => false,
				'class' => 'DroppyMainBundle:Color',
		        'query_builder' => function(EntityRepository $repository)
                                   {
		                               return $repository->createQueryBuilder('c')
		                                        ->orderBy('c.name', 'ASC');
		                           }
		         );
	}
}
