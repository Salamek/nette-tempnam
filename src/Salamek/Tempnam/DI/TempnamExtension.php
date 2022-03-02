<?php declare(strict_types = 1);

namespace Salamek\Tempnam\DI;

use Nette\DI\CompilerExtension;
use Salamek\Tempnam\Tempnam;

/**
 * Class TempnamExtension
 * @package Salamek\Tempnam\DI
 */
class TempnamExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'tempDir' => Expect::string()->required()->default('%tempDir%/tempnam'),
        ]);
    }


    public function loadConfiguration(): void
    {
        $config = (array) $this->getConfig();
        $builder = $this->getContainerBuilder();

        @mkdir($config['tempDir']); // @ - directory may exists

        $builder->addDefinition($this->prefix('tempnam'))
            ->setFactory(Tempnam::class, [$config['tempDir']]);
    }
}
