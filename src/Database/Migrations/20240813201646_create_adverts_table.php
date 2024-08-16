<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAdvertsTable extends AbstractMigration
{
    public function up(): void
    {
        if (!$this->hasTable('adverts')) {
            $table = $this->table('adverts');
            $table->addColumn('olx_advert_id', 'integer', ['null' => false])
                ->addColumn('last_price', 'integer', ['null' => true, 'default' => null])
                ->addColumn('current_price', 'integer', ['null' => true, 'default' => null])
                ->addColumn('currency', 'string', ['limit' => 4])
                ->addColumn('link', 'string',  ['null' => true, 'default' => null])
                ->addColumn('title', 'string',  ['null' => true, 'default' => null])
                ->addColumn('link_image', 'string',  ['null' => true, 'default' => null])
                ->addIndex(['olx_advert_id'], ['unique' => true])
                ->create();
        }
    }

    public function down(): void
    {
        if ($this->hasTable('adverts')) {
            $this->table('adverts')->drop()->save();
        }
    }
}
