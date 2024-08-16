<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database\Database;

abstract class Repository
{
    protected Database $db;
    protected string $table = '';

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAll(): array|false
    {
        $sql = "SELECT * FROM `$this->table`";
        return $this->db->doQuery($sql)->fetchAll();
    }
}