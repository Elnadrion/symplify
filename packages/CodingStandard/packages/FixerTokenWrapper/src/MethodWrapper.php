<?php declare(strict_types=1);

namespace Symplify\CodingStandard\FixerTokenWrapper;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Exception\UnexpectedTokenException;

final class MethodWrapper
{
    /**
     * @var Tokens
     */
    private $tokens;

    private function __construct(Tokens $tokens, int $index)
    {
        $this->ensureIsMethodToken($tokens[$index]);

        $this->tokens = $tokens;

        $this->position = $index;
    }

    public static function createFromTokensAndPosition(Tokens $tokens, int $position): self
    {
        return new self($tokens, $position);
    }

    private function ensureIsMethodToken(Token $token): void
    {
        if ($token->isGivenKind(T_FUNCTION)) {
            return;
        }

        throw new UnexpectedTokenException(sprintf(
            '"%s" expected "%s" token in its constructor. "%s" token given.',
            self::class,
            implode(',', ['T_FUNCTION']),
            $token->getName()
        ));
    }

    /**
     * @return ArgumentWrapper[]
     */
    public function getArguments(): array
    {
        $argumentsBracketStart = $this->tokens->getNextTokenOfKind($this->position, ['(']);
        $argumentsBracketEnd = $this->tokens->getNextTokenOfKind($argumentsBracketStart, [')']);

        if ($argumentsBracketStart === ($argumentsBracketEnd + 1)) {
            return [];
        }

        $arguments = [];
        for ($i = $argumentsBracketStart + 1; $i < $argumentsBracketEnd; $i++) {
            $token = $this->tokens[$i];

            if ($token->isGivenKind(T_VARIABLE) === false) {
                continue;
            }

            $arguments[] = ArgumentWrapper::createFromTokensAndPosition($this->tokens, $i);
        }

        return $arguments;
    }

    public function renameEveryVariableOccurrence(string $oldName, string $newName): void
    {
        $methodBodyStart = $this->tokens->getNextTokenOfKind($this->position, ['{']);
        $methodBodyEnd = $this->tokens->getNextTokenOfKind($this->position, ['}']);

        for ($i = $methodBodyStart + 1; $i < $methodBodyEnd; $i++) {
            $token = $this->tokens[$i];

            if ($token->isGivenKind(T_VARIABLE) === false) {
                continue;
            }

            $argumentWrapper = ArgumentWrapper::createFromTokensAndPosition($this->tokens, $i);

            // local property
            if ($argumentWrapper->getName() === 'this') {
                continue;
            }

            if ($argumentWrapper->getName() === $oldName) {
                $argumentWrapper->changeName($newName);
            }
        }
    }
}
