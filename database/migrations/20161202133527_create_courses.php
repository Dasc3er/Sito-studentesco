<?php

use Phinx\Migration\AbstractMigration;

class CreateCourses extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('courses');
        $table->addColumn('event_id', 'integer')
            ->addColumn('school_id', 'integer')
            ->addColumn('name', 'string')
            ->addColumn('description', 'string')
            ->addColumn('place', 'string')
            ->addColumn('time', 'string')
            ->addColumn('capacity', 'integer')
            ->addColumn('team_capacity', 'integer')
            ->addTimestamps(null, null)
            ->addForeignKey('event_id', 'events', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('school_id', 'schools', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
