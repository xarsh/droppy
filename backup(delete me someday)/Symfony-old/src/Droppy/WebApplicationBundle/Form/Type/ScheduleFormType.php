<?php

namespace Droppy\WebApplicationBundle\Form\Type;

use Proxies\DroppyUserBundleEntityUserProxy;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Droppy\UserBundle\Entity\User;
use Droppy\WebApplicationBundle\Entity\Schedule;
use Droppy\WebApplicationBundle\Entity\ScheduleRepository;

class ScheduleFormType extends AbstractType
{
	protected $user;

	public function __construct($securityContext)
	{
		$this->user = $securityContext->getToken()->getUser();
	}

	public function buildForm(FormBuilder $builder, array $options)
	{
	}
	
	public function getName()
	{
		return 'schedule_selector';
	}
	
	public function getParent(array $options)
	{
	    return 'entity';
	}
	
	public function getDefaultOptions(array $options)
	{
		$userId = $this->user->getId();
		return array('label' => 'schedule.name',
			'expanded' => false,
					'multiple' => false,
					'class' => 'DroppyWebApplicationBundle:Schedule',
					'query_builder' => function(ScheduleRepository $repository) use ($userId)
							{
								return $repository->getScheduleQueryByCreator($userId);
							},
					'property' => 'name');
	}
}
