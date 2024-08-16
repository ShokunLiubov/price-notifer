<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Dto\SubscriptionDTO;
use App\Services\OLXApiService;
use App\Services\OLXSubscriberService;
use App\Services\ValidateService;
use PHPMailer\PHPMailer\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class IndexController
{
    private ValidateService $validateService;
    private OLXSubscriberService $olxSubscriberService;
    private Request $request;

    public function __construct()
    {
        $this->validateService = new ValidateService();
        $this->olxSubscriberService = new OLXSubscriberService();
        $this->request = Request::createFromGlobals();
    }

    /**
     * @throws Exception
     */
    public function subscribe(): JsonResponse
    {
        $link = $this->request->get('link', '');
        $email = $this->request->get('email', '');

        $this->validateService->validateEmail($email)->isValidUrl($link);

        $advertId = (new OLXApiService())->getAdvertId($link);
        $dto = SubscriptionDTO::fromArray(['email' => $email, 'olx_advert_id' => $advertId, 'link' => $link]);

        if ($this->olxSubscriberService->subscribe($dto)) {
            return (new JsonResponse(['Subscribed']))->send();
        }

        return (new JsonResponse(['You are already subscribed']))->send();
    }

    public function emailVerify(): JsonResponse
    {
        $email = $this->request->get('email');
        $token = $this->request->get('token');

        $this->validateService->validateEmail($email);

        if ($this->olxSubscriberService->verifyEmail($email, $token)) {
            return (new JsonResponse(['Email verified']))->send();
        }

        return (new JsonResponse(['Not valid token']))->send();
    }
}
