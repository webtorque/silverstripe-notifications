<?php

/**
 * Manage the delivery of notifications to a specific medium (e.g.: SMS, Email, DataBase, Morse code, Pigeon Delivery
 * service).
 */
interface NotificationProviderInterface
{

    /**
     * Send a notification to this NotificationSender's medium.
     * @param  ParsedNotificationInterface $notification    Notification to send.
     * @param  Member                      $member          Member who will receive the notification.
     * @param  mixed                       $callToActionURL Relative or absolute URL to an action specific to the notice.
     * @throws NotificationFailureException If the delivery of the notification fails.
     * @return NotificationDeliveryInterface
     */
    public function send(
        ParsedNotificationInterface $notification,
        Member $member,
        $callToActionURL = false
    );
}
