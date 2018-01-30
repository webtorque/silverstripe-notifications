<?php

/**
 * Test registion for Immunoglobin
 */
class NotificationServiceTest extends SapphireTest
{
    protected static $fixture_file = 'NotificationServiceTest.yml';
    protected $usesDatabase = true;

    /**************************************************************
     *   TESTING LOGIC DEFINE IN `NotificationServiceInterface`   *
     **************************************************************/

    public function testSucessSend()
    {
        $service = new NotificationService(new MockParser(), [new MockNotificationProvider()]);
        $result = $service->send('mock', ["foo"=>"bar"], $this->objFromFixture('Member', 'tms'));

        $this->assertInstanceOf(NotificationResponseInterface::class, $result);
        $this->assertEmpty($result->getFailures());
        $this->assertNotEmpty($result->getDeliveries());
    }

    public function testSucessSendToManyMembers()
    {
        $service = new NotificationService(new MockMemberedParser(), [new MockMemberedNotificationProvider()]);
        $members = Member::get();

        $result = $service->send('mock', ["foo"=>"bar"], $members);

        $this->assertInstanceOf(NotificationResponseInterface::class, $result);

        // Make sure we got the right number of deliveries
        $this->assertEmpty($result->getFailures());
        $this->assertCount($members->Count(), $result->getDeliveries());

        // Make sure each Member of our member got a personalise notification
        $deliveries = $result->getDeliveries();
        foreach ($members as $i => $member) {
            $delivery = $deliveries[$i];
            $this->assertEquals($delivery->getMember(), $member);
            $this->assertEquals($delivery->getSubject(), 'Mock subject response for ' . $member->Name);
        }
    }

    public function testBadParser()
    {
        $this->setExpectedException(NotificationFailureException::class);
        $service = new NotificationService(new MockBadParser(), [new MockNotificationProvider()]);
        $result = $service->send('mock', ["foo"=>"bar"], $this->objFromFixture('Member', 'tms'));
    }

    public function testBadProvider()
    {
        $service = new NotificationService(new MockParser(), [new MockBadNotificationProvider()]);
        $result = $service->send('mock', ["foo"=>"bar"], $this->objFromFixture('Member', 'tms'));

        $this->assertInstanceOf(NotificationResponseInterface::class, $result);
        $this->assertNotEmpty($result->getFailures());
        $this->assertEmpty($result->getDeliveries());
    }

    public function testProviderMix()
    {
        $service = new NotificationService(new MockParser(), [
            new MockBadNotificationProvider(),
            new MockNotificationProvider(),
        ]);
        $result = $service->send('mock', ["foo"=>"bar"], $this->objFromFixture('Member', 'tms'));

        $this->assertInstanceOf(NotificationResponseInterface::class, $result);
        $this->assertNotEmpty($result->getFailures());
        $this->assertNotEmpty($result->getDeliveries());
        $this->assertEquals(1, sizeof($result->getFailures()));
        $this->assertEquals(1, sizeof($result->getDeliveries()));
    }

    /**************************************************************
     * TESTING LOGIC DEFINE IN `NotificationAjaxServiceInterface` *
     **************************************************************/

    public function testUnreadCountForCurrentMember()
    {
        $service = new NotificationService(new MockParser(), []);

        /**
         * Get a reference to TMS and log them in.
         * @var Member
         */
        $tms = $this->objFromFixture('Member', 'tms');
        $tms->logIn();

        // Test for our TMS
        $count = $service->getUnreadNotificationCountForMember();
        $this->assertEquals(1, $count);

        // Test for a user explicitely asking for themself
        $count = $service->getUnreadNotificationCountForMember($tms);
        $this->assertEquals(1, $count);

        // Test for a user explicitely asking for themself by ID
        $count = $service->getUnreadNotificationCountForMember($tms->ID);
        $this->assertEquals(1, $count);

        // Test for admin who shouldn't have any notification
        Member::default_admin()->logIn();
        $count = $service->getUnreadNotificationCountForMember();
        $this->assertEquals(0, $count);
    }

    public function testUnreadCountForUnknowUser()
    {
        $this->objFromFixture('Member', 'tms')->logIn();
        $this->setExpectedException(ValidationException::class);
        $service = new NotificationService(new MockParser(), []);
        $count = $service->getUnreadNotificationCountForMember(9999);
    }

    public function testUnreadCountForForbiddenUser()
    {
        $this->setExpectedException(PermissionFailureException::class);

        $this->objFromFixture('Member', 'tms')->logIn();
        $other = $this->objFromFixture('Member', 'other');

        $service = new NotificationService(new MockParser(), []);
        $count = $service->getUnreadNotificationCountForMember($other);
    }

    public function testUnreadCountForViewableUser()
    {
        Member::default_admin()->logIn();
        $service = new NotificationService(new MockParser(), []);
        $tms = $this->objFromFixture('Member', 'tms');
        $count = $service->getUnreadNotificationCountForMember($tms);
        $this->assertEquals(1, $count);
    }

