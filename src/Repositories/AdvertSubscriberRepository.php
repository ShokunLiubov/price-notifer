<?php

declare(strict_types=1);

namespace App\Repositories;

class AdvertSubscriberRepository extends Repository
{
    protected string $table = 'advert_subscriber';

    public function exists(int $advertId, int $subscriberId): bool
    {
        $sql = "SELECT COUNT(*) FROM `$this->table` WHERE advert_id = :advert_id AND subscriber_id = :subscriber_id";
        $count = $this->db->doQuery($sql, ['advert_id' => $advertId, 'subscriber_id' => $subscriberId])->fetchColumn();

        return $count > 0;
    }

    public function add(int $advertId, int $subscriberId): int
    {
        $sql = "INSERT INTO `$this->table` (advert_id, subscriber_id) VALUES (:advert_id, :subscriber_id)";
        return $this->db->insertAndGetId($sql, ['advert_id' => $advertId, 'subscriber_id' => $subscriberId]);
    }
}
