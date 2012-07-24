<?php

namespace Droppy\WebApplicationBundle\Form\Handler;

use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Droppy\UserBundle\Entity\User;
use Droppy\UserBundle\Entity\UserManager;
use Droppy\WebApplicationBundle\Entity\Schedule;
use Droppy\WebApplicationBundle\Util\IconUploader;

class ScheduleCreationFormHandler
{
	protected $form;
	protected $request;
	protected $userManager;
	protected $schedule;
	protected $iconUploader;
	
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
	
	public function onSuccess(Schedule $schedule, User $user)
	{
	    $this->schedule = $schedule;
	    $this->userManager->addCreatedSchedule($user, $schedule);
	    $this->userManager->updateUser($user);
	    $this->iconUploader->uploadScheduleIcon($schedule->getIconSet(), $schedule);
	}
	
	public function getName()
	{
	    return 'schedule_creation_handler';
	}
	
	public function getCreatedSchedule()
	{
	    return $this->schedule;
	}
}