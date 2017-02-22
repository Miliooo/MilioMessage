<?php
namespace Milio\Message\Controller;

use Milio\Message\Read\Provider\DbalThreadProvider;

/**
 * Class SingleThreadController
 */
class SingleThreadController
{
    /**
     * @var DbalThreadProvider
     */
    private $provider;

    public function __construct(DbalThreadProvider $provider)
    {
        $this->provider = $provider;
    }

    /** /thread/{id}/
     * @param string $threadId
     *
     * @return string
     */
    public function getThread($threadId)
    {
        $thread = $this->provider->getThread($threadId);
        $messages = $this->provider->getMessages($threadId);
        $msg = '';

        $data['thread'] = $thread->toArray();
        foreach($messages as $message) {
            $msg[] = $message->toArray();
        }

        $data['messages'] = $msg;



       return json_encode($data, JSON_PRETTY_PRINT);
    }


}