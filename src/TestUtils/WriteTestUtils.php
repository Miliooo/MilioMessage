<?php

namespace Milio\Message\TestUtils;

use Milio\Message\Write\Events\MessageAddedEvent;
use Milio\Message\Write\Events\ThreadCreatedEvent;

class WriteTestUtils
{
    const THREAD_ID = '61234f19-b8ca-4c54-8002-1615a9087da1';
    const THREAD_STARTER = 'Sophie';
    const RECEIVER_1 = 'Mark';
    const RECEIVER_2 = 'John';
    const SUBJECT = 'hello there Mark and John';
    const CREATED_AT = '2015-02-02 15:15:15';

    const MESSAGE_ID_1 = '61234f19-b8ca-4c54-8002-1615a9087da0';
    const SENDER_1 = 'Sophie';
    const BODY_1 = 'Hi this is the first message from Sophie to Mark and John';

    const MESSAGE_ID_2 = '61234f19-b8ca-4c54-8002-1615a9087da2';
    const SENDER_2 = 'Mark';
    const BODY_2 = 'Reply from Mark to Sophie and John';
    const CREATED_AT_2 = '2015-02-04 15:15:00';

    public static function getThreadCreatedEvent()
    {
        return new ThreadCreatedEvent(
            self::THREAD_ID,
            self::THREAD_STARTER,
            [self::RECEIVER_1, self::RECEIVER_2],
            self::SUBJECT,
            new \DateTime(self::CREATED_AT)
        );
    }

    public static function getFirstMessageAddedEvent()
    {
        return new MessageAddedEvent(
            self::THREAD_ID,
            self::MESSAGE_ID_1,
            self::THREAD_STARTER,
            self::BODY_1,
            new \DateTime(self::CREATED_AT)
        );
    }

    public static function getSecondMessageAddedEvent()
    {
        return new MessageAddedEvent(
            self::THREAD_ID,
            self::MESSAGE_ID_2,
            self::SENDER_2,
            self::BODY_2,
            new \DateTime(self::CREATED_AT_2)
        );
    }
}
