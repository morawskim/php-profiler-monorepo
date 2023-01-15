<?php

namespace Mmo\PhpProfiler\Serializer;

use Mmo\PhpProfiler\Profiler;

interface SerializerInterface
{
    public function serialize(Profiler $profiler): string;
}
