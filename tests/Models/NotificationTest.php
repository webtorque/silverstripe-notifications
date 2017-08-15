<?php

/**
 * Test Notification DO
 */
class NotificationTest extends SapphireTest
{
    protected static $fixture_file = 'NotificationTest.yml';
    protected $usesDatabase = true;

    public function testCanView()
    {
        $tms = $this->objFromFixture('Member', 'tms');


        $unread = $this->objFromFixture('Notification', 'unread');
        $forbidden = $this->objFromFixture('Notification', 'forbidden');

        $this->assertTrue($unread->canView($tms), 'Member should be able to view his notification');
        $this->assertFalse($forbidden->canView($tms), 'Member should not be able to view other users\' notifications');

        $tms->logIn();
        $this->assertTrue($unread->canView(), 'Current Member should be able to view his notification');
        $this->assertFalse($forbidden->canView(), 'Current Member should not be able to view other users\' notifications');

        $admin = Member::default_admin();
        $this->assertTrue($unread->canView($admin), 'Admin can view anyone\'s notification');
        $this->assertTrue($forbidden->canView($admin), 'Admin can view anyone\'s notification');
    }

    public function testCanEdit()
    {
        $tms = $this->objFromFixture('Member', 'tms');


        $unread = $this->objFromFixture('Notification', 'unread');
        $forbidden = $this->objFromFixture('Notification', 'forbidden');

        $this->assertTrue($unread->canEdit($tms), 'Member should be able to edit his notification');
        $this->assertFalse($forbidden->canEdit($tms), 'Member should not be able to edit other users\' notifications');

        $tms->logIn();
        $this->assertTrue($unread->canEdit(), 'Current Member should be able to edit his notification');
        $this->assertFalse($forbidden->canEdit(), 'Current Member should not be able to edit other users\' notifications');

        $admin = Member::default_admin();
        $this->assertTrue($unread->canEdit($admin), 'Admin can edit anyone\'s notification');
        $this->assertTrue($forbidden->canEdit($admin), 'Admin can edit anyone\'s notification');
    }

    public function testCanDelete()
    {
        $tms = $this->objFromFixture('Member', 'tms');

        $unread = $this->objFromFixture('Notification', 'unread');
        $forbidden = $this->objFromFixture('Notification', 'forbidden');

        $this->assertTrue($unread->canDelete($tms), 'Member should be able to delete his notification');
        $this->assertFalse($forbidden->canDelete($tms), 'Member should not be able to delete other users\' notifications');

        $tms->logIn();
        $this->assertTrue($unread->canDelete(), 'Current Member should be able to delete his notification');
        $this->assertFalse($forbidden->canDelete(), 'Current Member should not be able to delete other users\' notifications');

        $admin = Member::default_admin();
        $this->assertTrue($unread->canDelete($admin), 'Admin can delete anyone\'s notification');
        $this->assertTrue($forbidden->canDelete($admin), 'Admin can delete anyone\'s notification');
    }

    public function testCanCreate()
    {
        $tms = $this->objFromFixture('Member', 'tms');

        $unread = $this->objFromFixture('Notification', 'unread');
        $forbidden = $this->objFromFixture('Notification', 'forbidden');

        $this->assertFalse($unread->canCreate($tms), 'Member should not be able to create new notification directly.');

        $tms->logIn();
        $this->assertFalse($unread->canCreate(), 'Current Member should not be able to create new notification directly.');

        $admin = Member::default_admin();
        $this->assertTrue($unread->canCreate($admin), 'Admin can create new notification directly');
    }

    public function testCanMarkAsRead()
    {
        $tms = $this->objFromFixture('Member', 'tms');

        $unread = $this->objFromFixture('Notification', 'unread');
        $forbidden = $this->objFromFixture('Notification', 'forbidden');

        $this->assertTrue($unread->canMarkAsRead($tms), 'Member should be able to mark-has-viewed his notification');
        $this->assertFalse($forbidden->canMarkAsRead($tms), 'Member should not be able to mark-has-viewed other users\' notifications');

        $tms->logIn();
        $this->assertTrue($unread->canMarkAsRead(), 'Current Member should be able to mark-has-viewed his notification');
        $this->assertFalse($forbidden->canMarkAsRead(), 'Current Member should not be able to mark-has-viewed other users\' notifications');

        $admin = Member::default_admin();
        $this->assertFalse($unread->canMarkAsRead($admin), 'Even Admin can not mark-has-viewed anyone\'s notification');
    }

    public function testGetViewed()
    {
        $unread = $this->objFromFixture('Notification', 'unread');
        $read = $this->objFromFixture('Notification', 'read');

        $this->assertTrue($read->Viewed, 'Notification with a ViewedOn Date should be Viewed.');
        $this->assertFalse($unread->Viewed, 'Notification without a ViewedOn Date should not be Viewed.');

        $unread->ViewedOn = SS_DateTime::now()->getValue();
        $read->ViewedOn = null;

        $this->assertFalse($read->Viewed, 'Unsetting a ViewedOn date on a Notification should make it unviewed.');
        $this->assertTrue($unread->Viewed, 'Setting a ViewedOn date notification should make it Viewed.');
    }

    public function testSetViewed()
    {
        $unread = $this->objFromFixture('Notification', 'unread');
        $read = $this->objFromFixture('Notification', 'read');

        $unread->Viewed = true;
        $read->Viewed = false;

        $this->assertNull($read->ViewedOn, 'Unviewed Notification should havea viewed on date of null');
        $this->assertEquals(SS_DateTime::now()->getValue(), $unread->ViewedOn, 'Setting a Notification\'s viewed attribute to true, should set the ViewedOn attribute to the current date.');

        $unread->write();
        $read->write();

        $unread = Notification::get()->byID($unread->ID);
        $read = Notification::get()->byID($read->ID);

        $this->assertFalse($read->Viewed, 'Unsetting and writing a Notification\'s Viewed attribute should make it permanently unviewed');
        $this->assertTrue($unread->Viewed, 'Setting and writing a Notification\'s Viewed attribute should make it permanently viewed');
    }
}
