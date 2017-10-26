<?php

/**
 * Given a {@link NotificationType} and data, parse the notification into message elements suitable for a {@link Sender}.
 */
class ParsedNotification implements ParsedNotificationInterface
{
    protected $type;
    protected $data;
    protected $member;
    protected $subject=false;
    protected $shortMessage=false;
    protected $richMessage=false;

    /**
     * Instanciate a new ParsedNotification.
     * @param  NotificationType $type   DataObject describing the format to use for the different messages.
     * @param  array            $data   Information to inject in the format.
     * @param  Member           $member Member information that will be injected in the message.
     */
    public function __construct(
        NotificationType $type,
        array $data,
        Member $member
    ) {
        $this->type = $type;
        $this->data = $data;
        $this->member = $member;
    }



    /**
     * Retrieve a short statement suitable for an Email message subject line.
     * @return string
     */
    public function getSubject()
    {
        return $this->getGeneric('subject', 'SubjectFormat');
    }
    /**
     * Retrieve a short statement suitable for an Email message subject line.
     * @return string
     */
    public function getSystemSubject()
    {
        return $this->getGeneric('systemSubject', 'SystemSubjectFormat');
    }

    /**
     * Retrieve a Short Message suitable for an SMS message.
     * @return string
     */
    public function getShortMessage()
    {
        return $this->getGeneric('shortMessage', 'ShortMessageFormat');
    }

    /**
     * Retrieve an HTML-enriched messages.
     * @return string
     */
    public function getRichMessage()
    {
        return $this->getGeneric('richMessage', 'RichMessageFormat');
    }

    /**
     * Retrieve a message element based on its field name. Will parse it if necessary.
     * @param  string $fieldName           Name of the field to retrieve/parse.
     * @param  string $typeFormatFieldName Name of the format to use if the message hasn't been parsed yet.
     * @return string Parsed message element.
     */
    protected function getGeneric($fieldName, $typeFormatFieldName)
    {
        if ($this->$fieldName === false) {
            $this->$fieldName = $this->parse($typeFormatFieldName);
        }

        return $this->$fieldName;
    }

    /**
     * Parse a format from the Notification Type.
     * @param  string $typeFormatFieldName Name of the format field.
     * @return string
     */
    protected function parse($typeFormatFieldName)
    {
        $viewer = new SSViewer_FromString($this->type->$typeFormatFieldName);
        $viewable = new ArrayData($this->data);
        $viewable->setField('Member', $this->member);

        return $viewer->process($viewable);
    }
}
