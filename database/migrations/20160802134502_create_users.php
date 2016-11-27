<?php

use Phinx\Migration\AbstractMigration;

class CreateUsers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->addColumn('name', 'string')
            ->addColumn('username', 'string')
            ->addColumn('password', 'string')
            ->addColumn('email', 'string')
            ->addColumn('email_token', 'string')
            ->addColumn('reset_token', 'string')
            ->addColumn('role', 'integer')
            ->addColumn('state', 'integer')
            ->addTimestamps(null, null)
            ->addIndex(['email', 'email_token', 'reset_token'], ['unique' => true])
            ->create();
    }
}
