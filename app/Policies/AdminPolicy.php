<?php
namespace App\Policies;

use Core\Http\Request;
use Core\Http\Response;
use Core\Auth\Auth;
use Core\Interface\PolicyInterface;

class AdminPolicy implements PolicyInterface
{
    public static function authorize(): bool
    {
        return Auth::check() && Auth::user()['type'] === 'admin';
    }

    public static function check(Request $request): ?Response
    {

        if (!Auth::check()) {
            return Response::redirectResponse(base_url('login'));
        }

        return null; // acesso liberado
    }
}