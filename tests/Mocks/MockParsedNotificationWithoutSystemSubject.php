<?php

class MockParsedNotificationWithoutSystemSubject extends MockParsedNotification
{
    public function getSystemSubject() {
        return '';
    }
}
