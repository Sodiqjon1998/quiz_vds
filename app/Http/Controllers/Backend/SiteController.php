<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Bu qatorni qo'shing
use App\Models\User;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers2025 = DB::table('users')
            ->whereYear('created_at', date('Y'))
            ->where('user_type', '=', User::TYPE_TEACHER) // Array emas!
            ->count();

        $teachers2024 = DB::table('users')
            ->whereYear('created_at', date('Y')-1)
            ->where('user_type', '=', User::TYPE_TEACHER) // Array emas!
            ->count();

        $koordinators2025 = DB::table('users')
            ->whereYear('created_at', date('Y'))
            ->where('user_type', '=', User::TYPE_KOORDINATOR) // 2024 yil uchun
            ->count();

        $koordinators2024 = DB::table('users')
            ->whereYear('created_at', date('Y') - 1)
            ->where('user_type', '=', User::TYPE_KOORDINATOR) // 2024 yil uchun
            ->count();

        return view('backend.site.index', [
            'teachers2025' => $teachers2025,
            'teachers2024' => $teachers2024,
            'koordinators2025' => $koordinators2025,
            'koordinators2024' => $koordinators2024,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
