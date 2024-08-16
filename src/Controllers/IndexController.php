<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Response\Response;
use App\Dto\SubscriptionDTO;
use App\Services\OLXApiService;
use App\Services\OLXSubscriberService;
use App\Services\ValidateService;
use PHPMailer\PHPMailer\Exception;

class IndexController
{
    private ValidateService $validateService;
    private OLXSubscriberService $olxSubscriberService;

    public function __construct()
    {
        $this->validateService = new ValidateService();
        $this->olxSubscriberService = new OLXSubscriberService();
    }

    /**
     * @throws Exception
     */
    public function subscribe(): Response
    {
        $link = request()->get('link');
        $email = request()->get('email');

        $this->validateService->validateEmail($email)->isValidUrl($link);

        $advertId = (new OLXApiService())->getAdvertId($link);
        $dto = SubscriptionDTO::fromArray(['email' => $email, 'olx_advert_id' => $advertId, 'link' => $link]);

        if ($this->olxSubscriberService->subscribe($dto)) {
            return response()->json(['Subscribed']);
        }

        return response()->json(['You are already subscribed']);
    }

    public function emailVerify(): Response
    {
        $email = request()->get('email');
        $token = request()->get('token');

        $this->validateService->validateEmail($email);

        if ($this->olxSubscriberService->verifyEmail($email, $token)) {
            return response()->json(['Email verified']);
        }

        return response()->json(['Not valid token']);
    }
}
