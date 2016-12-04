<?php

use Phinx\Migration\AbstractMigration;

class CreateUserCourses extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('user_courses');
        $table->addColumn('course_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addTimestamps(null, null)
            ->addForeignKey('course_id', 'courses', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
