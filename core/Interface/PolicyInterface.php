<?php
namespace Core\Interface;

use Core\Http\Request;
use Core\Http\Response;

interface PolicyInterface
{
    public static function check(Request $request): ?Response;
}