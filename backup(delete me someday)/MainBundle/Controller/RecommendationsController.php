<?php

namespace Droppy\MainBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RecommendationsController extends ContainerAware
{
    public function showRecommendationsAction()
    {
		$user = $this->container->get('security.context')->getToken()->getUser();
		$recommended_events = array();
		$recommended_users = array();
		$popular_tags = array();

		return $this->container->get('templating')->renderResponse('DroppyMainBundle:Layout:recommendations.html.twig', array(
			'recommended_events' => $recommended_events,
			'recommended_users' => $recommended_users,
			'popular_tags' => $popular_tags
		));	
    }
}
