<?php declare(strict_types = 1);

namespace Salamek\Tempnam\DI;

use Nette\DI\CompilerExtension;
use Salamek\Tempnam\Tempnam;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

/**
 * Class TempnamExtension
 * @package Salamek\Tempnam\DI
 */
class TempnamExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'tempDir' => Expect::string()->required(),
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
