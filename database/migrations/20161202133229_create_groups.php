<?php

use Phinx\Migration\AbstractMigration;

class CreateGroups extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('groups');
        $table->addColumn('school_id', 'integer')
            ->addColumn('name', 'string')
            ->addTimestamps(null, null)
            ->addForeignKey('school_id', 'schools', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();

        $users = $this->table('users');
        $users->addColumn('group_id', 'integer', ['null' => true])
            ->addForeignKey('group_id', 'groups', 'id', ['delete' => 'SET NULL', 'update' => 'NO_ACTION'])
            ->save();
    }
}
