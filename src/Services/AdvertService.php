<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\AdvertDTO;
use App\Repositories\AdvertRepository;

class AdvertService extends Service
{
    private AdvertRepository $advertRepository;

    public function __construct()
    {
        $this->advertRepository = new AdvertRepository();
    }

    public function getAdvertByAdvertId(int $advertId): mixed
    {
        return $this->advertRepository->findByAdvertId($advertId);
    }

    public function createAdvert(AdvertDTO $dto): int
    {
        return $this->advertRepository->create($dto);
    }

    public function getAdverts(): ?array
    {
        return $this->advertRepository->getAll();
    }

    public function getOrCreateAdvert(int $olxAdvertId): int
    {
        $advert = $this->getAdvertByAdvertId($olxAdvertId);

        if (!$advert) {
            $data = (new OLXApiService())->getAdvertDataById($olxAdvertId);
            $dto = AdvertDTO::fromArray(['olx_advert_id' => $olxAdvertId, ...$data]);

            return $this->createAdvert($dto);
        }

        return $advert['id'];
    }

    public function getAdvertsWithChangedPrice(): array|false
    {
        return $this->advertRepository->getAdvertsWithChangedPrice();
    }

    public function updatePrice(int $id, array $price): void
    {
        $this->advertRepository->updatePrice($id, $price);
    }
}
