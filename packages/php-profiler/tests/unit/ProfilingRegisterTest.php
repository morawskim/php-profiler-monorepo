<?php

namespace Mmo\PhpProfiler\Test;

use Mmo\PhpProfiler\ProfilingRegister;
use PHPUnit\Framework\TestCase;

class ProfilingRegisterTest extends TestCase
{
    public function tearDown(): void
    {
        $closure = \Closure::bind(function () {
            ProfilingRegister::$registry = [];
        }, null, ProfilingRegister::class);

        $closure();
    }

    public function testGetOrRegister(): void
    {
        $profiler = ProfilingRegister::getOrRegister('foo', ['bar' => 'baz']);
        $profiler2 = ProfilingRegister::getOrRegister('foo');
        $profiler3 = ProfilingRegister::getOrRegister('foo2');

        $this->assertSame($profiler, $profiler2);
        $this->assertNotSame($profiler3, $profiler2);
    }
}
