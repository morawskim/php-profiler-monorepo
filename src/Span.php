<?php

namespace Mmo\PhpProfiler;

class Span
{
    /**
     * @var float
     */
    private $start;

    /**
     * @var float
     */
    private $duration;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $metadata;

    public function __construct(string $name, array $metadata = [])
    {
        $this->start = microtime(true);
        $this->name = $name;
        $this->metadata = $metadata;
    }

    public function stop(): void
    {
        $stop = microtime(true);
        $this->duration = round((($stop - $this->start) * 1000), 4);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @return float in milliseconds
     */
    public function getDuration(): float
    {
        if (null === $this->duration) {
            throw new \BadMethodCallException('Cannot get duration on no stopped span');
        }

        return $this->duration;
    }
}
