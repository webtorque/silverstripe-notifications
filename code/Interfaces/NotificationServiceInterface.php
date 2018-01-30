<?php

/**
 * Define a Notification Service that can be user to deliver notification to Members.
 */
interface NotificationServiceInterface
{
    /**
     * Sends out notifications through handlers
     *
     * @param string            $type               Type of the notification.
     * @param array             $data               The provider used to parse the content.
     * @param Member|Member[]   $members            The person or persons to sent to.
     * @param mixed             $callToActionURL    Relative or absolute URL to an action specific to the notice.
     * @return NotificationResponseInterface        Information about the delivery of a notifcation.
     * @throws NotificationFailureException         Information about why a notification request has failed.
     */
    public function send(
        $type,
        array $data,
        $members,
        $callToActionURL = false
    );
}
