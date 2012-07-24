<?php

namespace Droppy\MainBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;


class IndexController extends ContainerAware
{
    public function indexAction()
    {
        if($this->container->get('security.context')->isGranted('ROLE_USER'))
        {
            return new RedirectResponse($this->container->get('router')->generate('droppy_main_top'));
        }
        $csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');
        $session = $this->container->get('request')->getSession();
        $lastUsername = ($session === null) ? '' : $session->get(SecurityContext::LAST_USERNAME);
        $error = $this->getError();
        $popularUsers = $this->container->get('doctrine')->getRepository('DroppyUserBundle:User')->getPopularUsers();
        
    	return $this->container->get('templating')->renderResponse('DroppyMainBundle:Layout:index.html.twig', array(
    	    'csrf_token' => $csrfToken,
    	    'last_username' => $lastUsername,
			'error' => $error,
		    'popular_users' => $popularUsers,
    	));

    }
    
    public function getError()
    {
    	$request = $this->container->get('request');
    	$session = $request->getSession();
    	
    	if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) 
    	{
    		$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
    	}
    	elseif ($session && $session->has(SecurityContext::AUTHENTICATION_ERROR) !== null) 
    	{
    		$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
    		$session->remove(SecurityContext::AUTHENTICATION_ERROR);
    	} 
    	else 
    	{
    		$error = '';
    	}
    	
    	if ($error) 
    	{
    		$error = $error->getMessage();
    	}
		return $error;
    }
}

