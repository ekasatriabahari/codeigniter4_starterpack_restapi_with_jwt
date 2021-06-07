<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TokensMigration extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'users_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'unsigned' => true,
            ],
            'token' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'timestamp',
            ],
            'updated_at' => [
                'type' => 'timestamp',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('users_id', 'users', 'id');
        $this->forge->createTable('tokens');
    }

    public function down()
    {
        //
        $this->forge->dropTable('tokens');
    }
}
