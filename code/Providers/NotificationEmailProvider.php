<?php

/**
 * Notification provider used to send an email notification to a member.
 */
class NotificationEmailProvider implements NotificationProviderInterface
{
    /**
     * @inheritDoc
     * @param  ParsedNotificationInterface $notification    Notification to send.
     * @param  Member                      $member          User meant to receive the message.
     * @param  mixed                       $callToActionURL Relative or absolute URL to an action specific to the notice.
     * @return NotificationDelivery
     */
    public function send(ParsedNotificationInterface $notification, Member $member, $callToActionURL = false)
    {
        $email = new Email();
        try {
            $response = $email
                ->setTo($member->Email)
                ->setSubject($notification->getSubject())
                ->setBody($notification->getRichMessage())
                ->send();
        } catch (Exception $ex) {
            $failure = new NotificationFailureException('Notification Email delivery failed.');
            throw $failure->setMember($member)->setPrevious($ex);
        }

        return new NotificationDelivery('DELIVERED', 'EMAIL');
    }
}
