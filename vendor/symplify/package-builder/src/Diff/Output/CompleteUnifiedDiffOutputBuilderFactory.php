<?php

declare (strict_types=1);
namespace EasyCI202301\Symplify\PackageBuilder\Diff\Output;

use EasyCI202301\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use EasyCI202301\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
/**
 * @api
 * Creates @see UnifiedDiffOutputBuilder with "$contextLines = 1000;"
 */
final class CompleteUnifiedDiffOutputBuilderFactory
{
    /**
     * @var \Symplify\PackageBuilder\Reflection\PrivatesAccessor
     */
    private $privatesAccessor;
    public function __construct(PrivatesAccessor $privatesAccessor)
    {
        $this->privatesAccessor = $privatesAccessor;
    }
    /**
     * @api
     */
    public function create() : UnifiedDiffOutputBuilder
    {
        $unifiedDiffOutputBuilder = new UnifiedDiffOutputBuilder('');
        $this->privatesAccessor->setPrivateProperty($unifiedDiffOutputBuilder, 'contextLines', 10000);
        return $unifiedDiffOutputBuilder;
    }
}
