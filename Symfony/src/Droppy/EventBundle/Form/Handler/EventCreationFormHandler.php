<?php

namespace Droppy\EventBundle\Form\Handler;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Droppy\UserBundle\Entity\User;
use Droppy\UserBundle\Entity\UserManager;
use Droppy\EventBundle\Entity\Event;
use Droppy\MainBundle\Util\IconUploader;
use Droppy\EventBundle\Form\Type\DateTimeFormType;

class EventCreationFormHandler
{
	protected $form;
	protected $request;
	protected $userManager;
	
	public function __construct(Form $form, Request $request, UserManager $userManager)
	{
		$this->form = $form;
		$this->request = $request;
		$this->userManager = $userManager;
	}
	
	public function process(User $user)
	{
		if($this->request->getMethod() == 'POST')
		{
			$this->form->bindRequest($this->request);
			$this->checkStartDateTime($this->form->getData());
			if($this->form->isValid())
			{
				$this->onSuccess($this->form->getData(), $user);
				return true;
			}
		}
		return false;
	}
	
	public function checkStartDateTime(Event $event)
	{
		if($event->getStartDateTime()->getDate() === null)
		{
		    $dateForm = $this->form->get('startDateTime')->get('date');
		    $dateForm->addError(new FormError('event.error.start_date_time.null'));
		}
	}
	
	public function setEndDateTime(Event $event)
	{
		if($event->getEndDateTime()->getDate() === null)
		{
			$event->setEndDateTime();
		}
		else
		{
		    $event->getEndDateTime()->setTimezone($event->getStartDateTime()->getTimezone());
		    $event->getEndDateTime()->setAllDay($event->getStartDateTime()->isAllDay());
		}
	}
	
	
	public function onSuccess(Event $event, User $user)
	{
		$this->setEndDateTime($event);
	    $this->userManager->addCreatedEvent($user, $event);
	    $this->userManager->updateUser($user);
	    $this->addTags($event);
	    $this->userManager->updateUser($user);
	}
	
	public function addTags(Event $event)
	{
	    foreach($event->getTags() as $tag)
	    {
	        $tag->addEvent($event);
	    }
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