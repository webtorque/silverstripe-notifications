<?php

/**
 * Represent a Notification that has been parsed and can be use by a @link(NotificationSenderInterface) to send an
 * actual notification.
 */
interface ParsedNotificationInterface
{

    /**
     * Retrieve a short statement suitable for an Email message subject line.
     * @return string
     */
    public function getSubject();

    /**
     * Retrieve a Short Message suitable for an SMS message.
     * @return string
     */
    public function getShortMessage();

    /**
     * Retrieve an HTML-enriched messages.
     * @return string
     */
    public function getRichMessage();
}
