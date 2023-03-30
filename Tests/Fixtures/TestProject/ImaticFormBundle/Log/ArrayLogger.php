<?php declare(strict_types=1);
namespace Imatic\Bundle\FormBundle\Tests\Fixtures\TestProject\ImaticFormBundle\Log;

use Psr\Log\AbstractLogger;

class ArrayLogger extends AbstractLogger
{
    private $buffer = [];

    public function getBuffer(): array
    {
        return $this->buffer;
    }

    public function log($level, $message, array $context = []): void
    {
        $this->buffer = \array_merge(
            $this->buffer,
            [
                ['level' => $level, 'message' => $message, 'context' => $context],
            ]
        );
    }
}
