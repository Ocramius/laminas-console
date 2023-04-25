<?php

/**
 * @see       https://github.com/laminas/laminas-console for the canonical source repository
 * @copyright https://github.com/laminas/laminas-console/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-console/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Console\Prompt;

use Laminas\Console\Adapter\AbstractAdapter;
use Laminas\Console\Prompt\Password;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests for {@see \Laminas\Console\Prompt\Password}
 *
 * @covers \Laminas\Console\Prompt\Password
 */
class PasswordTest extends TestCase
{
    /** @var AbstractAdapter&MockObject */
    protected AbstractAdapter $adapter;

    public function setUp(): void
    {
        $this->adapter = $this->createMock(AbstractAdapter::class);
    }

    public function testCanPromptPassword()
    {
        $this->adapter->expects(self::once())
            ->method('writeLine')
            ->with('Password: ');
        $this->adapter->expects(self::exactly(4))
            ->method('readChar')
            ->willReturnOnConsecutiveCalls('f', 'o', 'o', PHP_EOL);
        $this->adapter->expects(self::never())
            ->method('write')
            ->with('Password: ');

        $char = new Password('Password: ');

        $char->setConsole($this->adapter);

        $this->assertEquals('foo', $char->show());
    }

    public function testCanPromptPasswordRepeatedly()
    {
        $this->adapter->expects(self::exactly(2))
            ->method('writeLine')
            ->with('New password? ');
        $this->adapter->expects(self::exactly(8))
            ->method('readChar')
            ->willReturnOnConsecutiveCalls('b', 'a', 'r', PHP_EOL, 'b', 'a', 'z', PHP_EOL);
        $this->adapter->expects(self::never())
            ->method('write')
            ->with('Password: ');

        $char = new Password('New password? ');

        $char->setConsole($this->adapter);

        $this->assertEquals('bar', $char->show());
        $this->assertEquals('baz', $char->show());
    }

    public function testProducesStarSymbolOnInput()
    {
        $this->adapter->expects(self::once())
            ->method('writeLine')
            ->with('New password? ');
        $this->adapter->expects(self::exactly(4))
            ->method('readChar')
            ->willReturnOnConsecutiveCalls('t', 'a', 'b', PHP_EOL);
        $this->adapter->expects(self::exactly(3))
            ->method('write')
            ->withConsecutive(['*'], ['**'], ['***']);

        $char = new Password('New password? ', true);

        $char->setConsole($this->adapter);

        $this->assertSame('tab', $char->show());
    }
}
