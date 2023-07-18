<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202307\Symfony\Component\HttpFoundation\Session;

use EasyCI202307\Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
/**
 * Interface for session with a flashbag.
 */
interface FlashBagAwareSessionInterface extends SessionInterface
{
    public function getFlashBag() : FlashBagInterface;
}
