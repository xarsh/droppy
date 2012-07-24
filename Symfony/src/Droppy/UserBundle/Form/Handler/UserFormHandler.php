<?php

namespace Droppy\UserBundle\Form\Handler;

use Droppy\MainBundle\Util\IconUploader;

use Droppy\UserBundle\Entity\User;

use Droppy\UserBundle\Entity\UserManager;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class UserFormHandler
{
	protected $form;
	protected $request;
	protected $userManager;
	protected $iconUploader;
	
	public function __construct(Form $form, Request $request, 
	        UserManager $userManager, IconUploader $iconUploader)
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
				$this->onSuccess($user);
				return true;
			}
		}
		return false;
	}
	
	public function onSuccess(User $user)
	{
	    $this->iconUploader->uploadUserIcon($user->getPersonalDatas());
        $this->userManager->updateUser($user);
	}
	
	public function getName()
	{
	    return 'user_form_handler';
	}
}