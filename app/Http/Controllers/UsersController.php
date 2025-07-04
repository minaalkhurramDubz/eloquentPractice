<?php

namespace App\Http\Controllers;

use App\Company;
use App\User;

class UsersController extends Controller
{
    public function index() {}

    public function searchwithIndividualQuery()
    {
        $search = $_GET['search'] ?? null;

        $users = User::query()->with('company');

        if (! empty($search)) {
            collect(str_getcsv($search, ' ', '"'))
                ->filter()
                ->each(function ($term) use ($users) {
                    $term = $term.'%';

                    $users->where(function ($query) use ($term) {
                        $query->where('first_name', 'like', $term)
                            ->orWhere('last_name', 'like', $term)
                            ->orWhereIn('company_id', Company::query()
                                ->where('name', 'like', $term)
                                ->pluck('id')
                            );
                    });
                });
        }

        $users = $users->paginate(20);

        return view('users', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function searchWithUnion()
    {
        $search = $_GET['search'] ?? null;

        $users = User::query()->with('company');

        if (! empty($search)) {
            collect(str_getcsv($search, ' ', '"'))
                ->filter()
                ->each(function ($term) use ($users) {
                    $term = $term.'%';

                    $users->whereIn('id', function ($query) use ($term) {
                        $query->select('id')
                            ->from(function ($query) use ($term) {
                                $query->select('users.id')
                                    ->from('users')
                                    ->where('users.first_name', 'like', $term)
                                    ->orWhere('users.last_name', 'like', $term)
                                    ->union(
                                        $query->newQuery()
                                            ->select('users.id')
                                            ->from('users')
                                            ->join('companies', 'users.company_id', '=', 'companies.id')
                                            ->where('companies.name', 'like', $term)
                                    );
                            }, 'matches');
                    });
                });
        }

        $users = $users->paginate(20);

        return view('users', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function eagerLoad()
    {
        $users = User::query()
            ->with('company')
            ->orderBy('first_name')
            ->simplePaginate();

        return view('users', ['users' => $users]);
    }

    public function subQuery()
    {
        $users = User::query()
            ->addSelect([
                'company_name' => Company::select('name')
                    ->whereColumn('companies.id', 'users.company_id')
                    ->limit(1),
            ])
            ->orderBy('first_name')
            ->simplePaginate();

        return view('users', ['users' => $users]);
    }

    public function filterByCompany($company)
    {
        $users = User::whereHas('company', function ($query) use ($company) {
            $query->where('name', 'like', '%'.$company.'%');
        })
            ->with('company')
            ->orderBy('first_name')
            ->simplePaginate();

        return view('users', ['users' => $users]);
    }
}
