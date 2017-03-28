<?php

use Phinx\Migration\AbstractMigration;

class CreateCourses extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('courses');
        $table->addColumn('name', 'string')
            ->addColumn('description', 'string', ['null' => true])
            ->addColumn('place', 'string')
            ->addColumn('capacity', 'integer')
            ->addColumn('team_capacity', 'integer', ['null' => true])
            ->addColumn('event_id', 'integer')
            ->addColumn('school_id', 'integer')
            ->addTimestamps(null, null)
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addForeignKey('event_id', 'events', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('school_id', 'schools', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
