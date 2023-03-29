<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcSeed;

/**
 * CustomEntries seed.
 */
class CustomEntries2Seed extends BcSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'custom_table_id' => 2,
                'modified' => NULL,
                'created' => NULL,
                'name' => 'プログラマー',
                'title' => 'プログラマー',
                'creator_id' => 1,
                'lft' => 1,
                'rght' => 2,
                'level' => 0,
                'status' => 1,
                'publish_begin' => NULL,
                'publish_end' => NULL,
                'published' => NULL,
            ],
        ];

        $table = $this->table('custom_entry_2_occupations');
        $table->insert($data)->save();
    }
}
