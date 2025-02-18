<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\Filter\Fixture;

use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\Tests\Filter\Source\SkippedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class SomeSkippedFixer implements DocumentedRuleInterface, SkippedRuleInterface
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Some description', [
            new CodeSample(
                <<<'CODE_SAMPLE'
bad code
CODE_SAMPLE
,
                <<<'CODE_SAMPLE'
good code
CODE_SAMPLE
            )
        ]);
    }
}
