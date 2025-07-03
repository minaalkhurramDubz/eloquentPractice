<?php

namespace App\Http\Controllers;

use App\Company;
use App\User;

class UsersController extends Controller
{
    public function index() {}

    public function eagerLoad()
    {
        // creating query
        $users = User::query()
   // eager loads
            ->with('company')
            ->orderBy('name')
            ->simplePaginate();

        return view('users', ['users' => $users]);
    }

    public function subQuery()
    {
        // creating query

        $users = User::query()
            ->addSelect(column: [
                'company_name' => Company::select('name')
                    ->whereColumn('companies.id', 'users.company_id')
                    ->limit(1),
            ])
            ->orderBy('name')
            ->simplePaginate();

        return view('users', ['users' => $users]);
    }

    public function filterByCompany($company)
    {
        $users = User::whereHas('company', function ($query) use ($company) {
            $query->where('name', 'like', '%'.$company.'%');
        })
            ->with('company')
            ->orderBy('name')
            ->simplePaginate();

        return view('users', [
            'users' => $users,
        ]);
    }
}
