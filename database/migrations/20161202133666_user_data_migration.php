<?php

use Phinx\Migration\AbstractMigration;

class UserDataMigration extends AbstractMigration
{
    protected $users = [
        [
            'name' => 'Admin',
            'username' => 'admin',
            'password' => 'admin',
            'email' => 'admin@gmail.com',
            'role' => 1,
            'state' => 1,
        ],
    ];

    protected $times = [
        [
            'name' => 'Prima ora',
        ],
        [
            'name' => 'Seconda ora',
        ],
        [
            'name' => 'Terza ora',
        ],
        [
            'name' => 'Quarta ora',
        ],
        [
            'name' => 'Quinta ora',
        ],
    ];

    public function up()
    {
        foreach ($this->users as $key => $value) {
            $this->users[$key]['password'] = \Crypt::hashpassword($value['password']);
        }

        $users = $this->table('users');
        $users->insert($this->users)->save();

        $times = $this->table('times');
        $times->insert($this->times)->save();
    }

    public function down()
    {
        foreach ($this->users as $value) {
            $this->execute('DELETE FROM users WHERE name="'.$value['name'].'"');
        }

        foreach ($this->times as $value) {
            $this->execute('DELETE FROM times WHERE name="'.$value['name'].'"');
        }
    }
}
