<?php

namespace Mmo\PhpProfiler\Dto;

class Probe
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $duration;

    /**
     * @var array<string, string>
     */
    private $metadata;

    /**
     * @var Probe[]
     */
    private $children;

    public function __construct(string $name, $duration, array $metadata = [], array $children = [])
    {
        $this->name = $name;
        $this->duration = $duration;
        $this->metadata = $metadata;
        $this->children = $children;
    }

    public function addChild(Probe $probe): void
    {
        $this->children[] = $probe;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    /**
     * @return string[]
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @return Probe[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
