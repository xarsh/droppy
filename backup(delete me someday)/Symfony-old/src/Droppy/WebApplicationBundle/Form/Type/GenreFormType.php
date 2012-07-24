<?php

namespace Droppy\WebApplicationBundle\Form\Type;

use Proxies\DroppyUserBundleEntityUserProxy;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Droppy\WebApplicationBundle\Entity\GenreRepository;

class GenreFormType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
	}
	
	public function getName()
	{
		return 'genre_selector';
	}
	
	public function getParent(array $options)
	{
	    return 'entity';
	}
	
	public function getDefaultOptions(array $options)
	{
		return array('label' => 'genre.name',
			'expanded' => false,
					'multiple' => false,
					'class' => 'DroppyWebApplicationBundle:Genre',
					'query_builder' => function(GenreRepository $repository)
										{
											return $repository->createQueryBuilder('g')
													->orderBy('g.name', 'ASC');
										},
					'property' => 'name');
	}
}
