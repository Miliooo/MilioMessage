<?php

namespace Milio\Message\Model;

use Doctrine\Common\Collections\ArrayCollection;

interface ThreadInterface
{
    /**
     * Gets all the messages contained in the thread.
     *
     * @return ArrayCollection
     */
    public function getMessages();

    /**
     * Returns an array collection with thread meta
     *
     * @return ArrayCollection An ArrayCollection of threadmeta
     */
    public function getThreadMeta();

    /**
     * Gets thread meta for the given participant
     *
     * @param string $userId The participant
     *
     * @return ThreadMetaInterface
     */
    public function getThreadMetaForParticipant($userId);

    /**
     * Gets the participant who created the thread.
     *
     * @return string The participant who created the thread
     */
    public function getCreatedBy();

    /**
     * @return \DateTime The creation date
     */
    public function getCreatedAt();

    /**
     * @return string $id
     */
    public function getThreadId();


}