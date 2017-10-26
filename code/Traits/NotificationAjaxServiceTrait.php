<?php

/**
 * Trait that implements the NotificationAjaxServiceInterface.
 */
trait NotificationAjaxServiceTrait
{

    /**
     * @inheritDoc
     * @param  mixed $member Member object or ID. If not defined will assumed the call is for the current Member.
     * @throws PermissionFailureException Thrown if the current member does not have read access to the provided member.
     * @return Notification[]
     */
    public function getUnreadNotificationCountForMember($member = null)
    {
        return $this->getUnreadNotificationsForMember($member)->count();
    }

    /**
     * @inheritDoc
     * @param  mixed $member Member object or ID. If not defined will assumed the call is for the current Member.
     * @throws PermissionFailureException Thrown if the current member does not have read access to the provided member.
     * @return DataList
     */
    public function getUnreadNotificationsForMember($member = null)
    {
        $member = $this->normalizeMember($member);

        if (!$member->canView()) {
            throw new PermissionFailureException('Access denied to member');
        }

        return Notification::get()
            ->filter('MemberID', $member->ID)
            ->exclude('Subject', '')
            ->where('"ViewedOn" IS NULL')
            ->sort('Created');
    }

    /**
     * @inheritDoc
     * @param  integer $notificationID ID of the notification to mark as read.
     * @throws PermissionFailureException Thrown if the current member is not allowed to mark this notification as read.
     * @throws ValidationException Thrown if the notification is already marked as read or does not exists.
     * @return void
     */
    public function markNotificationAsRead($notificationID)
    {
        /**
         * @var Notification
         */
        $notification = Notification::get()->byID($notificationID);
        if (!$notification) {
            throw new ValidationException('Unknown Notification.');
        }

        if (!$notification->canMarkAsRead()) {
            throw new PermissionFailureException('Access denied for marking notification as read.');
        }

        if ($notification->Viewed) {
            throw new ValidationException('Notification has already been viewed.');
        }

        $notification->Viewed = true;
        $notification->write();
    }

    /**
     * @inheritDoc
     * @param  mixed $member Member object or ID. If not defined will assumed the call is for the current Member.
     * @throws PermissionFailureException Thrown if the current member is not allowed to perform the action.
     * @throws ValidationException Thrown if the notification is already marked as read or does not exists.
     * @return void
     */
    public function markAllNotificationAsReadForMember($member = null)
    {
        $member = $this->normalizeMember($member);
        if ($member->ID != Member::currentUserID()) {
            throw new PermissionFailureException('Access denied for marking notifications as read.');
        }

        $update = SQLUpdate::create('"Notification"')
            ->addWhere(['MemberID' => $member->ID])
            ->addAssignments(['ViewedOn' => SS_Datetime::now()->getValue()])
            ->execute();
    }


    /**
     * Receives some member info as a Member Object, int or null. Return an appropriate member object.
     * @param  mixed $member Member info to normalize.
     * @throws ValidationException Thrown if the member is unknown.
     * @return Member
     */
    protected function normalizeMember($member = null)
    {
        if (!$member) {
            return Member::currentUser();
        }

        if (is_a($member, 'Member')) {
            return $member;
        }

        if (is_numeric($member)) {
            $member = Member::get()->byID($member);
            if ($member) {
                return $member;
            }
        }

        throw new ValidationException('Unknown Member');
    }
}
