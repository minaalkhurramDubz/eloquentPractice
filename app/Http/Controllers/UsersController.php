<?php

namespace App\Http\Controllers;

use App\Company;
use App\User;

class UsersController extends Controller
{
    public function index() {}

    public function searchwithIndividualQuery()
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

    public function searchWithUnion()
    {
        // Get the search term from the query string (?search=...)
        $search = $_GET['search'] ?? null;

        // Start the base query with eager loading for related company
        $users = User::query()->with('company');

        if (! empty($search)) {
            // Break search string into terms, handling quoted values too
            collect(str_getcsv($search, ' ', '"'))
                ->filter()
                ->each(function ($term) use ($users) {

                    // Sanitize term to remove special characters, then add SQL wildcard
                    //      $term = preg_replace('/[^A-Za-z0-9]/', '', $term) . '%';
                    $term = $term.'%';

                    // Use subquery with UNION to find matching user IDs
                    $users->whereIn('id', function ($query) use ($term) {
                        $query->select('id')
                            ->from(function ($query) use ($term) {
                                // First part of UNION: match user by name
                                $query->select('users.id')
                                    ->from('users')
                                    ->where('users.name', 'like', $term)

                                      // UNION with users who belong to matching company
                                    ->union(
                                        $query->newQuery()
                                            ->select('users.id')
                                            ->from('users')
                                            ->join('companies', 'users.company_id', '=', 'companies.id')
                                            ->where('companies.name', 'like', $term)
                                    );
                            }, 'matches'); // alias the subquery
                    });
                });
        }

        // Paginate the final result
        $users = $users->paginate(20);

        // Pass results and search value to the view
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
