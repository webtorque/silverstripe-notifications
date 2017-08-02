<?php

/**
 * Test registion for Immunoglobin
 */
class NotificationTypeTest extends SapphireTest
{
    protected static $fixture_file = 'notifications/tests/Models/NotificationTypeTest.yml';
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
}
