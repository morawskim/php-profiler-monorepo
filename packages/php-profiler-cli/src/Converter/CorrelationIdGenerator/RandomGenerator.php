<?php

namespace Mmo\PhpProfilerCli\Converter\CorrelationIdGenerator;

class RandomGenerator implements CorrelationIdGeneratorInterface
{
    public function getCorrelationId(): string
    {
        return bin2hex(random_bytes(16));
    }
}
