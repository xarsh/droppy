<?php

namespace Droppy\MainBundle\Controller;

use Symfony\Component\BrowserKit\Response;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

class StaticPagesController extends ContainerAware
{
    public function aboutusAction()
    {
    	return $this->container->get('templating')->renderResponse('DroppyMainBundle:Includes:StaticPages:aboutus.html.twig');
	}

    public function privacypolicyAction()
    {
    	return $this->container->get('templating')->renderResponse('DroppyMainBundle:Includes:StaticPages:privacypolicy.html.twig');
    }

    public function ruleAction()
    {
    	return $this->container->get('templating')->renderResponse('DroppyMainBundle:Includes:StaticPages:rule.html.twig');
	}

    public function newsAction()
    {
    	return $this->container->get('templating')->renderResponse('DroppyMainBundle:Includes:StaticPages:news.html.twig');
	}
}
