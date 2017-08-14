<?php

/**
 * Represent a notification that is attached to a specific member. Is normally created by
 * {@link NotificationDataObjectProvider}.
 *
 * @property string         $Subject
 * @property string         $ShortMessage
 * @property string         $RichMessage
 * @property boolean        $Viewed
 * @property SS_Datetime    $ViewedOn
 * @property string         $CallToActionURL
 * @property integer        $MemberID
 * @property Member         $Member
 */
class Notification extends DataObject
{
    private static $db = [
        'Subject' => 'Text',
        'ShortMessage' => 'Text',
        'RichMessage' => 'HTMLText',
        'ViewedOn' => 'Datetime',
        'CallToActionURL' => 'Varchar(128)'
    ];

    private static $has_one = [
        'Member' => 'Member'
    ];

    private static $summary_fields = array(
        'Member.FirstName' => 'First Name',
        'Member.Surname' => 'Surname',
        'Subject' => 'Subject'
    );

    private static $default_sort = [
        'Created' => 'ASC'
    ];

    /**
     * @inheritDoc
     * @param  Member|null $member User for which access is detemined.
     * @return boolean
     */
    public function canView($member = null)
    {
        if (!$member || !(is_a($member, 'Member')) || is_numeric($member)) {
            $member = Member::currentUser();
        }

        return $member->ID == $this->MemberID ? true :  parent::canView($member);
    }

    /**
     * @inheritDoc
     * @param  Member|null $member User for which access is detemined.
     * @return boolean
     */
    public function canEdit($member = null)
    {
        if (!$member || !(is_a($member, 'Member')) || is_numeric($member)) {
            $member = Member::currentUser();
        }

        return $member->ID == $this->MemberID ? true :  parent::canEdit($member);
    }

    /**
     * @inheritDoc
     * @param  Member|null $member User for which access is detemined.
     * @return boolean
     */
    public function canDelete($member = null)
    {
        if (!$member || !(is_a($member, 'Member')) || is_numeric($member)) {
            $member = Member::currentUser();
        }

        return $member->ID == $this->MemberID ? true :  parent::canDelete($member);
    }

    /**
     * Determine if the provided user can mark this Notification as read.
     * @param  mixed $member Member object. Defaults to the current user.
     * @return boolean
     */
    public function canMarkAsRead($member = null)
    {
        if (!$member || !(is_a($member, 'Member')) || is_numeric($member)) {
            $member = Member::currentUser();
        }

        return $member->ID == $this->MemberID;
    }

    /**
     * Accessor for the Viewed attribute
     * @return boolean
     */
    public function getViewed()
    {
        return (boolean)$this->ViewedOn;
    }

    /**
     * Mutator for theViewed attribute.
     * @param boolean $value New value.
     */
    public function setViewed($value)
    {
        if ($value != $this->Viewed) {
            $this->ViewedOn = $value ? SS_Datetime::now()->getValue() : null;
        }
    }
}
