<?php

namespace App\Services;

class ApiCallsService
{
    protected $tokenService;
    protected $invalidTokenService;

    public function __construct(TokenService $tokenService, InvalidTokenService $invalidTokenService)
    {
        $this->tokenService = $tokenService;
        $this->invalidTokenService = $invalidTokenService;
    }

    // Recursive method to handle paginated requests to Github
    // Once it has recursively performed all calls to Github, It returns an array
    // Buckle in for some spaghetti code
    public function githubCallsHandler(string $url, int $userId): array
    {
        $tokenStatus = (new UserDataService())->checkGithubTokenStatus($userId);

        if (!$tokenStatus) {
            dd('No token');
        }

        $client = $this->tokenService->client($userId);

        $response = $client->get($url);

        $resources = json_decode($response->body());
        $linkHeader = $response->headers()['Link'] ?? null;

        $linksArray = ($linkHeader[0]) ? $this->parseLinkHeader($linkHeader[0]) : [];

        if (isset($linkHeader[0]) && !isset($linksArray['next']) && !isset($linksArray['last'])) {
            return $resources;
        }

        $moreResources = $this->githubCallsHandler($linksArray['next'], $userId);

        return array_merge($resources, $moreResources);
    }

    protected function parseLinkHeader(string $header): array
    {
        if (0 == strlen($header)) {
            throw new \Exception('input must not be of zero length');
        }

        $parts = explode(',', $header);
        $links = [];

        foreach ($parts as $p) {
            $section = explode(';', $p);

            if (2 != count($section)) {
                throw new \Exception("section could not be split on ';'");
            }
            $url = trim(preg_replace('/<(.*)>/', '$1', $section[0]));
            $name = trim(preg_replace('/rel="(.*)"/', '$1', $section[1]));
            $links[$name] = $url;
        }

        return $links;
    }

    // protected function checkForToken () {
    //     $tokenStatus = (new UserDataService())->checkGithubTokenStatus();
    // }
}
