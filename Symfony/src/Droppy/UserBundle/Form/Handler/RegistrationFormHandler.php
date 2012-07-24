<?php

namespace Droppy\UserBundle\Form\Handler;

use FOS\UserBundle\Model\UserInterface;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;

class RegistrationFormHandler extends BaseHandler
{
	public function process($confirmation = false)
	{
		$user = $this->userManager->createUser();
		$this->form->setData($user);
	
		if ('POST' == $this->request->getMethod()) {
			$this->form->bindRequest($this->request);
			if ($this->form->isValid()) {
				$this->onSuccess($user, $confirmation);
				return true;
			}
		}
	
		return false;
	}
	
	protected function onSuccess(UserInterface $user, $confirmation)
	{
		if ($confirmation) {
			$user->setEnabled(false);
			$this->mailer->sendConfirmationEmailMessage($user);
		} else {
			//$this->mailer->sendRegistrationEmail($user);
			$user->setConfirmationToken(null);
			$user->setEnabled(true);
		}
	
		$this->userManager->updateUser($user);
	}
}
