<?php

namespace Droppy\MainBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SearchController extends ContainerAware
{
    public function searchAction()
    {
		return $this->container->get('templating')->renderResponse('DroppyMainBundle:Layout:search.html.twig');	
    }
}
