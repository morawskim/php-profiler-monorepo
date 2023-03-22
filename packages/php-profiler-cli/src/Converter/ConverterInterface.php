<?php

namespace Mmo\PhpProfilerCli\Converter;

use Mmo\PhpProfilerCli\Reader\ReaderInterface;

interface ConverterInterface
{
    public function convert(ReaderInterface $reader): string;
}
