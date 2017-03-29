<?php

use Phinx\Migration\AbstractMigration;

class CreateTeachers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('teachers');
        $table->addColumn('name', 'string')
            ->addColumn('user_id', 'integer')
            ->addTimestamps(null, null)
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->create();
    }
}
