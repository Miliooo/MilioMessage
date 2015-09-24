<?php

namespace Milio\Message\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Milio\Message\Exceptions\ThreadMetaForParticipantNotFoundException;

interface ThreadInterface
{
    /**
     * Gets all the messages contained in the thread.
     *
     * @return ArrayCollection|MessageInterface
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
     * @param string $participantId The participant
     *
     * @throws ThreadMetaForParticipantNotFoundException When no thread meta was found for given participant
     *
     * @return ThreadMetaInterface
     */
    public function getThreadMetaForParticipant($participantId);

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
     * @return string The subject of the thread
     */
    public function getSubject();

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
     * @param string $participantId The participant we check
     *
     * @return boolean true if participant, false otherwise
     */
    public function isParticipant($participantId);

    /**
     * @param $participantId
     *
     * @return string[]
     */
    public function getOtherParticipants($participantId);

    /**
     * Adds a message to the thread
     *
     * @param MessageInterface $message
     */
    public function addMessage(MessageInterface $message);
}