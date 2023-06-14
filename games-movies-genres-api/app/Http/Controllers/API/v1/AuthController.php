<?php

namespace App\Http\Controllers\API\v1;

use App\Exceptions\API\v1\UnauthorizedHttpException;
use App\Exceptions\API\v1\UnprocessableEntityHttpException;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Auth\AuthChangePasswordRequest;
use App\Http\Requests\API\v1\Auth\AuthLoginRequest;
use App\Http\Requests\API\v1\Auth\AuthRegisterRequest;
use App\Http\Requests\API\v1\Auth\AuthForgotPasswordRequest;
use App\Http\Requests\API\v1\Auth\AuthResetPasswordRequest;
use App\Http\Resources\API\v1\UserResource;
use App\Http\Responses\API\v1\JsonMessageResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function register(AuthRegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response(new UserResource($user), 201);
    }

    public function login(AuthLoginRequest $request)
    {
        if (!auth()->attempt($request->validated())) {
            throw new UnauthorizedHttpException(__('auth.failed'));
        }

        /** @var User */
        $user = auth()->user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response(['token' => $token], 200);
    }

    public function user()
    {
        return response(new UserResource(auth()->user()), 200);
    }

    public function logout()
    {
        /** @var User */
        $user = auth()->user();

        $user->tokens()->delete();
        $user->withAccessToken(null);

        $guard = auth()->guard();
        if (method_exists($guard, 'forgetUser')) {
            $guard->{'forgetUser'}();
        } elseif (method_exists($guard, 'logout')) {
            $guard->{'logout'}();
        }

        return new JsonMessageResponse(__('auth.logged_out'), 200);
    }

    public function forgotPassword(AuthForgotPasswordRequest $request)
    {
        $credentials = $request->validated();
        $email = $credentials['email'];

        $status = Password::sendResetLink($credentials, function ($user, $token) {
            $user->sendAPIV1PasswordResetNotification($token);
        });

        if ($status != Password::RESET_LINK_SENT) {
            $statusMessage = __($status);
            info("Tried to send a reset link for the email: $email, but received a status: $statusMessage");
        }

        // Always say that it was sent, to not give malicious people info about our users
        return new JsonMessageResponse(__(Password::RESET_LINK_SENT), 200);
    }

    public function resetPassword(AuthResetPasswordRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'token' => $request->token,
        ] + $request->validated();

        $status = Password::reset(
            $credentials,
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                ])->save();
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            throw new UnprocessableEntityHttpException(__($status));
        }

        return new JsonMessageResponse(__($status), 200);
    }

    public function changePassword(AuthChangePasswordRequest $request)
    {
        /** @var User */
        $user = auth()->user();

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return new JsonMessageResponse(__('passwords.change'), 200);
    }
}
