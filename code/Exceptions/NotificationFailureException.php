<?php


/**
 * Exception thrown when a notification fails to deliver.
 */
class NotificationFailureException extends Exception
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $data;

    /**
     * @var Member
     */
    private $member;

    /**
     * Instanciate a NotificationFailureException
     * @param string    $message  Exception message.
     * @param string    $type     Notification type that cause this error.
     * @param array     $data     Data tInstanciate a NotificationFailureExceptionhat was attached to the Notification send request.
     * @param Member    $member   Member for which the notification was destined.
     * @param integer   $code     Error code.
     * @param Exception $previous Underlying exception that cause this exception to be thrown.
     */
    public function __construct(
        $message = "",
        $type = "",
        array $data = [],
        Member $member = null,
        $code = 0,
        Exception $previous = null
    ) {
        $this->type = $type;
        $this->data = $data;
        $this->member = $member;
        return parent::__construct($message, $code, $previous);
    }

    /**
     * Get the value of Instanciate a NotificationFailureException
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of Notification type.
     * @param string $type String describing the Notification Type.
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the value of Data
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of Data
     * @param   array $data Orignal data provided with the send request.
     * @return  self
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get the value of Member.
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Set the value of Member
     * @param Member $member Member who the notification is destined for.
     * @return self
     */
    public function setMember(Member $member)
    {
        $this->member = $member;
        return $this;
    }

    /**
     * Set the previous exception.
     * @param Exception $ex Underlying exception that triggered this exception.
     * @return self
     */
    public function setPrevious(Exception $ex)
    {
        $this->previous = $ex;
        return $this;
    }
}
