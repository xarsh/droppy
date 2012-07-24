<?php

namespace Droppy\MainBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RecommendController extends ContainerAware
{
    public function recommendAction()
    {
		$user = $this->container->get('security.context')->getToken()->getUser();
		$recommended_users = array();

		return $this->container->get('templating')->renderResponse('DroppyMainBundle:Layout:recommend.html.twig', array(
            'user' => $user,
			'recommended_users' => $recommended_users,
		));	
    }
}
