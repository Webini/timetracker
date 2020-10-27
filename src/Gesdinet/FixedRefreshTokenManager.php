<?php

/**
 * Temporary fix for Gesdinet refresh token bundle
 * cf https://github.com/markitosgv/JWTRefreshTokenBundle/issues/200
 */

namespace App\Gesdinet;

use Doctrine\Persistence\ObjectManager;
use Gesdinet\JWTRefreshTokenBundle\Doctrine\RefreshTokenManager;

/**
 * Class FixedRefreshTokenManager
 * @package App\Gesdinet
 */
class FixedRefreshTokenManager extends RefreshTokenManager
{
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository($class);
        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }
}