    public function testUnreadListForMember()
    {
        $service = new NotificationService(new MockParser(), []);

        /**
         * Get a reference to TMS and log them in.
         * @var Member
         */
        $tms = $this->objFromFixture('Member', 'tms');
        $tms->logIn();

        // Test for our TMS
        $notifications = $service->getUnreadNotificationsForMember();
        $this->assertCount(1, $notifications);
        foreach ($notifications as $notification) {
            $this->assertInstanceOf(Notification::class, $notification);
            $this->assertEquals($tms->ID, $notification->MemberID);
        }

        // Test for admin who shouldn't have any notification
        Member::default_admin()->logIn();
        $notifications = $service->getUnreadNotificationsForMember();
        $this->assertCount(0, $notifications, 'Admin should not have any notifications');

        // Test for admin whose trying to see notification for TMS
        $notifications = $service->getUnreadNotificationsForMember($tms);
        $this->assertCount(1, $notifications, 'TMS should have one unraed notification when counted by Admin.');
        foreach ($notifications as $notification) {
            $this->assertInstanceOf(Notification::class, $notification);
            $this->assertEquals($tms->ID, $notification->MemberID);
        }
    }

    public function testUnreadListForUnknowMember()
    {
        $this->objFromFixture('Member', 'tms')->logIn();
        $this->setExpectedException(ValidationException::class);
        $service = new NotificationService(new MockParser(), []);
        $service->getUnreadNotificationsForMember(9999);
    }

    public function testUnreadListForForbiddenUser()
    {
        $this->setExpectedException(PermissionFailureException::class);

        $this->objFromFixture('Member', 'tms')->logIn();
        $other = $this->objFromFixture('Member', 'other');

        $service = new NotificationService(new MockParser(), []);
        $service->getUnreadNotificationsForMember($other);
    }

    public function testMarkNotificationAsRead()
    {
        $this->objFromFixture('Member', 'tms')->logIn();
        $id = $this->objFromFixture('Notification', 'unread')->ID;

        $service = new NotificationService(new MockParser(), []);
        $service->markNotificationAsRead($id);

        $viewed = Notification::get()->byID($id)->Viewed;

        $this->assertTrue($viewed == true, 'Unread notification should be viewed after being marked as viewed.');
    }

    public function testMarkNotificationAsReadTwice()
    {
        $this->setExpectedException(ValidationException::class);
        $this->objFromFixture('Member', 'tms')->logIn();
        $id = $this->objFromFixture('Notification', 'read')->ID;

        $service = new NotificationService(new MockParser(), []);
        $service->markNotificationAsRead($id);
    }

    public function testMarkUnknowNotificationAsRead()
    {
        $this->setExpectedException(ValidationException::class);
        $this->objFromFixture('Member', 'tms')->logIn();
        $id = 9999;

        $service = new NotificationService(new MockParser(), []);
        $service->markNotificationAsRead($id);
    }

    public function testMarkNotificationAsReadForDifferentUser()
    {
        $this->setExpectedException(PermissionFailureException::class);
        $this->objFromFixture('Member', 'tms')->logIn();
        $id = $this->objFromFixture('Notification', 'forbidden')->ID;

        $service = new NotificationService(new MockParser(), []);
        $service->markNotificationAsRead($id);
    }

    public function testMarkNotificationAsReadByAdmin()
    {
        $this->setExpectedException(PermissionFailureException::class);
        Member::default_admin()->logIn();
        $id = $this->objFromFixture('Notification', 'forbidden')->ID;

        $service = new NotificationService(new MockParser(), []);
        $service->markNotificationAsRead($id);
    }

    public function markAllNotificationAsReadForMember()
    {
        $this->objFromFixture('Member', 'tms')->logIn();
        $other = $this->objFromFixture('Member', 'other');
        $service = new NotificationService(new MockParser(), []);
        $service->markAllNotificationAsReadForMember();

        // Make sure all our notification have been mark as read.
        $this->assertEquals(0, $service->getUnreadNotificationCountForMember());

        // Make sure we didn't touch the other notification.
        $this->assertGreaterThan(0, $service->getUnreadNotificationCountForMember($other));
    }

    public function markAllNotificationAsReadForDifferentMember()
    {
        $this->setExpectedException(PermissionFailureException::class);

        $tms = $this->objFromFixture('Member', 'tms');
        Member::default_admin()->logIn();
        $service = new NotificationService(new MockParser(), []);
        $service->markAllNotificationAsReadForMember($tms);
    }

    public function markAllNotificationAsReadForUnknownMember()
    {
        $this->setExpectedException(ValidationException::class);

        $this->objFromFixture('Member', 'tms')->logIn();

        $service = new NotificationService(new MockParser(), []);
        $service->markAllNotificationAsReadForMember(9999);
    }
}

class MockParser implements NotificationParserInterface
{
    public function parse($type, array $data, Member $member, $callToActionURL=false)
    {
        return new MockParsedNotification();
    }
}

class MockBadParser implements NotificationParserInterface
{
    public function parse($type, array $data, Member $member, $callToActionURL=false)
    {
        throw new NotificationFailureException();
    }
}

class MockNotificationProvider implements NotificationProviderInterface
{
    public function send(ParsedNotificationInterface $notification, Member $member, $callToActionURL=false)
    {
        return new MockNotificationDelivery();
    }
}

class MockBadNotificationProvider implements NotificationProviderInterface
{
    public function send(ParsedNotificationInterface $notification, Member $member, $callToActionURL=false)
    {
        throw new NotificationFailureException();
    }
}

class MockNotificationDelivery implements NotificationDeliveryInterface
{
    public function getState()
    {
        return 'DELIVERED';
    }

    public function getMedium()
    {
        return 'MockeNotifcation';
    }
}
