<?php

use Phinx\Migration\AbstractMigration;

class CreateQuotes extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('quotes');
        $table->addColumn('content', 'string', ['limit' => 5000])
            ->addColumn('user_id', 'integer')
            ->addColumn('teacher_id', 'integer')
            ->addTimestamps(null, null)
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('teacher_id', 'teachers', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
