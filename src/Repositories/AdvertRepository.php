<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Dto\AdvertDTO;

class AdvertRepository extends Repository
{
    protected string $table = 'adverts';

    public function findByAdvertId(int $advertId): ?array
    {
        $sql = "SELECT * FROM `$this->table` WHERE `olx_advert_id` = :olx_advert_id";
        $query = $this->db->doQuery($sql, ['olx_advert_id' => $advertId]);

        return $this->db->first($query);
    }

    public function getAdvertsWithChangedPrice(): array|false
    {
        $sql = "SELECT `id`, `last_price`, `current_price`, `currency`, `link`, `title`, `link_image` 
                FROM `$this->table` 
                WHERE `last_price` != `current_price`";

        return $this->db->doQuery($sql)->fetchAll();
    }

    public function create(AdvertDTO $dto): int
    {
        $sql = "INSERT INTO `$this->table` 
                (`olx_advert_id`, `link`, `last_price`, `current_price`, `title`, `link_image`, `currency`) 
                VALUES (:olx_advert_id, :link, :last_price, :current_price, :title, :link_image, :currency)";

        return $this->db->insertAndGetId(
            $sql,
            [
                'olx_advert_id' => $dto->olxAdvertId,
                'link' => $dto->link,
                'last_price' => $dto->price,
                'current_price' => $dto->price,
                'title' => $dto->title,
                'link_image' => $dto->linkImage,
                'currency' => $dto->currency
            ]
        );
    }

    public function updatePrice(int $id, array $price): int
    {
        $sql = "UPDATE `$this->table` SET `" . $price['field'] . "` = :price WHERE `id` = :id";
        return $this->db->doQuery($sql, ['id' => $id, 'price' => $price['price']])->rowCount();
    }
}
