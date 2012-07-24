<?php

namespace Droppy\UserBundle\Listener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Droppy\UserBundle\Entity\User;
use Droppy\WebApplicationBundle\Entity\Schedule;

class ScheduleCreator
{
	/**
	 * Used to get user manager
	 * 
	 * @var ContainerInterface $container
	 */	
	protected $container;
	
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}
	
	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		$entityManager = $args->getEntityManager();
		if($entity instanceof User)
		{
			$this->createUserSchedule($entity, $entityManager);
		}
	}
	
	public function createUserSchedule(User $user, EntityManager $em)
	{
		$schedule = new Schedule();
		$genre = $em->getRepository('DroppyWebApplicationBundle:Genre')
			->findOneByCanonicalName('other');
		$schedule->setGenre($genre);
		$schedule->getPrivacySettings()->setVisibility('private');
		$schedule->setName('My Schedule');
		$this->container->get('droppy_user_manager')->addCreatedSchedule($user, $schedule);
	}
}