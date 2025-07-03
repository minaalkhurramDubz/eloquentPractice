<?php

namespace App\Http\Controllers;

use App\Company;
use App\User;

class UsersController extends Controller
{
    public function index()
    {

        // get the search query string from the url or null (default)
        $search = $_GET['search'] ?? null;

        // build the query , eager loading associated companies
        $users = User::query()->with('company');

        // if seach not empty
        if (! empty($search)) {
            // break apart the search string into words
            collect(str_getcsv($search, ' ', '"'))
                ->filter()
                // append with a wildcard for filtering the queries
                ->each(function ($term) use ($users) {
                    $term = $term.'%';
                    // search each term individual - works faster because of combined queries due to indexing 
                    $users->where(function ($query) use ($term) {
                        $query->where('name', 'like', $term)
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

    // takes longer to run because it does not use indexes efficiently, especially if the subquery has ORDER BY, LIMIT, or lacks WHERE conditions

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
