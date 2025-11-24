<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Users;

class SiteController extends Controller
{
    public function index()
    {
        // Hodimlar sonini hisoblash
        $admins = DB::table('users')
            ->where('user_type', Users::TYPE_ADMIN)
            ->where('status', Users::STATUS_ACTIVE)
            ->count();

        $teachers = DB::table('users')
            ->where('user_type', Users::TYPE_TEACHER)
            ->where('status', Users::STATUS_ACTIVE)
            ->count();

        $koordinators = DB::table('users')
            ->where('user_type', Users::TYPE_KOORDINATOR)
            ->where('status', Users::STATUS_ACTIVE)
            ->count();

        $students = DB::table('users')
            ->where('user_type', Users::TYPE_STUDENT)
            ->where('status', Users::STATUS_ACTIVE)
            ->count();

        return view('backend.site.index', compact(
            'admins',
            'teachers',
            'koordinators',
            'students'
        ));
    }
}