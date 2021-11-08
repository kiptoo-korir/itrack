<?php

namespace Tests\Feature;

use App\Services\ApiCallsService;
use App\Services\InvalidTokenService;
use App\Services\TokenService;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class FetchIssuesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testExample()
    {
        // https://api.github.com/repos/Brian-Nduhiu/Store/issues
        $client = (new TokenService())->client(4);
        $apiService = (new ApiCallsService(new TokenService(), new InvalidTokenService()));

        $response1 = $client->get('https://api.github.com/repos/kiptoo-korir/laravel_sms_tryout/issues?sort=created');
        $response2 = $apiService->githubCallsHandler('https://api.github.com/repos/kiptoo-korir/laravel_sms_tryout/issues?sort=created&page=1&per_page=2', 4);

        $jsonResponse1 = json_decode($response1->body());

        $this->assertTrue($jsonResponse1 == $response2);
    }
}
