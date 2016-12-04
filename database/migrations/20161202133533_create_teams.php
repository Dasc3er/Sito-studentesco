<?php

use Phinx\Migration\AbstractMigration;

class CreateTeams extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('teams');
        $table->addColumn('course_id', 'integer')
            ->addColumn('name', 'string')
            ->addColumn('user_id', 'integer')
            ->addTimestamps(null, null)
            ->addForeignKey('course_id', 'courses', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->create();
    }
}
