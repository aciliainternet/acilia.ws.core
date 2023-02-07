<?php

namespace WS\Core\Service;

use Symfony\Component\HttpClient\HttpClient;

class RecaptchaService
{
    public const RECAPTCHA_URL_VERIFY = 'https://www.google.com/recaptcha/api/siteverify';

    private SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function verify(string $token): bool
    {
        $secretKey = $this->settingService->get('recaptcha_secret_key');
        $client = HttpClient::create();
        $score = 0.7;

        if (empty($token) || $token === null) {
            return false;
        }

        $response = $client->request(
            'POST',
            self::RECAPTCHA_URL_VERIFY,
            [
                'body' => [
                    'secret' => $secretKey,
                    'response' => $token
                ]
            ]
        )->getContent();
        $response = json_decode($response, true);
        if (!isset($response['score'])) {
            return false;
        }

        return floatval($response['score']) >= floatval($score);
    }
}
