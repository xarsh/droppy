<?php

namespace Droppy\EventBundle\Listener;

use Droppy\MainBundle\Util\IconUploader;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Droppy\EventBundle\Entity\Event;

class EventCreationListener
{
	/**
	 * Used to upload event icon
	 * 
	 * @var IconUploader $iconUploader
	 */	
	protected $iconUploader;
	
	public function __construct(IconUploader $iconUploader)
	{
		$this->iconUploader = $iconUploader;
	}
	
	public function postPersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		$em = $args->getEntityManager();
		if($entity instanceof Event)
		{
			$this->iconUploader->uploadEventIcon($entity);
			$em->flush();
		}
	}
}