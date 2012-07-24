<?php

namespace Droppy\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StaticPagesController extends Controller
{
    public function privacyAction()
    {
    	return $this->render('DroppyMainBundle:Layout:privacy_policy.html.twig');

    }

    public function termsAction()
    {
    	return $this->render('DroppyMainBundle:Layout:terms.html.twig');

    }
}

