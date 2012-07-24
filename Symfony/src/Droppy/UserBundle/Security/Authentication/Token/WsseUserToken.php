<?php

namespace Droppy\UserBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class WsseUserToken extends AbstractToken
{
    protected $created;
    protected $digest;
    protected $nonce;

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getDigest()
    {
        return $this->digest;
    }

    public function setDigest($digest)
    {
        $this->digest = $digest;
    }

    public function getNonce()
    {
        return $this->nonce;
    }

    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
    }

    public function getCredentials()
    {
        return '';
    }
}
