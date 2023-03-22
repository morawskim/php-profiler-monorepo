<?php

namespace Mmo\PhpProfilerCli\Converter\CorrelationIdGenerator;

interface CorrelationIdGeneratorInterface
{
    public function getCorrelationId(): string;
}
