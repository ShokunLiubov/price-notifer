<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\AdvertForMailDto;
use App\Dto\SubscriptionDTO;
use App\Repositories\AdvertSubscriberRepository;
use App\Repositories\SubscriberRepository;

class OLXSubscriberService
{
    private AdvertService $advertService;
    private SubscriberRepository $subscriberRepository;
    private AdvertSubscriberRepository $advertSubscriberRepository;

    public function __construct()
    {
        $this->advertService = new AdvertService();
        $this->subscriberRepository = new SubscriberRepository();
        $this->advertSubscriberRepository = new AdvertSubscriberRepository();
    }

    public function getSubscriberByEmail(string $email): mixed
    {
        return $this->subscriberRepository->findByEmail($email);
    }

    public function createSubscriberAndGetId(string $email): int
    {
        $id = $this->subscriberRepository->create($email);

        (new NotificationService())->sendEmailVerify($email, $id);

        return $id;
    }

    public function getOrCreateSubscriber(string $email): int
    {
        $subscriber = $this->getSubscriberByEmail($email);

        if (!$subscriber) {
            return $this->createSubscriberAndGetId($email);
        }

        return $subscriber['id'];
    }

    public function subscribe(SubscriptionDTO $dto): ?int
    {
        $advertId = $this->advertService->getOrCreateAdvert($dto->olxAdvertId);
        $subscriberId = $this->getOrCreateSubscriber($dto->email);

        //Check if subscription exists
        if ($this->advertSubscriberRepository->exists($advertId, $subscriberId)) {
            return null;
        }

        return $this->advertSubscriberRepository->add($advertId, $subscriberId);
    }

    public function notifySubscribers(): void
    {
        $changedPriceAdverts = $this->advertService->getAdvertsWithChangedPrice();

        foreach ($changedPriceAdverts as $advert) {
            $subscriberEmails = $this->subscriberRepository->getByAdvertId($advert['id']);
            $dto = AdvertForMailDto::fromArray($advert);
            (new NotificationService())->sendEmailChangedPrice($dto, $subscriberEmails);
            $this->updateLastPrice($dto);
        }
    }

    public function updateLastPrice(AdvertForMailDto $dto): void
    {
        (new AdvertService())->updatePrice($dto->id, ['field' => 'last_price', 'price' => $dto->currentPrice]);
    }

    public function verifyEmail(string $email, string $token): ?int
    {
        $subscriber = $this->getSubscriberByEmail($email);

        if (!$subscriber || $subscriber['email_verified']) {
            return null;
        }

        $verificationRecord = $this->subscriberRepository->findVerificationRecord($subscriber['id'], $token);

        if (!$verificationRecord) {
            return null;
        }

        if ($this->subscriberRepository->verifyEmail($subscriber['id'])) {
            return $this->subscriberRepository->deleteVerificationRecord($verificationRecord['id']);
        }

        return null;
    }
}
