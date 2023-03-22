<?php

namespace Mmo\PhpProfilerCli\Reader;

interface ReaderInterface
{
    public function readProfilerData(): iterable;
}
