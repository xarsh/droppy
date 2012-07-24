<?php

namespace Droppy\UserBundle\Form\Handler;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class SettingsFormHandler
{
	protected $form;
	protected $request;
	protected $em;
	
	public function __construct(Form $form, Request $request, EntityManager $em)
	{
		$this->form = $form;
		$this->request = $request;
		$this->em = $em;
	}
	
	public function process()
	{
		if($this->request->getMethod() == 'POST')
		{
			$this->form->bindRequest($this->request);
			if($this->form->isValid())
			{
				$this->onSuccess();
				return true;
			}
		}
		return false;
	}
	
	public function onSuccess()
	{
        $this->em->flush();
	}
	
	public function getName()
	{
	    return 'settings_form_handler';
	}
}