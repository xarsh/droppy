<?php

namespace Droppy\WebApplicationBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Droppy\UserBundle\Entity\User;
use Droppy\UserBundle\Entity\UserManager;
use Droppy\WebApplicationBundle\Entity\Event;
use Droppy\WebApplicationBundle\Util\IconUploader;

class MakeEventFormHandler
{
	protected $form;
	protected $request;
	protected $userManager;
	protected $schedule;
	protected $iconUpload;
	
	public function __construct(Form $form, Request $request, UserManager $userManager, 
	       IconUploader $iconUploader)
	{
		$this->form = $form;
		$this->request = $request;
		$this->userManager = $userManager;		
		$this->iconUploader = $iconUploader;
	}
	
	public function process(User $user)
	{
		if($this->request->getMethod() == 'POST')
		{
			$this->form->bindRequest($this->request);
			if($this->form->isValid())
			{
				$this->onSuccess($this->form->getData(), $user);
				return true;
			}
		}
		return false;
	}
	
	public function onSuccess(Event $event, User $user)
	{
	    $this->event = $event;
	    $this->userManager->dropEvent($user, $event);
	    $this->userManager->addCreatedEvent($user, $event);
	    $this->userManager->updateUser($user);
	    $this->iconUploader->uploadEventIcon($event->getIconSet(), $event);
	}
	
	public function getName()
	{
	    return 'event_creation_handler';
	}
	
	public function getCreatedEvent()
	{
	    return $this->event;
	}
}