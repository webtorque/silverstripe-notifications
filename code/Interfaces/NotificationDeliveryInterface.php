<?php

/**
 * Details information about the delivery of a Notification. Originally returned by a Notification Sender.
 */
interface NotificationDeliveryInterface
{
    /**
     * State of the delivery
     * @return string Can either be DELIVERED or QUEUED
     */
    public function getState();

    /**
     * Medium use to perform the devlivery. e.g.: SMS, EMAIL
     * @return string
     */
    public function getMedium();
}
