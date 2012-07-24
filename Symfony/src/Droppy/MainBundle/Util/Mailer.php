<?php
namespace Droppy\MainBundle\Util;

use FOS\UserBundle\Model\UserInterface;

use FOS\UserBundle\Mailer\Mailer as BaseMailer;

class Mailer extends BaseMailer
{
	
	protected function sendRegistrationEmail(UserInterface $user)
	{
		$template = $this->parameters['registration.template'];
		$rendered = $this->templating->render($template, array(
		            'user' => $user
		));
		$this->sendEmailMessage($rendered, $this->parameters['from_email']['confirmation'], $user->getEmail());
	}
}