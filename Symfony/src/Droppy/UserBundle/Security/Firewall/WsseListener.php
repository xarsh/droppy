<?php

namespace Droppy\UserBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Droppy\UserBundle\Security\Authentication\Token\WsseUserToken;

class WsseListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;

    public function __construct(SecurityContextInterface $securityContext,
            AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if($request->headers->has('x-wsse'))
        {
            $wsseRegex = '/UsernameToken Username="([^"]+)", PasswordDigest="([^"]+)", Nonce="([^"]+)", Created="([^"]+)"/';

            if(preg_match($wsseRegex, $request->headers->get('x-wsse'), $matches))
            {
                $token = new WsseUserToken();
                $token->setUser($matches[1]);

                $token->setDigest($matches[2]);
                $token->setNonce($matches[3]);
                $token->setCreated($matches[4]);

                try
                {
                    $returnValue = $this->authenticationManager->authenticate($token);

                    if($returnValue instanceof TokenInterface)
                    {
                        return $this->securityContext->setToken($returnValue);
                    } 
                    else if($returnValue instanceof Response)
                    {
                        return $event->setResponse($returnValue);
                    }
                } 
                catch(AuthenticationException $e)
                {
                }
            }
        }
        $response = new Response();
        $response->setStatusCode(403);
        $event->setResponse($response);
    }
}
