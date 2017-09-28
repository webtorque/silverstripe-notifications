<?php

/**
 * DataObject representing a notification type.
 *
 * User can not create or delete notification types. They can however Edit the format of a notifcation type to customise
 * the message that will be sent to the user.
 *
 * @property-read   string          $Name                   Human friendly name of the notification type.
 * @property-read   string          $SystemName             Non-Human friendly name of the notification type.
 * @property-read   string          $FormatVariables        List of type specific variables that can be used. Must be a
 *                                                          comma-sperated string.
 * @property        string          $SubjectFormat          Format for the subject of the notification type.
 * @property        HTMLText        $MessageFormat          Format for the content of the notification type.
 * @property        string          $ShortMessageFormat     Format for the content of the notification type.
 * @property        string          $ShortMessageFormat     Format for the content of the notification type.
 * @todo Add a variable list to the help text editors know what variables they can use.
 */
class NotificationType extends DataObject
{
    private static $db = [
        'Name'                  => 'Varchar(100)',
        'SystemName'            => 'Varchar(100)',
        'SubjectFormat'         => 'Varchar(255)',
        'FormatVariables'       => 'Text',
        'ShortMessageFormat'    => 'Varchar(160)',
        'RichMessageFormat'     => 'HTMLText',
    ];

    private static $summary_fields = [
        'Name',
    ];

    private static $indexes = [
        'SystemName_unique' => 'unique("SystemName")'
    ];

    private static $field_labels = [
        'SubjectFormat' => 'Email Subject Format',
        'MessageFormat' => 'Rich Email Format',
    ];

    /**
     * {@inheritDoc}
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Don't let end users edit the Name of the notification type
        $fields->fieldByName('Root.Main.Name')
            ->setReadOnly(true)
            ->setDisabled(true);

        // Smelly end users don't need to know what the System Name for the notification type is.
        $fields->removeByName(['SystemName', 'FormatVariables']);

        $fields->fieldByName('Root.Main.ShortMessageFormat')
            ->setMaxLength(160)
            ->setRightTitle('Used for SMS messages. Limited to 160 characters.');

        $fields->fieldByName('Root.Main.ShortMessageFormat')
            ->setMaxLength(255)
            ->setRightTitle('Used for SMS messages. Limited to 160 characters.');

        $fields->addFieldToTab(
            'Root.Main',
            LiteralField::create(
                'AvailableVariableHelp',
                $this->renderWith('NotificationType_FormatVariables')
            )
        );

        return $fields;
    }

    /**
     * {@inheritDoc}
     * @param  mixed $member User for which the permission is checked.
     * @return boolean
     */
    public function canCreate($member = null)
    {
        // Don't let anyone create a new Notification Types.
        return false;
    }

    /**
     * {@inheritDoc}
     * @param  mixed $member User for which the permission is checked.
     * @return boolean
     */
    public function canDelete($member = null)
    {
        // Don't let anyone delete a notification type.
        return false;
    }

    /**
     * Retrieve a NotificationType by its System Name or null if none can be found.
     * @param  string $systemName System Name search for.
     * @return NotificationType
     */
    public static function bySystemName($systemName)
    {
        return self::get()
            ->filter(['SystemName' => $systemName])
            ->first();
    }

    /**
     * @inheritDoc
     * @return void
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();

        // Create/update default records.
        $defaultRecords = $this->config()->get('default_records', Config::UNINHERITED);
        foreach ($defaultRecords as $record) {
            $do = self::bySystemName($record['SystemName']);
            if (!$do) {
                $do = new self($record);
            }
            $do->Name = $record['Name'];
            if (isset($record['FormatVariables'])) {
                $do->FormatVariables = $record['FormatVariables'];
            }
            $do->write();
        }
    }

    /**
     * Return a list of FormatVariables for display in a template.
     * @return ArrayList    List of format variables.
     */
    public function FormatVariablesList()
    {
        // Split our format variables into an array.
        $strList = $this->FormatVariables ? explode(',', $this->FormatVariables) : [];

        // Loop over the format variable array and built a list suitable for an SS tempalte.
        $list = ArrayList::create();
        foreach ($strList as $str) {
            $list->add(ArrayData::create(['Text' => $str]));
        }
        return $list;
    }
}
