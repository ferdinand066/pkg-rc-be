<?php

namespace App\Http\Services\Admin;

use App\Models\User;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UserService
{
    public function index(array $data = null)
    {
        if ($data !== null) extract($data);

        return User::when($orderBy, function ($q, $orderBy) use ($dataOrder) {
                return $q->orderBy($orderBy, $dataOrder ?? 'asc');
            }, function ($q) {
                return $q->orderBy('name', 'asc');
            })
            ->where('id', '<>', '00000000-00000000-00000000-00000000')
            ->paginate(10)
            ->onEachSide(1)
            ->withQueryString();
    }
}
