<?php

class MockBadParsedNotification extends MockParsedNotification
{
    public function getSubject()
    {
        throw new Exception('Mock Exception');
    }
    public function getSystemSubject()
    {
        throw new Exception('Mock Exception');
    }
}
