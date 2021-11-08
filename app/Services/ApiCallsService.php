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

        // Early return when user doesn't have a valid token
        if (!$tokenStatus) {
            (new NotificationService())->notifyOfInvalidToken($userId);

            return [];
        }

        $client = $this->tokenService->client($userId);

        $response = $client->get($url);

        $statusCode = $response->status();
        $this->invalidTokenService->responseHandler($statusCode);

        // Early return for errorneous response
        if ($this->checkForError($statusCode)) {
            return [];
        }

        $resources = json_decode($response->body());
        $linkHeader = $response->headers()['Link'] ?? null;

        $linksArray = isset($linkHeader) ? $this->parseLinkHeader($linkHeader[0]) : [];

        if ((0 === count($linksArray)) || (!isset($linksArray['next']) && !isset($linksArray['last']))) {
            return $this->typeConverter($resources);
        }

        $moreResources = $this->githubCallsHandler($linksArray['next'], $userId);

        $mergedArray = array_merge($resources, $moreResources);

        return $this->typeConverter($mergedArray);
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

    protected function typeConverter(mixed $input): array
    {
        if ('array' === gettype($input)) {
            return $input;
        }

        return (array) $input;
    }

    protected function checkForError(int $statusCode): bool
    {
        $errorCodes = [500, 400, 403, 404];

        return in_array($statusCode, $errorCodes);
    }
}
