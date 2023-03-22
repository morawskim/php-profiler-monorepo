<?php

namespace Mmo\PhpProfiler\Serializer;

use Mmo\PhpProfiler\Dto\Probe;
use Mmo\PhpProfiler\Profiler;

interface SerializerInterface
{
    public function serialize(Profiler $profiler): string;

    public function deserialize(string $serializedString): Probe;
}
