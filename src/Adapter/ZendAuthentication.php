<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-cache/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authentication\Adapter;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Expressive\Authentication\AuthenticationInterface;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authentication\UserRepository\UserTrait;

class ZendAuthentication implements AuthenticationInterface
{
    use UserTrait;

    /**
     * @var AuthenticationService
     */
    protected $auth;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var ResponseInterface
     */
    protected $responsePrototype;

    /**
     * Constructor
     *
     * @param AuthenticationService $auth
     * @param array $config
     * @param ResponseInterface $responsePrototype
     */
    public function __construct(
        AuthenticationService $auth,
        array $config,
        ResponseInterface $responsePrototype
    ) {
        $this->auth = $auth;
        $this->config = $config;
        $this->responsePrototype = $responsePrototype;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(ServerRequestInterface $request): ?UserInterface
    {
        if ('POST' === $request->getMethod()) {
            $params = $request->getParsedBody();
            $username = $this->config['username'] ?? 'username';
            $password = $this->config['password'] ?? 'password';
            if (!isset($params[$username]) || !isset($params[$password])) {
                return null;
            }
            $this->auth->getAdapter()->setIdentity($params[$username]);
            $this->auth->getAdapter()->setCredential($params[$password]);

            $result = $this->auth->authenticate();
            if (! $result->isValid()) {
                return null;
            }
            // @todo the role is missing
            return $this->generateUser($result->getIdentity(), '');
        }
        return $this->auth->hasIdentity() ?
               $this->generateUser($this->auth->getIdentity(), '') :
               null;
    }

    /**
     * {@inheritDoc}
     */
    public function unauthorizedResponse(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responsePrototype->withHeader(
            'Location',
            $this->config['redirect']
        )->withStatus(301);
    }
}