<?php

namespace Mmo\PhpProfiler;

class ProfilingRegister
{
    /**
     * @var array<string, Profiler>
     */
    private static $registry = [];

    public static function getOrRegister(string $name, array $metadata = []): Profiler
    {
        $key = md5($name);
        if (null === (self::$registry[$key] ?? null)) {
            self::$registry[$key] = new Profiler($name, $metadata);
        }

        return self::$registry[$key];
    }
}
