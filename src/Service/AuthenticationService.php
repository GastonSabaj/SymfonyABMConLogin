<?php

namespace App\Service;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthenticationService
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function checkAuthentication(): bool
    {
        return $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY');
    }
}