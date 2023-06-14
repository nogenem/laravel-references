<?php

namespace App\Http\Controllers\API;

use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\User\UserSearchRequest;
use App\Http\Resources\API\User\SimpleUserResource;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function search(UserSearchRequest $request)
    {
        $filters = [
            'name' => $request->get('name')
        ];
        $sortQueryString = $request->get('sort') ?? '';
        $perPage = +$request->get('per_page', 20);

        $users = $this->userService->getAllFilteredSortedAndPaginated($filters, $sortQueryString, $perPage);

        return SimpleUserResource::collection($users);
    }
}
