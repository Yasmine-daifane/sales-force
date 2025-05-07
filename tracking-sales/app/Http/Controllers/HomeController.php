<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommercialVisit;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
    // {
    //     return view('home');
    // }


    public function index(Request $request)
    {
        $search = $request->input('search');

        $visits = CommercialVisit::with('user') // Load the user (commercial)
            ->when($search, function ($query, $search) {
                return $query->where('client_name', 'like', "%{$search}%")
                             ->orWhere('location', 'like', "%{$search}%")
                             ->orWhere('cleaning_type', 'like', "%{$search}%");
            })->paginate(10);

        return view('home', compact('visits', 'search'));
    }
}
