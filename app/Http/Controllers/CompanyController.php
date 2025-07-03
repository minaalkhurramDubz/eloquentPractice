<?php

namespace App\Http\Controllers;

use App\Company;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::orderBy('name')->get();

        return view('companies', [
            'companies' => $companies,
        ]);
    }
}
