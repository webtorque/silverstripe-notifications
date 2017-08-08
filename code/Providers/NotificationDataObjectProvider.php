<?php

/**
 * Notification provider used to save notification to the Database.
 */
class NotificationDataObjectProvider implements NotificationProviderInterface
{
    /**
     * Save a notification to the database and display it to the user next time they access the website.
     * @param  ParsedNotificationInterface $notification    Message to send.
     * @param  Member                      $member          User who will receive the message.
     * @param  mixed                       $callToActionURL Relative or absolute URL to an action specific to the notice.
     * @return NotificationDataObjectDelivery   Results of the send action
     */
    public function send(ParsedNotificationInterface $notification, Member $member, $callToActionURL = false)
    {
        $do = new Notification();
        try {
            $do->Subject = $notification->getSubject();
            $do->ShortMessage = $notification->getShortMessage();
            $do->RichMessage = $notification->getRichMessage();
            $do->MemberID = $member->ID;
            $do->CallToActionURL = $callToActionURL ?: '';

            $do->write();
        } catch (Exception $ex) {
            $failure = new NotificationFailureException('Could not save notification.');
            throw $failure->setMember($member)->setPrevious($ex);
        }

        return new NotificationDataObjectDelivery($do);
    }
}
