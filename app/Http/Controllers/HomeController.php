<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\Patient;
use DB;
use Illuminate\Http\Request;

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
        $this->middleware('web');
    }

    public function me(Request $request)
    {
        return $request->user();
    }

    public function stat()
    {
        $hospitals = Hospital::count();
        $patients = Patient::count();

        $stats = [
            'labels' => [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ],
            'data' => []
        ];

        $months = collect(range(1, 12))->map(fn($m) => [
            'month' => $m,
            'total' => 0,
        ]);

        $raw = DB::table('patients')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->get()
            ->keyBy('month');

        $stats['data'] = $months->map(fn($m) => $raw[$m['month']] ?? $m)->pluck('total');

        return response()->json(compact('hospitals', 'patients', 'stats'));
    }
}
