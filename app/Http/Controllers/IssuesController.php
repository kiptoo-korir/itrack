<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Repository;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IssuesController extends Controller
{
    protected $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function createIssue(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'repositoryId' => 'required|exists:repositories,id',
        ]);

        $body = $request->body;
        $title = $request->title;
        $repositoryId = $request->repositoryId;
        $userId = Auth::id();
        $httpClient = $this->tokenService->client($userId);

        $repositoryInformation = Repository::findOrFail($repositoryId);
        $repositoryFullname = $repositoryInformation->fullname;

        $issuesPostUrl = "https://api.github.com/repos/{$repositoryFullname}/issues";
        $issuesData = [
            'title' => $title,
            'body' => $body,
        ];

        $response = $httpClient->post($issuesPostUrl, $issuesData);
        $responseBody = json_decode($response->body());

        if (201 === $response->status()) {
            $newIssueDetails = [
                'title' => $title,
                'body' => $body,
                'repository' => $repositoryId,
                'owner' => $userId,
                'issue_no' => $responseBody->number,
                'state' => $responseBody->state,
                'date_created_online' => $responseBody->created_at,
                'date_updated_online' => $responseBody->updated_at,
                'date_closed_online' => $responseBody->closed_at,
                // 'labels' => $responseBody->labels
            ];
            Issue::create($newIssueDetails);

            return response()->json(['message' => 'Issue created successfully'], 201);
        }
    }
}
