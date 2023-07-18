<?php

declare (strict_types=1);
namespace EasyCI202307\Symplify\PackageBuilder\DependencyInjection\CompilerPass;

use EasyCI202307\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use EasyCI202307\Symfony\Component\DependencyInjection\ContainerBuilder;
final class AutowireInterfacesCompilerPass implements CompilerPassInterface
{
    /**
     * @var string[]
     * @readonly
     */
    private $typesToAutowire;
    /**
     * @param string[] $typesToAutowire
     */
    public function __construct(array $typesToAutowire)
    {
        $this->typesToAutowire = $typesToAutowire;
    }
    public function process(ContainerBuilder $containerBuilder) : void
    {
        $definitions = $containerBuilder->getDefinitions();
        foreach ($definitions as $definition) {
            foreach ($this->typesToAutowire as $typeToAutowire) {
                if (!\is_a((string) $definition->getClass(), $typeToAutowire, \true)) {
                    continue;
                }
                $definition->setAutowired(\true);
                continue 2;
            }
        }
    }
}
