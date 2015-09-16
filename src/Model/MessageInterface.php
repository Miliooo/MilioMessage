<?php

namespace Milio\Message\Model;


interface MessageInterface
{
    /**
     * Gets the unique id of the message
     *
     * @return integer The unique id of the message
     */
    public function getId();

    /**
     * Gets the creation time of the message
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Gets the body of the message
     *
     * @return string The body
     */
    public function getBody();


    /**
     * Gets the sender of the message
     *
     * @return string
     */
    public function getSenderId();

    /**
     * Adds message meta to the messageMeta collection
     *
     * @param MessageMetaInterface $messageMeta
     */
    public function addMessageMeta(MessageMetaInterface $messageMeta);

    /**
     * Returns an array collection with message meta
     *
     * @return ArrayCollection An ArrayCollection of messageMeta
     */
    public function getMessageMeta();

    /**
     * Gets message meta for the given participant
     *
     * @param string $participant The participant
     *
     * @return MessageMetaInterface|null The messagemeta or null when not found
     */
    public function getMessageMetaForParticipant($participant);

    /**
     * Gets the thread this message belongs to
     *
     * @return ThreadInterface the thread this message belongs to
     */
    public function getThread();
}