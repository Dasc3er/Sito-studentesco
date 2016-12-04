<?php

use Phinx\Migration\AbstractMigration;

class CreateUserGroups extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('user_groups');
        $table->addColumn('group_id', 'integer')
            ->addColumn('name', 'string')
            ->addTimestamps(null, null)
            ->addForeignKey('group_id', 'groups', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
