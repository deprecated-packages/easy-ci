<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202307\Symfony\Component\HttpFoundation\Session\Storage\Proxy;

use EasyCI202307\Symfony\Component\HttpFoundation\Session\Storage\Handler\StrictSessionHandler;
/**
 * @author Drak <drak@zikula.org>
 */
class SessionHandlerProxy extends AbstractProxy implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
    protected $handler;
    public function __construct(\SessionHandlerInterface $handler)
    {
        $this->handler = $handler;
        $this->wrapper = $handler instanceof \SessionHandler;
        $this->saveHandlerName = $this->wrapper || $handler instanceof StrictSessionHandler && $handler->isWrapper() ? \ini_get('session.save_handler') : 'user';
    }
    public function getHandler() : \SessionHandlerInterface
    {
        return $this->handler;
    }
    // \SessionHandlerInterface
    public function open(string $savePath, string $sessionName) : bool
    {
        return $this->handler->open($savePath, $sessionName);
    }
    public function close() : bool
    {
        return $this->handler->close();
    }
    /**
     * @return string|false
     */
    public function read(string $sessionId)
    {
        return $this->handler->read($sessionId);
    }
    public function write(string $sessionId, string $data) : bool
    {
        return $this->handler->write($sessionId, $data);
    }
    public function destroy(string $sessionId) : bool
    {
        return $this->handler->destroy($sessionId);
    }
    /**
     * @return int|false
     */
    public function gc(int $maxlifetime)
    {
        return $this->handler->gc($maxlifetime);
    }
    public function validateId(string $sessionId) : bool
    {
        return !$this->handler instanceof \SessionUpdateTimestampHandlerInterface || $this->handler->validateId($sessionId);
    }
    public function updateTimestamp(string $sessionId, string $data) : bool
    {
        return $this->handler instanceof \SessionUpdateTimestampHandlerInterface ? $this->handler->updateTimestamp($sessionId, $data) : $this->write($sessionId, $data);
    }
}
