<?php

class MockBadParsedNotification extends MockParsedNotification
{
    public function getSubject()
    {
        throw new Exception('Mock Exception');
    }
}
