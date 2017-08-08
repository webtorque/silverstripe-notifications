play
<?php

class NotificationSmsMessageMediaProviderTest extends SapphireTest
{
    protected $usesDatabase = true;
    protected static $fixture_file = 'NotificationSmsMessageMediaProviderTest.yml';

    public function setUp()
    {
        Member::add_extension('MockMobileMemberExtension');
        return parent::setUp();
    }

    public function testSimpleSend()
    {
        $provider = new NotificationSmsMessageMediaProvider('e2s.pcsms.us', 'messagemedia@example.com');
        $member = $this->objFromFixture('Member', 'tms');
        $parsedNotification = new MockParsedNotification();

        // Simple notification with advanced parsing
        $delivery = $provider->send($parsedNotification, $member, false);
        $this->assertEquals('DELIVERED', $delivery->getState());
        $this->assertEquals('SMS', $delivery->getMedium());
        $this->assertEmailSent(
            '642101234567@e2s.pcsms.us',
            'messagemedia@example.com',
            $parsedNotification->getShortMessage()
        );

        // Downgrade to basic validation
        $provider->setForceBasicPhoneParsing(true);
        $delivery = $provider->send($parsedNotification, $member, false);
        $this->assertEquals('DELIVERED', $delivery->getState());
        $this->assertEquals('SMS', $delivery->getMedium());
        $this->assertEmailSent(
            '642101234567@e2s.pcsms.us',
            'messagemedia@example.com',
            $parsedNotification->getShortMessage()
        );
    }

    public function testSendWithCTA()
    {
        Config::inst()->update('Director', 'alternate_base_url', 'https://example.com/');

        $provider = new NotificationSmsMessageMediaProvider('e2s.pcsms.us', 'messagemedia@example.com');
        $member = $this->objFromFixture('Member', 'tms');
        $parsedNotification = new MockParsedNotification();

        // Relative URL
        $delivery = $provider->send($parsedNotification, $member, 'example.html');
        $this->assertEquals('DELIVERED', $delivery->getState());
        $this->assertEquals('SMS', $delivery->getMedium());
        $this->assertEmailSent(
            '642101234567@e2s.pcsms.us',
            'messagemedia@example.com',
            $parsedNotification->getShortMessage() . ' https://example.com/example.html'
        );

        // Absolute URL
        $delivery = $provider->send($parsedNotification, $member, 'http://example.org/hello.php');
        $this->assertEquals('DELIVERED', $delivery->getState());
        $this->assertEquals('SMS', $delivery->getMedium());
        $this->assertEmailSent(
            '642101234567@e2s.pcsms.us',
            'messagemedia@example.com',
            $parsedNotification->getShortMessage() . ' http://example.org/hello.php'
        );
    }

    public function testAlternativeMobileField()
    {
        $provider = new NotificationSmsMessageMediaProvider(
            'e2s.pcsms.us',
            'messagemedia@example.com',
            'Surname'
        );
        $member = $this->objFromFixture('Member', 'tms');
        $parsedNotification = new MockParsedNotification();

        $delivery = $provider->send($parsedNotification, $member, false);
        $this->assertEquals('DELIVERED', $delivery->getState());
        $this->assertEquals('SMS', $delivery->getMedium());
        $this->assertEmailSent('642876543210@e2s.pcsms.us', 'messagemedia@example.com');
    }

    public function testMissingPhoneNumber()
    {
        $this->setExpectedException(NotificationFailureException::class, 'Invalid phone number field');

        $provider = new NotificationSmsMessageMediaProvider('e2s.pcsms.us', 'messagemedia@example.com');
        $member = $this->objFromFixture('Member', 'noPhone');
        $parsedNotification = new MockParsedNotification();
        $delivery = $provider->send($parsedNotification, $member, false);
    }

    public function testInvalidPhoneNumber()
    {
        $this->setExpectedException(NotificationFailureException::class, 'Could not parse phone number');

        $provider = new NotificationSmsMessageMediaProvider('e2s.pcsms.us', 'messagemedia@example.com');
        $member = $this->objFromFixture('Member', 'badPhone');
        $parsedNotification = new MockParsedNotification();
        $delivery = $provider->send($parsedNotification, $member, false);
    }

    public function testLandLineNumber()
    {
        $this->setExpectedException(NotificationFailureException::class, 'Phone number is not for a mobile');

        $provider = new NotificationSmsMessageMediaProvider('e2s.pcsms.us', 'messagemedia@example.com');
        $member = $this->objFromFixture('Member', 'landLine');
        $parsedNotification = new MockParsedNotification();
        $delivery = $provider->send($parsedNotification, $member, false);
    }
}

class MockMobileMemberExtension extends DataExtension
{
    private static $db = array(
        'MobileNumber' => 'Varchar',
    );
}
