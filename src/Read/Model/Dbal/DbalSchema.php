<?php

namespace Milio\Message\Read\Model\Dbal;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;

class DbalSchema
{
    /**
     * Object Representation of the table used in this class.
     *
     * @return Table
     */
    public function getThreadTable()
    {
        $schema = new Schema();

        $table = $schema->createTable('milio_thread');

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('thread_id', 'guid', ['length' => 36]);
        $table->addColumn('sender', 'string', ['length' => 36]);
        $table->addColumn('created_at', 'datetime', ['required' => true]);
        $table->setPrimaryKey(array('id'));
        $table->addUniqueIndex(array('thread_id'));

        return $table;
    }
}
