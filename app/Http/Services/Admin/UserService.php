<?php

namespace App\Http\Services\Admin;

use App\Models\User;

class UserService
{
    public function index(?array $data = null)
    {
        if ($data !== null) extract($data);
        $searchFields = ['name', 'email'];

        return User::with('acceptedBy')->when($orderBy, function ($q, $orderBy) use ($dataOrder) {
            return $q->orderBy($orderBy, $dataOrder ?? 'asc');
        }, function ($q) {
            return $q->orderBy('name', 'asc');
        })
            ->when($search ?? false, function ($q) use ($search, $searchFields) {
                $q->where(function ($query) use ($search, $searchFields) {
                    foreach ($searchFields as $field) {
                        $query->orWhere($field, 'like', '%' . $search . '%');
                    }
                });
            })
            ->where('id', '<>', '00000000-00000000-00000000-00000000')
            ->paginate(10)
            ->onEachSide(1)
            ->withQueryString();
    }

    public function getAdmins()
    {
        return User::where('role', User::ROLE_ADMIN)->get();
    }
}
