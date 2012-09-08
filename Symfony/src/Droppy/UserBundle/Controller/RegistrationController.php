<?php

namespace Droppy\UserBundle\Controller;

use Symfony\Component\DependencyInjection\Container;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;


class RegistrationController extends BaseController
{
    public function registerAction()
    {
        if($this->container->get('security.context')->isGranted('ROLE_USER'))
        {
            return new RedirectResponse($this->container->get('router')->generate('droppy_main_top'));
        }
        $form = $this->container->get('fos_user.registration.form');
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');
    
        $process = $formHandler->process($confirmationEnabled);
        if ($process) {
			$user = $form->getData();
            if ($confirmationEnabled) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_registration_check_email';
            } else {
                $this->authenticateUser($user);
                $this->sendEmail($user);
                $route = 'droppy_main_top';
            }
            $this->setFlash('fos_user_success', 'registration.flash.user_created');
            $url = $this->container->get('router')->generate($route);
    
            return new RedirectResponse($url);
        }
        $formView = $form->createView();
        $formView->getChild('plainPassword')->getChild('first')->set('label', 'registration.password');
        $formView->getChild('plainPassword')->getChild('second')->set('label', 'registration.password_confirmation');
    
        return $this->container->get('templating')->renderResponse('DroppyUserBundle:Layout:register.html.twig', array(
                'form' => $formView
        ));
    }
    
    protected function sendEmail(User $user)
    {
    	$t = $this->container->get('translator');
    	$message = \Swift_Message::newInstance()
    	->setSubject($t->trans('registration.subject', array(), 'email'))
    	->setFrom($this->container->getParameter('droppy_user.email.noreply'))
    	//->setTo($user->getEmail())
    	->setTo('tuvistavie@hotmail.com')
    	->setBody($t->trans('registration.content', array('%username%' => $user->getUsername()), 'email'));
    	$this->container->get('mailer')->send($message);
    	throw new \Exception($message->getBody());
    }
}
