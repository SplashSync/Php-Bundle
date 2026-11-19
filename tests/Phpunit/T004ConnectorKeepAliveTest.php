<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Bundle\Tests\Phpunit;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use ReflectionProperty;
use Splash\Bundle\Connectors\NullConnector;
use Splash\Bundle\Events\ConnectorKeepAliveEvent;
use Splash\Bundle\Models\AbstractConnector;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Test of the Connector Keep-Alive Helper
 */
class T004ConnectorKeepAliveTest extends TestCase
{
    /**
     * Webservice ID of the connector under test
     */
    const WEBSERVICE_ID = "ThisIsKeepAliveWsId";

    /**
     * The event carries the webservice id and the message.
     */
    public function testEventCarriesWebserviceIdAndMessage(): void
    {
        $dispatched = array();
        $connector = $this->getConnector($dispatched);

        $connector->keepAlive("Import page 12/40");

        Assert::assertCount(1, $dispatched);
        $event = $this->eventAt($dispatched, 0);
        Assert::assertSame(self::WEBSERVICE_ID, $event->getWebserviceId());
        Assert::assertSame("Import page 12/40", $event->getMessage());
    }

    /**
     * The message is optional.
     */
    public function testEventWithoutMessage(): void
    {
        $dispatched = array();
        $connector = $this->getConnector($dispatched);

        $connector->keepAlive();

        Assert::assertCount(1, $dispatched);
        Assert::assertNull($this->eventAt($dispatched, 0)->getMessage());
    }

    /**
     * Two calls within the interval dispatch one event, the first one.
     */
    public function testCallsAreThrottled(): void
    {
        $dispatched = array();
        $connector = $this->getConnector($dispatched);

        $connector->keepAlive("First");
        $connector->keepAlive("Second");
        $connector->keepAlive("Third");

        Assert::assertCount(1, $dispatched);
        Assert::assertSame("First", $this->eventAt($dispatched, 0)->getMessage());
    }

    /**
     * Once the interval is elapsed, the next call dispatches again.
     */
    public function testDispatchResumesAfterInterval(): void
    {
        $dispatched = array();
        $connector = $this->getConnector($dispatched);

        $connector->keepAlive("First");
        $connector->keepAlive("Dropped");
        $this->rewindLastKeepAlive($connector, ConnectorKeepAliveEvent::INTERVAL + 1);
        $connector->keepAlive("Second");
        $connector->keepAlive("Dropped");

        Assert::assertCount(2, $dispatched);
        Assert::assertSame("First", $this->eventAt($dispatched, 0)->getMessage());
        Assert::assertSame("Second", $this->eventAt($dispatched, 1)->getMessage());
    }

    /**
     * Build a configured connector whose dispatcher collects the events.
     *
     * @param array<int, object> $dispatched Collected events, by reference
     */
    private function getConnector(array &$dispatched): AbstractConnector
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher
            ->method("dispatch")
            ->willReturnCallback(function (object $event) use (&$dispatched): object {
                $dispatched[] = $event;

                return $event;
            })
        ;
        $connector = new NullConnector($dispatcher, new NullLogger());
        $connector->configure(NullConnector::NAME, self::WEBSERVICE_ID, array());

        return $connector;
    }

    /**
     * Get a collected event, checked as a keep-alive event.
     *
     * @param array<int, object> $dispatched Collected events
     */
    private function eventAt(array $dispatched, int $index): ConnectorKeepAliveEvent
    {
        Assert::assertArrayHasKey($index, $dispatched);
        Assert::assertInstanceOf(ConnectorKeepAliveEvent::class, $dispatched[$index]);

        return $dispatched[$index];
    }

    /**
     * Move the last keep-alive timestamp back in time.
     */
    private function rewindLastKeepAlive(AbstractConnector $connector, int $seconds): void
    {
        $property = new ReflectionProperty(AbstractConnector::class, "lastKeepAlive");
        $property->setAccessible(true);
        $property->setValue($connector, microtime(true) - $seconds);
    }
}
