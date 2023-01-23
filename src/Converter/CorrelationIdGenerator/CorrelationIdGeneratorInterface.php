<?php

namespace Mmo\PhpProfiler\Converter\CorrelationIdGenerator;

interface CorrelationIdGeneratorInterface
{
    public function getCorrelationId(): string;
}
