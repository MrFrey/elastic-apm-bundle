<?php

namespace Goksagun\ElasticApmBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

interface TokenStorageAwareInterface
{
    public function setTokenStorage(TokenStorageInterface $tokenStorage);
}