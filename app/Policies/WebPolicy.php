<?php
namespace App\Policies;

use Core\Interface\PolicyInterface;
use Core\Http\Request;
use Core\Http\Response;
use Core\Auth\Auth;

class WebPolicy implements PolicyInterface
{
    public static function authorize(): bool
    {
        return Auth::check() && Auth::user()['type'] === 'web';
    }

    public static function check(Request $request): ?Response
    {

        if (!Auth::check()) {
            return Response::redirectResponse(base_url('auth/login'));
        }

        if (Auth::user()['type'] !== 'web') {
            return Response::redirectResponse(base_url('unauthorized'));
        }

        return null;
    }
}
