<?php

namespace Milio\Message\Read\Model\Dbal;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;

class DbalSchema
{
    /**
     * Object Representation of the thread table.
     *
     * @return Table
     */
    public static function getThreadTable()
    {
        $schema = new Schema();

        $table = $schema->createTable('milio_thread');
        $table->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true]);
        $table->addColumn('thread_id', 'guid', ['length' => 36]);
        $table->addColumn('sender', 'string', ['length' => 36]);
        $table->addColumn('subject', 'string', ['length' => 230]);
        $table->addColumn('created_at', 'datetime');
        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('thread_id'));

        return $table;
    }

    /**
     * Object Representation of the thread meta table.
     *
     * @return Table
     */
    public static function getThreadMetaTable()
    {
        $schema = new Schema();

        $table = $schema->createTable('milio_thread_meta');
        $table->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true]);
        $table->addColumn('thread_id', 'guid', ['length' => 36]);
        $table->addColumn('user_id', 'guid', ['length' => 36]);
        $table->addColumn('is_inbox', 'boolean');
        $table->addColumn('unread_count', 'integer');
        $table->addColumn('last_message_date', 'datetime');
        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('thread_id', 'user_id'));

        return $table;
    }

    /**
     * Object Representation of the message table.
     *
     * @return Table
     */
    public static function getMessageTable()
    {
        $schema = new Schema();

        $table = $schema->createTable('milio_message');
        $table->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true]);
        $table->addColumn('thread_id', 'guid', ['length' => 36]);
        $table->addColumn('message_id', 'guid', ['length' => 36]);
        $table->addColumn('sender', 'guid', ['length' => 36]);
        $table->addColumn('body', 'text');
        $table->addColumn('created_at', 'datetime');
        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('message_id'));

        return $table;
    }

    /**
     * Object Representation of the message meta table.
     *
     * @return Table
     */
    public static function getMessageMetaTable()
    {
        $schema = new Schema();

        $table = $schema->createTable('milio_message_meta');
        $table->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true]);
        $table->addColumn('message_id', 'guid', ['length' => 36]);
        $table->addColumn('user_id', 'guid', ['length' => 36]);
        $table->addColumn('is_read', 'boolean');
        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('message_id', 'user_id'));

        return $table;
    }

    /**
     * Returns all the tables.
     *
     * @return Table[]
     */
    public static function getTables()
    {
        return [
            self::getThreadTable(),
            self::getThreadMetaTable(),
            self::getMessageTable(),
            self::getMessageMetaTable(),
        ];
    }
}
