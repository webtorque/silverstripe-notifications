<?php

/**
 * Test registion for Immunoglobin
 */
class NotificationTypeTest extends SapphireTest
{
    protected static $fixture_file = 'NotificationTypeTest.yml';
    protected $usesDatabase = true;

    public function testPermissions()
    {
        $notificationType = NotificationType::get()->first();
        Member::default_admin()->logIn();

        $this->assertFalse($notificationType->canCreate(), 'No one should be able to create new Notification type');
        $this->assertFalse($notificationType->canDelete(), 'No one should be able to delete Notification types');
    }

    public function testBySystemName()
    {
        $this->assertNotNull(
            NotificationType::bySystemName('boom'),
            '`bySystemName` should return a DO when provided a valid system name'
        );
        $this->assertNull(
            NotificationType::bySystemName('unknown'),
            '`bySystemName` should return null when provided a invalid system name'
        );
    }

    public function testDefaultRecords()
    {
        /**
         * @var NotificationType
         */
        $singleton = NotificationType::singleton();
        $singleton->config()->update('default_records', [
            ['SystemName' => 'TestOne', 'Name' => 'Test One'],
            ['SystemName' => 'TestTwo', 'Name' => 'Test Two'],
        ]);
        $singleton->requireDefaultRecords();

        $t1 = NotificationType::bySystemName('TestOne');
        $this->assertNotEmpty(
            $t1,
            'Notification Type with a System name of TestOne should have been created.'
        );
        $this->assertEquals('Test One', $t1->Name, 'TestOne should have been created with a Name of "Test One"');

        $t2 = NotificationType::bySystemName('TestTwo');
        $this->assertNotEmpty(
            $t2,
            'Notification Type with a System name of TestTwo should have been created.'
        );
        $this->assertEquals('Test Two', $t2->Name, 'TestTwo should have been created with a Name of "Test Two"');
    }
}
