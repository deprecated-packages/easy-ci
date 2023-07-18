<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace EasyCI202307\Symfony\Component\VarDumper\Caster;

use EasyCI202307\Symfony\Component\HttpFoundation\Request;
use EasyCI202307\Symfony\Component\Uid\Ulid;
use EasyCI202307\Symfony\Component\Uid\Uuid;
use EasyCI202307\Symfony\Component\VarDumper\Cloner\Stub;
use EasyCI202307\Symfony\Component\VarExporter\Internal\LazyObjectState;
/**
 * @final
 */
class SymfonyCaster
{
    private const REQUEST_GETTERS = ['pathInfo' => 'getPathInfo', 'requestUri' => 'getRequestUri', 'baseUrl' => 'getBaseUrl', 'basePath' => 'getBasePath', 'method' => 'getMethod', 'format' => 'getRequestFormat'];
    /**
     * @return array
     */
    public static function castRequest(Request $request, array $a, Stub $stub, bool $isNested)
    {
        $clone = null;
        foreach (self::REQUEST_GETTERS as $prop => $getter) {
            $key = Caster::PREFIX_PROTECTED . $prop;
            if (\array_key_exists($key, $a) && null === $a[$key]) {
                $clone = $clone ?? clone $request;
                $a[Caster::PREFIX_VIRTUAL . $prop] = $clone->{$getter}();
            }
        }
        return $a;
    }
    /**
     * @return array
     */
    public static function castHttpClient($client, array $a, Stub $stub, bool $isNested)
    {
        $multiKey = \sprintf("\x00%s\x00multi", \get_class($client));
        if (isset($a[$multiKey])) {
            $a[$multiKey] = new CutStub($a[$multiKey]);
        }
        return $a;
    }
    /**
     * @return array
     */
    public static function castHttpClientResponse($response, array $a, Stub $stub, bool $isNested)
    {
        $stub->cut += \count($a);
        $a = [];
        foreach ($response->getInfo() as $k => $v) {
            $a[Caster::PREFIX_VIRTUAL . $k] = $v;
        }
        return $a;
    }
    /**
     * @return array
     */
    public static function castLazyObjectState($state, array $a, Stub $stub, bool $isNested)
    {
        if (!$isNested) {
            return $a;
        }
        $stub->cut += \count($a) - 1;
        $instance = $a['realInstance'] ?? null;
        switch ($a['status']) {
            case LazyObjectState::STATUS_INITIALIZED_FULL:
                $a = 'INITIALIZED_FULL';
                break;
            case LazyObjectState::STATUS_INITIALIZED_PARTIAL:
                $a = 'INITIALIZED_PARTIAL';
                break;
            case LazyObjectState::STATUS_UNINITIALIZED_FULL:
                $a = 'UNINITIALIZED_FULL';
                break;
            case LazyObjectState::STATUS_UNINITIALIZED_PARTIAL:
                $a = 'UNINITIALIZED_PARTIAL';
                break;
        }
        if ($instance) {
            $a['realInstance'] = $instance;
            --$stub->cut;
        }
        return $a;
    }
    /**
     * @return array
     */
    public static function castUuid(Uuid $uuid, array $a, Stub $stub, bool $isNested)
    {
        $a[Caster::PREFIX_VIRTUAL . 'toBase58'] = $uuid->toBase58();
        $a[Caster::PREFIX_VIRTUAL . 'toBase32'] = $uuid->toBase32();
        // symfony/uid >= 5.3
        if (\method_exists($uuid, 'getDateTime')) {
            $a[Caster::PREFIX_VIRTUAL . 'time'] = $uuid->getDateTime()->format('EasyCI202307\\Y-m-d H:i:s.u \\U\\T\\C');
        }
        return $a;
    }
    /**
     * @return array
     */
    public static function castUlid(Ulid $ulid, array $a, Stub $stub, bool $isNested)
    {
        $a[Caster::PREFIX_VIRTUAL . 'toBase58'] = $ulid->toBase58();
        $a[Caster::PREFIX_VIRTUAL . 'toRfc4122'] = $ulid->toRfc4122();
        // symfony/uid >= 5.3
        if (\method_exists($ulid, 'getDateTime')) {
            $a[Caster::PREFIX_VIRTUAL . 'time'] = $ulid->getDateTime()->format('EasyCI202307\\Y-m-d H:i:s.v \\U\\T\\C');
        }
        return $a;
    }
}
