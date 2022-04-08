<?php

namespace App\Application;

final class GithubClient
{
    private const USERNAME = 'redacted';
    private const PERSONAL_TOKEN = 'redacted';

    public function timeline(string $number, int $page = 1)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request(
            'GET',
            'https://api.github.com/repos/engagor/engagor/issues/' . $number . '/timeline?per_page=100&page=' . $page,
            ['auth' => [self::USERNAME, self::PERSONAL_TOKEN]]
        );


        return $response->getBody()->getContents();
    }

    public function list(int $page = 1, string $labels = '')
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request(
            'GET',
            'https://api.github.com/repos/engagor/engagor/issues?state=closed&pulls=true&since=2021-01-01&per_page=100&page=' . $page . '&labels=' . $labels,
            ['auth' => [self::USERNAME, self::PERSONAL_TOKEN]]
        );

        return $response->getBody()->getContents();
    }
}