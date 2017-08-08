<?php

class NotificationDataObjectDelivery extends NotificationDelivery
{

    /**
     * @var Notification
     */
    protected $dataobject;

    /**
     * @param Notification $dataobject Saved Notification object.
     */
    public function __construct(Notification $dataobject)
    {
        parent::__construct('QUEUED', 'DATAOBJECT');
        $this->dataobject = $dataobject;
    }

    /**
     * Object that was created to notify the user.
     * @return Notification  Saved Notification object.
     */
    public function getDataObject()
    {
        return $this->dataobject;
    }
}
