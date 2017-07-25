<?php

namespace Salamek\Tempnam;


/**
 * Class Tempnam
 * @package Salamek\Tempnam
 */
class Tempnam extends Object
{
    /** @var string */
    private $tempDir;

    public function __construct($tempDir)
    {
        $this->tempDir = $tempDir;
    }
}
