<?php

/**
 * Test registion for Immunoglobin
 */
class NotificationParserTest extends SapphireTest
{
    protected static $fixture_file = 'notifications/tests/NotificationParserTest.yml';
    protected $usesDatabase = true;

    public function testSucessParse()
    {
        $parser = new NotificationParser();
        $response = $parser->parse('boom', ['hello' => 'world'], $this->objFromFixture('Member', 'tms'));
        $this->assertInstanceOf(ParsedNotificationInterface::class, $response);
    }

    public function testFailedParse()
    {
        $this->setExpectedException(NotificationFailureException::class);
        $parser = new NotificationParser();
        $response = $parser->parse('noBoom', ['hello' => 'world'], $this->objFromFixture('Member', 'tms'));
    }
}
