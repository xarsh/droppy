<?php

namespace Droppy\UserBundle\Form\Handler;

use Droppy\MainBundle\Util\IconUploader;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Droppy\UserBundle\Entity\User;
use Droppy\UserBundle\Entity\PersonalDatas;
use Droppy\UserBundle\Entity\UserManager;
use Droppy\EventBundle\Entity\Event;

class PersonalDatasFormHandler
{
	protected $form;
	protected $request;
	protected $em;
	protected $iconUploader;
	
	public function __construct(Form $form, Request $request, EntityManager $em, IconUploader $iconUploader)
	{
		$this->form = $form;
		$this->request = $request;
		$this->em = $em;
		$this->iconUploader = $iconUploader;
	}
	
	public function process()
	{
		if($this->request->getMethod() == 'POST')
		{
			$this->form->bindRequest($this->request);
			if($this->form->isValid())
			{
				$this->onSuccess($this->form->getData());
				return true;
			}
		}
		return false;
	}
	
	public function onSuccess(PersonalDatas $personalDatas)
	{
	    $this->iconUploader->uploadUserIcon($personalDatas);
        $this->em->flush();
	}
	
	public function getName()
	{
	    return 'personal_datas_form_handler';
	}
}