<?php

namespace Mmo\PhpProfilerCli\Converter\CorrelationIdGenerator;

class StaticGenerator implements CorrelationIdGeneratorInterface
{
    /**
     * @var string
     */
    private $correlationId;

    public function __construct(string $correlationId)
    {
        $this->correlationId = $correlationId;
    }

    public function getCorrelationId(): string
    {
        return $this->correlationId;
    }
}
