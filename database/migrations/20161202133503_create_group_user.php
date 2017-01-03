<?php

use Phinx\Migration\AbstractMigration;

class CreateGroupUser extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('group_user');
        $table->addColumn('group_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('start', 'date')
            ->addColumn('end', 'date')
            ->addTimestamps(null, null)
            ->addForeignKey('group_id', 'groups', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
