<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class OLXApiService
{
    private Client $client;
    protected const OLX_API_URL = 'https://m.olx.ua/api/v2/offers/';

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getAdvertId(string $link = ''): ?int
    {
        $link = basename(parse_url($link, PHP_URL_PATH));
        $response = $this->client->request('GET', "https://m.olx.ua/d/uk/obyavlenie/$link");
        $content = $response->getBody()->getContents();

        $pattern = '/"sku":"(\d+)"/';
        preg_match($pattern, $content, $matches);

        return (int)$matches[1] ?? null;
    }

    public function getAdvertDataById(int $id): array
    {
        $response = $this->client->request('GET', "https://m.olx.ua/api/v2/offers/$id");
        $data = json_decode($response->getBody()->getContents(), true);

        if (!isset($data['data'])) {
            return [];
        }

        $imageLink = str_replace(
            ['{width}', '{height}'],
            [$data['data']['photos'][0]['width'], $data['data']['photos'][0]['height']],
            $data['data']['photos'][0]['link']
        );

        return [
            'link' => $data['data']['url'],
            'price' => $data['data']['params'][0]['value']['value'],
            'currency' => $data['data']['params'][0]['value']['currency'],
            'title' => $data['data']['title'],
            'link_image' => $imageLink,
        ];
    }

    public function syncPrices(): void
    {
        $service = new AdvertService();
        $advertsMap = arrayByKey($service->getAdverts(), 'olx_advert_id');

        $requests = function () use ($advertsMap) {
            foreach ($advertsMap as $advert) {
                yield new Request('GET', self::OLX_API_URL . $advert['olx_advert_id']);
            }
        };

        $pool = new Pool($this->client, $requests(), [
            'concurrency' => 5,
            'fulfilled' => function (Response $response, $index) use ($advertsMap, $service) {
                $data = json_decode($response->getBody()->getContents(), true);

                // Get price from response
                $olxAdvertId = $data['data']['id'] ?? null;
                $price = $data['data']['params'][0]['value']['value'] ?? null;

                if (!$price || !$olxAdvertId) {
                    return;
                }

                if ($advertsMap[$olxAdvertId]['current_price'] != $price) {
                    $service->updatePrice($advertsMap[$olxAdvertId]['id'], ['field' => 'current_price', 'price' => $price]);
                }
            },
            'rejected' => function (RequestException $reason, $index) {
                $logFile = '/var/log/cron.log';
                file_put_contents($logFile, $reason->getMessage(), FILE_APPEND);
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();
    }
}
