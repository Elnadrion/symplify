<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNestedCallInAssertMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenNestedCallInAssertMethodCallRule;

/**
 * @extends RuleTestCase<ForbiddenNestedCallInAssertMethodCallRule>
 */
final class ForbiddenNestedCallInAssertMethodCallRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipAssertNothing.php', []];
        yield [__DIR__ . '/Fixture/SkipCleanAssert.php', []];
        yield [__DIR__ . '/Fixture/SkipSimpleGetter.php', []];
        yield [__DIR__ . '/Fixture/SkipAssertTrue.php', []];

        yield [__DIR__ . '/Fixture/NestedAssertMethodCall.php', [
            [ForbiddenNestedCallInAssertMethodCallRule::ERROR_MESSAGE, 14],
        ]];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(ForbiddenNestedCallInAssertMethodCallRule::class);
    }
}
