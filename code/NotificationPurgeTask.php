<?php

/**
 * Build task to purge notification types that have been removed from the Default Records of {@link NotificationType}.
 */
class NotificationPurgeTask extends BuildTask
{
    /**
     * @inheritDoc
     * @return string
     */
    public function getTitle()
    {
        return 'Purge undefined notification types';
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function getDescription()
    {
        return 'Will delete any notification types that is not defined as a default record on the `Notification Type.`';
    }

    /**
     * @inheritDoc
     * @param  mixed $request HTTP Request.
     * @return void
     */
    public function run($request)
    {
        // Get all types not in the default list
        $defaultRecords = NotificationType::config()->get('default_records', Config::UNINHERITED);
        $sysNames = array_column($defaultRecords, 'SystemName');
        $typesToPurge = NotificationType::get()->exclude(['SystemName' => $sysNames]);

        // Display a warning if there's nothing to delete.
        if ($typesToPurge->count() == 0) {
            Debug::message('Nothing to delete', false);
            return;
        }

        // Loop the deprecated types and blast them.
        foreach ($typesToPurge as $type) {
            $type->delete();
            Debug::message('Deleted ' . $type->SystemName, false);
        }
    }
}
