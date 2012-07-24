<?php

namespace Droppy\WebApplicationBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SettingsController extends Controller
{
	public function changePersonalSettingsAction()
	{
		return $this->render('DroppyWebApplicationBundle:Settings:personal_settings.html.twig');
	}
}