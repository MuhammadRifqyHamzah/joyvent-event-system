<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ApiTesterController extends Controller
{
    /**
     * Render the admin API Tester (Mini Postman) view.
     */
    public function index()
    {
        return view('admin.api-tester');
    }
}
