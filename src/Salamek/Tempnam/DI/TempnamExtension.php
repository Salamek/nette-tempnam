<?php

namespace Salamek\Tempnam\DI;

use Nette;
use Nette\DI\Compiler;
use Nette\DI\Configurator;
use Salamek\Tempnam\Tempnam;

/**
 * Class TempnamExtension
 * @package Salamek\Tempnam\DI
 */
class TempnamExtension extends Nette\DI\CompilerExtension
{
    public $defaults = [
        'tempDir' => '%tempDir%/tempnam'
    ];


    public function loadConfiguration()
    {
        $config = $this->getConfig($this->defaults);
        $builder = $this->getContainerBuilder();

        @mkdir($config['tempDir']); // @ - directory may exists

        $builder->addDefinition($this->prefix('tempnam'))
            ->setFactory(Tempnam::class, [$config['tempDir']]);
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
