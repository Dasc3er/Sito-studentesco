<?php

use Phinx\Migration\AbstractMigration;

class CreateLogins extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('logins');
        $table->addColumn('user_id', 'integer')
            ->addColumn('session_code', 'string')
            ->addColumn('browser', 'string')
            ->addColumn('address', 'string')
            ->addColumn('last_active', 'timestamp')
            ->addTimestamps(null, null)
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
