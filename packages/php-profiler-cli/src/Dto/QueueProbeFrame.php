<?php

namespace Mmo\PhpProfilerCli\Dto;

class QueueProbeFrame {
    public $time;
    public $calledFunctionNamesStack;
    public $parentsFrame = [];

    public function __construct(float $time, array $functionNamesArray)
    {
        $this->time = $time;
        $this->calledFunctionNamesStack = $functionNamesArray;
    }

    /**
     * @param QueueProbeFrame[] $parentsFrame
     */
    public function setParentProbes(array $parentsFrame): void
    {
        $this->parentsFrame = $parentsFrame;
    }
}
