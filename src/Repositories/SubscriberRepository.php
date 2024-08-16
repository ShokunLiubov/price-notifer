<?php

declare(strict_types=1);

namespace App\Repositories;

class SubscriberRepository extends Repository
{
    protected string $table = 'subscribers';
    const EMAIL_VERIFIED = 1;

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM `$this->table` WHERE `email` = :email";
        $query = $this->db->doQuery($sql, ['email' => $email]);

        return $this->db->first($query);
    }

    public function create(string $email): int
    {
        $sql = "INSERT INTO `$this->table` (`email`) VALUES (:email)";
        return $this->db->insertAndGetId($sql, ['email' => $email]);
    }

    public function getByAdvertId(int $id): false|array
    {
        $sql = "SELECT s.email FROM {$this->table} s
            JOIN advert_subscriber asub ON s.id = asub.subscriber_id
            WHERE asub.advert_id = :id AND s.email_verified = :email_verified";

        return $this->db->doQuery($sql, ['id' => $id, 'email_verified' => self::EMAIL_VERIFIED])->fetchAll();
    }

    public function findVerificationRecord($subscriberId, $token): ?array
    {
        $sql = "SELECT * FROM email_verification WHERE subscriber_id = :subscriber_id AND token = :token";
        $query = $this->db->doQuery($sql, ['subscriber_id' => $subscriberId, 'token' => $token]);

        return $this->db->first($query);
    }

    public function verifyEmail($subscriberId): int
    {
        $sql = "UPDATE $this->table SET email_verified = :email_verified WHERE id = :id";
        return $this->db->doQuery($sql, ['email_verified' => self::EMAIL_VERIFIED, 'id' => $subscriberId])->rowCount();
    }

    public function deleteVerificationRecord($id): int
    {
        $sql = "DELETE FROM email_verification WHERE id = :id";
        return $this->db->doQuery($sql, ['id' => $id])->rowCount();
    }
}
