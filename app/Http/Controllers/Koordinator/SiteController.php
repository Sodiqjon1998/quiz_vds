<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;

class SiteController extends Controller
{
    /**
     * Display the koordinator dashboard.
     */
    public function index()
    {
        return view('koordinator.site.index');
    }
}
