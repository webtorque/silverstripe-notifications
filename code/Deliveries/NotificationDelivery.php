<?php

class NotificationDelivery implements NotificationDeliveryInterface
{

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $medium;

    /**
     * @param string $state  Should be 'DELIVERED' or 'QUEUED'.
     * @param string $medium Description of the medium that the notification will be/was deliverd with.
     */
    public function __construct($state, $medium)
    {
        $this->state = $state;
        $this->medium = $medium;
    }

    /**
     * State of the delivery
     * @return string Can either be DELIVERED or QUEUED
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Medium use to perform the devlivery. e.g.: SMS, EMAIL
     * @return string
     */
    public function getMedium()
    {
        return $this->medium;
    }
}
