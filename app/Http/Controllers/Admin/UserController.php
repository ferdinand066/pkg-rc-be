<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Services\Admin\UserService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(UserService $service)
    {
        $data = $this->getSearchAndSort();
        $users = $service->index($data);

        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Successfully get users', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Successfully get users', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|integer|between:1,2'
        ]);

        if ($user->id === Auth::user()->id) {
            return $this->sendError(Response::HTTP_UNPROCESSABLE_ENTITY, 'You cannot update your role!');
        }

        $user->update($validated);
        $user->refresh();

        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Successfully update user role', compact('user'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    public function activate(User $user){
        $user->update([
            'account_accepted_at' => now(),
            'account_accepted_by' => Auth::user()->id,
        ]);

        $user->refresh();

        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Successfully activate this user', compact('user'));
    }
}
