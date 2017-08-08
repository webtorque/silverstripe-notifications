<?php

/**
 * Container for response to calling the send method on a Notification Service. Contains a list of successful and failed
 * deliveries.
 */
class NotificationResponse implements NotificationResponseInterface
{
    /**
     * @var NotificationFailureException[]
     */
    private $failures = [];

    /**
     * @var NotificationDeliveryInterface[]
     */
    private $deliveries = [];

    /**
     * Add a notification failure to this notification response.
     * @param   NotificationFailureException $exception Exception thrown when NotificationPrtovider failes to send
     *                                                  a notification.
     * @return  self
     */
    public function addFailure(NotificationFailureException $exception)
    {
        $this->failures[] = $exception;
        return $this;
    }

    /**
     * Add a notification delivery success to this notifcation response.
     * @param   NotificationDeliveryInterface $delivery Information about the delivery of a notification sent
     *                                                  by a Notifcation Provider.
     * @return  self
     */
    public function addDelivery(NotificationDeliveryInterface $delivery)
    {
        $this->deliveries[] = $delivery;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return  NotificationFailureException
     */
    public function getFailures()
    {
        return $this->failures;
    }

    /**
     * {@inheritDoc}
     * @return NotificationDeliveryInterface[]
     */
    public function getDeliveries()
    {
        return $this->deliveries;
    }
}
