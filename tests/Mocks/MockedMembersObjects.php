<?php
/**
 * This file contains 3 mocked classes specifically designed to test sending notification to multiple members. The
 * provide simple hooks to test that they have been destined for a specific member.
 */


class MockMemberedNotificationProvider implements NotificationProviderInterface
{
    public function send(ParsedNotificationInterface $notification, Member $member, $callToActionURL=false)
    {
        return new MockMemberedNotificationDelivery($member, $notification->getSubject());
    }
}

class MockMemberedParser implements NotificationParserInterface
{
    public function parse($type, array $data, Member $member, $callToActionURL=false)
    {
        return new MockMemberedParsedNotification($member);
    }
}


class MockMemberedParsedNotification extends MockParsedNotification
{
    private $member;

    public function __construct($member)
    {
        $this->member = $member;
    }

    public function getSubject()
    {
        return 'Mock subject response for ' . $this->member->Name;
    }
}


class MockMemberedNotificationDelivery implements NotificationDeliveryInterface
{
    private $member;
    private $subject;

    public function __construct($member, $subject)
    {
        $this->member = $member;
        $this->subject = $subject;
    }

    public function getState()
    {
        return 'DELIVERED';
    }

    public function getMedium()
    {
        return 'MockeNotifcation';
    }

    public function getMember()
    {
        return $this->member;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function __toString()
    {
        return $this->getSubject();
    }
}
