<?php declare(strict_types=1);

namespace WyriHaximus\Tests\React\Cache;

use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use React\Cache\CacheInterface;
use WyriHaximus\AsyncTestUtilities\AsyncTestCase;
use WyriHaximus\React\Cache\ReadWrite;
use WyriHaximus\React\Cache\Swallow;

/**
 * @internal
 */
final class SwallowTest extends AsyncTestCase
{
    /**
     * @test
     */
    public function swallow(): void
    {
        self::expectOutputString('Exception: Doh!');

        $key = 'sleutel';
        $default = 'standaard';

        $exception = new \Exception('Doh!');

        $cache = $this->prophesize(CacheInterface::class);
        $cache->get($key, $default)->shouldBeCalled()->willThrow($exception);

        $logger = $this->prophesize(LoggerInterface::class);
        $logger->log(Argument::type('string'), Argument::type('string'), Argument::type('array'))->shouldBeCalled();

        $swallow = new Swallow($cache->reveal(), $logger->reveal());
        $swallow->get($key, $default);
    }
}
