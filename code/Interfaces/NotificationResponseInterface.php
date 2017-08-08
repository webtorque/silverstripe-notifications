<?php

/**
 * Response to a notification sender service request. This give information about the status of the request.
 */
interface NotificationResponseInterface
{
    /**
     * Errors that got triggered while trying to send notification.
     * @return NotificationFailureException[]    List of exceptions caught.
     */
    public function getFailures();


    /**
     * Report on the notification that got successfully delivered.
     * @return NotificationDeliveryInterface[]
     */
    public function getDeliveries();
}
