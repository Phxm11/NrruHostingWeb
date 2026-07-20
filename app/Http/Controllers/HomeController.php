<?php

namespace App\Http\Controllers;

use App\Models\ResourcePlan;

class HomeController extends Controller
{
    public function index()
    {
        $virtualServerPlans = ResourcePlan::where('service_type', 'virtual_server')
            ->orderBy('fee_per_year')
            ->get();

        $webHostingPlans = ResourcePlan::where('service_type', 'web_hosting')
            ->orderBy('fee_per_year')
            ->get();

        return view('home', compact('virtualServerPlans', 'webHostingPlans'));
    }
}
