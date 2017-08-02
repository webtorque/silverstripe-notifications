<?php

class NotificationParser implements NotificationParserInterface
{

    /**
     * {@inheritDoc}
     * @param   string $typeName        System name of the notification type to parse (e.g.:
     *                                  participant_approval). Will determine what template will be
     *                                  used.
     * @param   array  $data            Abritary data to inject into template.
     * @param   Member $member          Member who will receive the notification. This information
     *                                  will be injected in the template.
     * @param mixed  $callToActionURL Relative or absolute URL to an action specific to the notice.
     * @return  ParsedNotificationInterface
     * NotificationFailureException
     */
    public function parse($typeName, array $data, Member $member, $callToActionURL = false)
    {
        // Get the notification
        $type = NotificationType::bySystemName($typeName);

        if ($type) {
            $data['CallToActionURL'] = $callToActionURL;
            return new ParsedNotification($type, $data, $member);
        } else {
            throw new NotificationFailureException(
                'Unknown notification type.',
                $typeName,
                $data,
                $member
            );
        }
    }
}
