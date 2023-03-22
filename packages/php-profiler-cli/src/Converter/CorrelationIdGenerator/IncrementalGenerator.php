<?php

namespace Mmo\PhpProfilerCli\Converter\CorrelationIdGenerator;

class IncrementalGenerator implements CorrelationIdGeneratorInterface
{
    /**
     * @var int
     */
    private $currentValue;

    public function __construct(int $startFrom = 1)
    {
        $this->currentValue = $startFrom;
    }

    public function getCorrelationId(): string
    {
        $correlationId = $this->currentValue;
        $this->currentValue += 1;

        return $correlationId;
    }
}
