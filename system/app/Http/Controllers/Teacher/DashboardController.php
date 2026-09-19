<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show teacher dashboard (Unified into Main Dashboard)
     */
    public function index()
    {
        return redirect()->route('admin.dashboard');
    }
}
