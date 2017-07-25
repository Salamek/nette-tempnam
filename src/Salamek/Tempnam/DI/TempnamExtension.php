<?php

namespace Salamek\Tempnam\DI;

use Nette;
use Nette\DI\Compiler;
use Nette\DI\Configurator;

/**
 * Class TempnamExtension
 * @package Salamek\Tempnam\DI
 */
class TempnamExtension extends Nette\DI\CompilerExtension
{

    public function loadConfiguration()
    {
        $config = $this->getConfig();
        $builder = $this->getContainerBuilder();


        $builder->addDefinition($this->prefix('tempnam'))
            ->setClass('Salamek\Tempnam\Tempnam', ['%tempDir%/']);
    }

    /**
     * @param Configurator $config
     * @param string $extensionName
     */
    public static function register(Configurator $config, $extensionName = 'tempnamExtension')
    {
        $config->onCompile[] = function (Configurator $config, Compiler $compiler) use ($extensionName) {
            $compiler->addExtension($extensionName, new TempnamExtension());
        };
    }
}
