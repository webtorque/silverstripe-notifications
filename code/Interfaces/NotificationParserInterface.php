<?php

/**
 * Define the signature for a class capable of parsing a notification type into message. The class will receive
 * information about the type of notification that will be sent, some data to inject into the notifiation and the
 * member for which the notification is destined.
 */
interface NotificationParserInterface
{

    /**
     * Parse a notification from the given data.
     * @param   string $type            System name of the notification type to parse (e.g.: participant_approval). Will
     *                                  determine what template will be used.
     * @param   array  $data            Abritary data to inject into template.
     * @param   Member $member          Member who will receive the notification. This information will be injected in the
     *                                  template.
     * @param   mixed  $callToActionURL Relative or absolute URL to an action specific to the notice.
     * @return  ParsedNotificationInterface
     * @throws  NotificationFailureException    If the parsing fails for whatever reason.
     */
    public function parse(
        $type,
        array $data,
        Member $member,
        $callToActionURL = false
    );
}
