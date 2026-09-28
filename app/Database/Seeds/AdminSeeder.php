<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('users')->countAllResults() > 0) {
            echo "A staff account exists; seeder skipped.\n";
            return;
        }
        $username = trim((string) getenv('POS_ADMIN_USER'));
        $password = (string) getenv('POS_ADMIN_PASSWORD');
        $name = trim((string) getenv('POS_ADMIN_NAME'));
        if (! preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username) || strlen($password) < 12 || $name === '' || strlen($name) > 100) {
            throw new \RuntimeException('Set POS_ADMIN_USER (3-50 letters/numbers/_), POS_ADMIN_NAME, and POS_ADMIN_PASSWORD (at least 12 characters) before seeding.');
        }
        $this->db->table('users')->insert(['username' => $username, 'full_name' => $name, 'password' => password_hash($password, PASSWORD_DEFAULT), 'active' => 1, 'created_at' => date('Y-m-d H:i:s')]);
        echo "Initial staff account created. Remove the POS_ADMIN_PASSWORD environment variable now.\n";
    }
}
