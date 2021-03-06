<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication/blob/master/LICENSE.md New BSD License
 */
namespace Zend\Expressive\Authentication;

interface UserInterface
{
    /**
     * Get the username
     *
     * @return string
     */
    public function getUsername(): string;

    /**
     * Get the user role
     *
     * @return string
     */
    public function getUserRole(): string;
}
