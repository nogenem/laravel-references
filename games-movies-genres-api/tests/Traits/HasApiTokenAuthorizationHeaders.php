<?php

namespace Tests\Traits;

use App\Models\User;

trait HasApiTokenAuthorizationHeaders
{
    protected User $authorizedUser;
    protected string $authorizationToken;
    protected array $authorizationHeaders;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorizedUser = User::factory()->create();
        $this->authorizationToken = $this->authorizedUser->createToken('api-token')->plainTextToken;

        $this->authorizationHeaders = [
            'Authorization' => "Bearer " . $this->authorizationToken,
        ];
    }
}
