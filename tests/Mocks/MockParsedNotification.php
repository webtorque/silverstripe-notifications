<?php

class MockParsedNotification implements ParsedNotificationInterface
{
    public function getSubject()
    {
        return 'Mock subject response';
    }

    public function getShortMessage()
    {
        return 'Mock short message response';
    }

    public function getRichMessage()
    {
        return 'Mock rich message response';
    }

    public function getSystemSubject() {
        return 'Mock system subject response';
    }
}
