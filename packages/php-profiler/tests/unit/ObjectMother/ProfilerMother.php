<?php

namespace Mmo\PhpProfiler\Test\ObjectMother;

use Mmo\PhpProfiler\Profiler;

class ProfilerMother
{
    public static function basic(): Profiler
    {
        $profiler = new Profiler("foo", ['tag1' => 'val1', 'tag2' => 'val2']);
        $profiler->start('bar');
        $profiler->stop();
        $profiler->stop();

        return $profiler;
    }

    public static function nested(): Profiler
    {
        $profiler = new Profiler("foo", ['tag1' => 'val1', 'tag2' => 'val2']);
        $profiler->start('bar');
        $profiler->startNested('baz', ['key' => 'value']);
        $profiler->stop();
        $profiler->stop();
        $profiler->stop();

        return $profiler;
    }
}
