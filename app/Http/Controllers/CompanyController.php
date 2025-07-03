<?php

namespace App\Http\Controllers;
   use App\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{


public function index()
{
    $companies = Company::orderBy('name')->get();

    return view('companies', [
        'companies' => $companies
    ]);
}

}
