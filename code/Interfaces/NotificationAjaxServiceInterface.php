<?php

/**
 * Define a Notification Ajax Service that can be use to display notification to the user.
 */
interface NotificationAjaxServiceInterface
{

    /**
     * Retrieve the number of unread notification destined for a specific member.
     * @param  mixed $member Member object or ID. If not defined will assumed the call is for the current Member.
     * @throws PermissionFailureException Thrown if the current member does not have read access to the provided member.
     * @return Notification[]
     */
    public function getUnreadNotificationCountForMember($member = null);

    /**
     * Retrieve a list of unread notifications for the given member. The notifications are sorted in Ascending order of
     * creation.
     * @param  mixed $member Member object or ID. If not defined will assumed the call is for the current Member.
     * @throws PermissionFailureException Thrown if the current member does not have read access to the provided member.
     * @return DataList
     */
    public function getUnreadNotificationsForMember($member = null);

    /**
     * Mark a notification as read. This will prevent it from being returned by `getUnreadNotificationsForMember()`.
     * Only the member who the Notifcation was destined to can mark it as read.
     * @param  integer $notificationID ID of the notification to mark as read.
     * @throws PermissionFailureException Thrown if the current member is not allowed to mark this notification as read.
     * @throws ValidationException Thrown if the notification is already marked as read or does not exists.
     * @return void
     */
    public function markNotificationAsRead($notificationID);

    /**
     * Mark all notification as read for the given user.
     * @param  mixed $member Member object or ID. If not defined will assumed the call is for the current Member.
     * @throws PermissionFailureException Thrown if the current member is not allowed to perform the action.
     * @throws ValidationException If the member doesn't exists.
     * @return void
     */
    public function markAllNotificationAsReadForMember($member = null);
}
