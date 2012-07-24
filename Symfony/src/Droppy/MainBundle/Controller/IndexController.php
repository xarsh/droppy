<?php

namespace Droppy\MainBundle\Controller;

use Droppy\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;

class IndexController extends ContainerAware
{
    protected $route;
    protected $tools;
    
    public function indexAction()
    {
        if($this->container->get('security.context')->isGranted('ROLE_USER'))
        {
			return $this->toTop();
        }
        
        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        if($this->register($form, $formHandler))
        {
            return new RedirectResponse($this->container->get('router')->generate($this->route));
        }
        
        $csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');
        $session = $this->container->get('request')->getSession();
        $lastUsername = ($session === null) ? '' : $session->get(SecurityContext::LAST_USERNAME);
        $error = $this->getError();
        $popularUsers = $this->container->get('doctrine')->getRepository('DroppyUserBundle:User')->getSelectedUsers()->toArray();
    	return $this->container->get('templating')->renderResponse('DroppyMainBundle:Layout:index.html.twig', array(
    	    'csrf_token' => $csrfToken,
    	    'last_username' => $lastUsername,
			'error' => $error,
			'popular_users' => $popularUsers,
			'form' => $form->createView()
    	));

    }
    
    public function toTop()
    {
    	$tools = $this->container->get('droppy_main.controller_tools');
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$user = $tools->getUserRepository()->getUser($user);
    	$toBootstrap = array('user' => $user, 'dropped' => true);
    	$bootstrappedUser = $tools->normalizeData($toBootstrap, array('droppy_user.normalizer.user'));
    	$timeline = $tools->getEventRepository()->getDroppedEvents($user, $user);
    	$bootstrappedTimeline = $tools->normalizeData($timeline, array(
    	                    'droppy_user.normalizer.user_drops_event_relation',
    	                    'droppy_event.normalizer.event_minimal'));
    	
    	return $this->container->get('templating')->renderResponse('DroppyMainBundle:Layout:top.html.twig', array(
    				    'user' => $user,
    			        'bootstrappedUser' => $bootstrappedUser,
    			        'bootstrappedTimeline' => $bootstrappedTimeline,
    			        'bootstrappedCalendar' => $this->getCalendarEvents($user)
    	));
    }
    
    protected function getCalendarEvents(User $user)
    {
    	$tools = $this->container->get('droppy_main.controller_tools');
    	$start = new \DateTime;
    	$start->modify('first day of last month');
    	$end = new \DateTime;
    	$end->modify('last day of next month');
    	$results = $tools->getEventRepository()->eventsInRange($user, $user, $start, $end);
    	$encodedDatas = $tools->normalizeData($results, array(
    	        	'droppy_event.normalizer.event_minimal',
    	        	'droppy_user.normalizer.user_drops_event_relation'
    	));
    	return $encodedDatas;
    }
    
    public function register($form, $formHandler)
    {
        if($this->container->get('security.context')->isGranted('ROLE_USER'))
        {
            $this->route = 'droppy_main_index';
            return true;            
        }
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');
        $process = $formHandler->process($confirmationEnabled);
        if ($process) {
            $user = $form->getData();
            if($confirmationEnabled) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $this->route = 'fos_user_registration_check_email';
            } else {
                $this->authenticateUser($user);
                $this->route = 'droppy_main_index';
            }
            $this->container->get('session')->setFlash('fos_user_success', 'registration.flash.user_created');
    
            return true;
        }
        return false;
    }
    
    protected function authenticateUser(UserInterface $user)
    {
        try {
            $this->container->get('fos_user.user_checker')->checkPostAuth($user);
        } catch (AccountStatusException $e) {
            return;
        }
    
        $providerKey = $this->container->getParameter('fos_user.firewall_name');
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
    
        $this->container->get('security.context')->setToken($token);
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

