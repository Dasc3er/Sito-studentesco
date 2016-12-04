<?php

use Phinx\Migration\AbstractMigration;

class CreateLikes extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('likes');
        $table->addColumn('user_id', 'integer')
            ->addColumn('quote_id', 'integer')
            ->addTimestamps(null, null)
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('quote_id', 'quotes', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
