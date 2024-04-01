<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marketlist;
use App\Models\MarketlistItem;
use App\Models\Bu;
use App\Models\Site;
use PDF;
use Carbon\Carbon;


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
    public function index()
    {
        return view('home');
    }

    public function report(Request $request)
    {
        if(empty($request->bu_id) || empty($request->site_id) || empty($request->startDate) || empty($request->endDate)) {
            return response()->json('Harap isikan bu, site, dan rentang tanggal');
        }
        $startDate = Carbon::createFromFormat('d-m-Y', $request->startDate)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d-m-Y', $request->endDate)->format('Y-m-d');;
        $startDateLabel = Carbon::createFromFormat('Y-m-d', $startDate)->format('d F Y');
        $endDateLabel = Carbon::createFromFormat('Y-m-d', $endDate)->format('d F Y');
        $bu = Bu::findOrFail($request->bu_id)->name;
        $site = Site::findOrFail($request->site_id)->name;
        $now = Carbon::now()->format('d F Y');
        $marketlists = Marketlist::with(['items', 'user'])->whereNot('status','cancel')->where('bu_id', $request->bu_id)->where('site_id', $request->site_id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at')->get();

        // return response()->json($marketlists);
        $pdf = PDF::loadview('reportMarketlist', ['marketlists' => $marketlists, 'now' => $now, 'startDate' => $startDateLabel, 'endDate' => $endDateLabel, 'bu' => $bu, 'site' => $site]);
    	return $pdf->stream();

    }
}
