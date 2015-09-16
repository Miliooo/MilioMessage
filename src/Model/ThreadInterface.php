<?php

namespace Milio\Message\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Milio\Message\Exceptions\ThreadMetaForParticipantNotFoundException;

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
     * @return ArrayCollection An ArrayCollection of thread meta
     */
    public function getThreadMeta();

    /**
     * Gets thread meta for the given participant
     *
     * @param string $userId The participant
     *
     * @throws ThreadMetaForParticipantNotFoundException When no thread meta was found for given participant
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

    /**
     * Gets all the participants for the current thread
     *
     * @return string[] An array with participants
     */
    public function getParticipants();

    /**
     * Checks if the given participant is a participant of the thread
     *
     * @param string $userId The participant we check
     *
     * @return boolean true if participant, false otherwise
     */
    public function isParticipant($userId);
}