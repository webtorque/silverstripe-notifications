<?php

/**
 * Test registion for Immunoglobin
 */
class NotificationPurgeTaskTest extends SapphireTest
{
    protected static $fixture_file = 'NotificationPurgeTaskTest.yml';
    protected $usesDatabase = true;

    public function testRun()
    {
        Config::inst()->update('NotificationType', 'default_records', [
            ['SystemName' => 'TestOne', 'Name' => 'Test One'],
            ['SystemName' => 'TestTwo', 'Name' => 'Test Two'],
        ]);

        $singleton = NotificationType::singleton();
        ob_start(); // Suppress output
        NotificationPurgeTask::singleton()->run(new NotificationPurgeTask());
        ob_get_clean(); // Unsuppress output

        $this->assertEmpty(NotificationType::bySystemName('boom'), 'Boom Notification Type should have been removed by NotificationPurgeTask.');
        $this->assertNotEmpty(NotificationType::bySystemName('TestOne'), 'TestOne Notification Type should still be there.');
    }
}
