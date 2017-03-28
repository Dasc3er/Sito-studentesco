<?php

use Phinx\Migration\AbstractMigration;

class CreateEvents extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('events');
        $table->addColumn('name', 'string')
            ->addColumn('date', 'date')
            ->addColumn('subscription_end', 'date')
            ->addTimestamps(null, null)
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->create();
    }
}
