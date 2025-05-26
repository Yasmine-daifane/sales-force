<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Import DB facade
use App\Models\User;
use App\Models\CommercialVisit;
use Carbon\Carbon; // Import Carbon for date manipulation

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
        // Optional: Add middleware to ensure only admins can access this dashboard
        // $this->middleware("role:admin");
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        // Ensure only admins see this detailed dashboard if needed
        // if (!$user || strtolower($user->role ?? '') !== 'admin') {
        //     return redirect('/default-route-for-non-admins');
        // }

        // --- Data Aggregation for Dashboard ---
        $commercialRoleName = "commercial";

        // 1. Count total number of commercials
        $commercialCount = User::where("role", $commercialRoleName)->count();

        // 2. Count total number of visits
        $totalVisitsCount = CommercialVisit::count();

        // 3. Get visits per commercial (for bar chart and table)
        $visitsPerCommercial = User::where("role", $commercialRoleName)
            ->withCount("commercialVisits")
            ->orderBy("commercial_visits_count", "desc")
            ->get();

        // 4. Prepare data for the Bar chart (Visits per Commercial)
        $barChartLabels = $visitsPerCommercial->pluck("name")->toArray();
        $barChartData = $visitsPerCommercial->pluck("commercial_visits_count")->toArray();

        // --- NEW KPIs ---
        $today = Carbon::today();

        // 5. Count visits in the last 7 days (including today)
        $visitsLast7Days = CommercialVisit::whereDate("visit_date", ">=", $today->copy()->subDays(6))->count();

        // 6. Count visits in the last 30 days (including today)
        $visitsLast30Days = CommercialVisit::whereDate("visit_date", ">=", $today->copy()->subDays(29))->count();

        // 7. Calculate average visits per commercial
        $averageVisits = ($commercialCount > 0) ? round($totalVisitsCount / $commercialCount, 2) : 0;

        // 8. Prepare data for Line chart (Daily Visits - Last 30 Days)
        $visitsDailyRaw = CommercialVisit::select(DB::raw("DATE(visit_date) as date"), DB::raw("count(*) as count"))
            ->whereDate("visit_date", ">=", $today->copy()->subDays(29))
            ->groupBy("date")
            ->orderBy("date", "asc")
            ->pluck("count", "date");

        // Create a full date range for the last 30 days, initialized to 0 visits
        $dateRange = collect();
        for ($i = 29; $i >= 0; $i--) {
            $dateRange->put($today->copy()->subDays($i)->toDateString(), 0);
        }

        // Merge the actual counts into the full date range
        $dailyVisitCounts = $dateRange->merge($visitsDailyRaw);

        $lineChartLabels = $dailyVisitCounts->keys()->map(function($date) {
            // Format date for display (e.g., "May 26")
            return Carbon::parse($date)->isoFormat("MMM D");
        })->toArray();
        $lineChartData = $dailyVisitCounts->values()->toArray();

        // --- Pass all data to the view ---
        return view("home", compact(
            "commercialCount",
            "totalVisitsCount",
            "visitsPerCommercial",
            "barChartLabels",
            "barChartData",
            "visitsLast7Days",      // New
            "visitsLast30Days",     // New
            "averageVisits",        // New
            "lineChartLabels",      // New
            "lineChartData"         // New
        ));
    }
}

