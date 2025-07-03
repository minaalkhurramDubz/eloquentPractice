<?php

namespace App\Http\Controllers;

use App\User;

class UsersController extends Controller
{
 
    public function index()
    {
        // creating query 
        $users = User::query()
   // eager loads 
            ->with('company')
            ->orderBy('name')
            ->simplePaginate();

        return view('users', ['users' => $users]);
    }

    public function filterByCompany($company)
{
    $users = User::whereHas('company', function ($query) use ($company) {
            $query->where('name', 'like', '%' . $company . '%');
        })
        ->with('company')
        ->orderBy('name')
        ->simplePaginate();

    return view('users', [
        'users' => $users,
    ]);
}

}
