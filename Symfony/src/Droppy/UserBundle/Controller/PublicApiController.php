<?php

namespace Droppy\UserBundle\Controller;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Droppy\UserBundle\Entity\UserDropsEventRelation;
use Droppy\EventBundle\Entity\Event;
use Droppy\EventBundle\Exception\XMLHttpRequestException;
use Symfony\Component\HttpFoundation\Response;


class PublicApiController
{
    protected $container;
    protected $tools;
    
    public function __construct($container)
    {
        $this->container = $container;
        $this->tools = $this->container->get('droppy_main.controller_tools');
    }
    
    public function getPublicKeyAction()
    {
        list($fileName, $token) = $this->checkAndGetDatas();

        list($publicKey, $privateKey) = $this->generateKeys();

        file_put_contents($fileName, $privateKey);

        return new Response($publicKey, 200, array('Content-Type', 'text/plain'));
    }
    
    public function createUserAction()
    {
        $parameters = $this->tools->getParameters();
        $parameters['plain_password'] = $this->getPlainPassword($parameters['encrypted_password'], $parameters['token']);
        $user = $this->container->get('droppy_user.normalizer.user')->denormalize($parameters, 'DroppyUserBundle:User');
        $user->getPersonalDatas()->setDisplayedName($parameters['displayed_name']);
        $errors = $this->container->get('validator')->validate($user, array('Registration'));
        $this->tools->checkErrors($errors);

        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $this->container->get('droppy_user.user_manager')->updateUser($user);
        unlink($this->getKeyFile($parameters['token']));
        $encodedDatas = $this->tools->normalizeData($user, array('droppy_user.normalizer.user'));
        
        return new Response($encodedDatas, 200, array('Content-Type', 'application/json'));
    }
    
    protected function getPlainPassword($password, $token)
    {
        $keyFile = $this->getKeyFile($token);
        if(($key =  openssl_pkey_get_private('file://' . $keyFile)) === false)
        {
            throw new HttpException(400, 'Wrong public key.');
        }
        openssl_private_decrypt($this->hex2bin($password), $plainPassword, $key);
        if(empty($plainPassword))
        {
            throw new InvalidArgumentException('Could not decrypt password.');
        }
        return $plainPassword;
    }
    
    public function getSaltAction()
    {
        $username = $this->tools->getParameter('username');
        try {
            $user = $this->container->get('droppy_user.user_manager')->loadUserByUsername($username);
        } catch(UsernameNotFoundException $e) {
            return new Response(sprintf('User %s not found.', $username), 404);
        }
    	return new Response($user->getSalt(), 200, array('Content-Type', 'text/plain'));
    }
    
    protected function hex2bin($data)
    {
        $bin = "";
        $i = 0;
        do {
            $bin .= chr(hexdec($data{$i}.$data{($i + 1)}));
            $i += 2;
        } while($i < strlen($data));
        return $bin;
    }
    
    protected function generateKeys()
    {
        $keyPair = openssl_pkey_new();
        openssl_pkey_export($keyPair, $privateKey);
        $publicKey = openssl_pkey_get_details($keyPair);
        $publicKey = $publicKey["key"];
        return array($publicKey, $privateKey);
    }
    
    protected function checkAndGetDatas()
    {
        $cacheDir = $this->container->getParameter('droppy_user.keys_cache_dir');
        list($token) = array_values($this->tools->getParameters());
        $this->tools->checkOrCreateDirectory($cacheDir);

        $this->checkToken($token, $cacheDir);
        $fileName = $this->getKeyFile($token);
        return array($fileName, $token);
    }
    
    protected function checkToken($token, $cacheDir)
    {
        if(strpos($token, '/') !== false)
        {
            throw new InvalidArgumentException('Token should not contain any / or \.');
        }
        if(file_exists($cacheDir . '/' . $token . '.pem') === true)
        {
            throw new AuthenticationException('This token has already been used.');
        }
    }
    
    protected function getKeyFile($token)
    {
        return $this->container->getParameter('droppy_user.keys_cache_dir') . '/' . $token . '.pem';
    }
    
}