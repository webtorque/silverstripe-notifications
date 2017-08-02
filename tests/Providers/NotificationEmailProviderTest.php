<?php

/**
 * Test registion for Immunoglobin
 */
class NotificationEmailProviderTest extends SapphireTest
{
    protected $usesDatabase = true;
    protected static $fixture_file = 'notifications/tests/NotificationParserTest.yml';

    public function testSend()
    {
        $provider = new NotificationEmailProvider();
        $provider->send(new MockParsedNotification(), $this->objFromFixture('Member', 'tms'));

        $this->assertEmailSent(
            'tms@nzblood.co.nz',
            null,
            'Mock subject response',
            '/Mock rich message response/'
        );
    }

    public function testFailedParse()
    {
        $this->setExpectedException(NotificationFailureException::class);

        $provider = new NotificationEmailProvider();
        $provider->send(new MockBadParsedNotification(), $this->objFromFixture('Member', 'tms'));
    }
}
