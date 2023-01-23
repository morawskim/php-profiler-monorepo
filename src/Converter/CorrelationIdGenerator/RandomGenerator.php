<?php

namespace Mmo\PhpProfiler\Converter\CorrelationIdGenerator;

class RandomGenerator implements CorrelationIdGeneratorInterface
{
    public function getCorrelationId(): string
    {
        return bin2hex(random_bytes(16));
    }
}
