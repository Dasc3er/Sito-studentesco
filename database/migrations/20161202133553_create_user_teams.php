<?php

use Phinx\Migration\AbstractMigration;

class CreateUserTeams extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('user_teams');
        $table->addColumn('team_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addTimestamps(null, null)
            ->addForeignKey('team_id', 'teams', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
