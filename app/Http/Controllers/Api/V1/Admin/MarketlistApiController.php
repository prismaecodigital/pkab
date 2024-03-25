<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marketlist;
use App\Models\Bu;
use App\Models\Dept;
use App\Models\User;
use App\Models\Site;
use App\Models\MarketlistItem;
use App\Models\MarketlistOrderItem;
use App\Http\Requests\StoreMarketlistRequest;
use App\Http\Requests\UpdateMarketlistRequest;
use App\Http\Resources\Admin\MarketlistResource;
use App\Http\Resources\Admin\MarketlistOrderItemResource;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use PDF;

class MarketlistApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        abort_if(Gate::denies('marketlist_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (auth()->user()->hasRole('purchasing')) {
            $data = new MarketlistResource(Marketlist::with(['items', 'bu', 'site', 'user'])->advancedFilter()->whereIn('status', ['purchasing_ml_1','purchasing_ml_2','user_acc'])->paginate(request('limit', 10)));
            $bus = Bu::get(['id', 'name']);
            $sites = Site::get(['id', 'name']);
        }
        else {
            $data = new MarketlistResource(Marketlist::with(['items', 'bu', 'site', 'user'])->advancedFilter()->whereIn('status', ['purchasing_ml_1','purchasing_ml_2','user_acc'])->whereIn('bu_id', auth()->user()->bu->pluck('id'))->paginate(request('limit', 10)));
            $bus = Bu::whereIn('id', auth()->user()->bu->pluck('id'))->get(['id', 'name']);
            $sites = Site::whereIn('bu_id', auth()->user()->bu->pluck('id'))->get(['id', 'name']);
        }

        return response([
            'data' => $data,
            'meta' => [
                'bu'   => $bus,
                'site'   => $sites
            ],
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        abort_if(Gate::denies('marketlist_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bu_id = auth()->user()->bu()->pluck('id');

        return response([
            'meta' => [
                'bu'   => Bu::whereIn('id', $bu_id)->get(['id', 'name']),
                'item' => MarketlistItem::get(['id','name']),
                'satuan' => Marketlist::SATUAN_SELECT
            ],
        ]);       

        // return new MarketlistResource(Marketlist::with('items')->advancedFilter()->paginate(request('limit', 10)));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMarketlistRequest  $request // no
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMarketlistRequest $request)
    {
        //
        $site = Site::findOrFail($request->site_id);
        $bu = Bu::findOrFail($request->bu_id);
    
        $data = $request->only([
            'event',
            'bu_id',
            'site_id'
        ]);
    
        $data['user_id'] = auth()->user()->id;
        $data['created_at'] = Carbon::now();
        $data['status'] = 'purchasing_ml_1';
        $data['code'] = $this->generateCode($site->code ?? 'ML', $bu->id, $data['created_at']);
    
            $marketlist = Marketlist::create($data);
    
            foreach ($request->items as $itemData) {
                $item = MarketlistOrderItem::create([
                    'item_id' => $itemData['item_id'],
                    'ml_id' => $marketlist->id,
                    'required_date' => $itemData['required_date'],
                    'qty' => $itemData['qty'],
                    'satuan' => $itemData['satuan'],
                    'notes' => $itemData['notes'] ?? '',
                ]);
            }
    
            return (new MarketlistResource($marketlist))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marketlist  $marketlist
     * @return \Illuminate\Http\Response
     */
    public function show(Marketlist $marketlist)
    {
        // abort if gate denies
        return response([
            'data' => new MarketlistResource($marketlist->load(['bu','site','user','items'])),
            'meta' => [
                'bu'   => Bu::get(['id', 'name']),
                'site'   => Site::get(['id', 'name']),
                'satuan' => Marketlist::SATUAN_SELECT,
                
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marketlist  $marketlist
     * @return \Illuminate\Http\Response
     */
    public function edit(Marketlist $marketlist)
    {
        //
        abort_if(Gate::denies('marketlist_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'data' => new MarketlistResource($marketlist->load(['bu','site','user','items'])),
            'meta' => [
                'bu'   => Bu::get(['id', 'name']),
                'site'   => Site::get(['id', 'name']),
                'satuan' => Marketlist::SATUAN_SELECT,
                'item' => MarketlistItem::get(['id','name']),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMarketlistRequest  $request
     * @param  \App\Models\Marketlist  $marketlist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Marketlist $marketlist)
    {
        // return response()->json($request->all());

        if($marketlist->status === 'user_acc') {
            foreach($request->items as $item) {
                $marketlistOrder = MarketlistOrderItem::where('id', $item['id'])->first();
                $marketlistOrder->update(['approved_qty' => $item['approved_qty'], 'approved_date' => $item['approved_date']]);
            }
            if($request->isClosed === '1') {
                $marketlist->update(['status' => 'selesai']);
            }
        }
        if($marketlist->status === 'purchasing_ml_2') {
            foreach($request->items as $item) {
                $marketlistOrder = MarketlistOrderItem::where('id', $item['id'])->first();
                $marketlistOrder->update(['qty' => $item['qty'], 'satuan' => $item['satuan']]);
            }
            $marketlist->update(['status' => 'user_acc']);
        }
        if($marketlist->status === 'purchasing_ml_1') {
            foreach($request->items as $item) {
                $marketlistOrder = MarketlistOrderItem::where('id', $item['id'])->first();
                $marketlistOrder->update(['qty' => $item['qty'], 'satuan' => $item['satuan']]);
            }
            $marketlist->update(['status' => 'purchasing_ml_2']);
        }

        return new MarketlistResource($marketlist->load(['user', 'site', 'bu', 'items']));

    }

    public function updateDataOnly(Request $request, Marketlist $marketlist)
    {
        // dd($request->all());
        $marketlist = Marketlist::where('id', $request->id)->first();
        // return response()->json($request->all());
        $marketlist->update($request->all());
        $marketlist->items()->delete();
        foreach ($request->items as $itemData) {
            $item = MarketlistOrderItem::create([
                'item_id' => $itemData['item_id'],
                'ml_id' => $marketlist->id,
                'required_date' => $itemData['required_date'],
                'qty' => $itemData['qty'],
                'satuan' => $itemData['satuan'],
                'notes' => $itemData['notes'] ?? '',
            ]);
        }

        return new MarketlistResource($marketlist->load(['user', 'site', 'bu', 'items']));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marketlist  $marketlist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Marketlist $marketlist)
    {
        //
        $marketlist->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    protected function generateCode($siteCode, $buId, $createdAt)
    {
        $bu = Bu::where('id', $buId)->first();
        $buCode = $bu->code;
        $dateCode = substr($createdAt, 2, 2) . substr($createdAt, 5, 2);
        
        $marketlist = Marketlist::whereYear('created_at', $createdAt)
            ->whereMonth('created_at', $createdAt)
            ->orderBy('id', 'desc');
        $count = $marketlist->count();
        $last = $marketlist->orderBy('id','desc')->first();
        $number = str_pad($count + 1, 3, "0", STR_PAD_LEFT);
        
        if(empty($last)) {
            $number = str_pad($count + 1, 3, "0", STR_PAD_LEFT);
            $new_code = $buCode . $siteCode . $dateCode . $number;
            return $new_code;
        }
        
        $code = $last->code;
        $num = intval(substr($code, -3));
        $number = str_pad($num + 1, 3, "0", STR_PAD_LEFT);
        $new_code = $buCode . $siteCode . $dateCode . $number;
        
        return $new_code;
    }

    public function approveData(Request $request, Marketlist $marketlist)
    {
        $marketlist = Marketlist::where('id', $request->id)->first();
        if($marketlist->status === 'user_acc') {
            foreach($request->items as $item) {
                $marketlistOrder = MarketlistOrderItem::where('id', $item['id'])->first();
                $marketlistOrder->update(['approved_qty' => $item['approved_qty'], 'approved_date' => $item['approved_date']]);
            }
            if($request->isClosed === '1') {
                $marketlist->update(['status' => 'selesai']);
            }
        }
        if($marketlist->status === 'purchasing_ml_2') {
            foreach($request->items as $item) {
                $marketlistOrder = MarketlistOrderItem::where('id', $item['id'])->first();
                $marketlistOrder->update(['qty' => $item['qty'], 'satuan' => $item['satuan']]);
            }
            $marketlist->update(['status' => 'user_acc']);
        }
        if($marketlist->status === 'purchasing_ml_1') {
            foreach($request->items as $item) {
                $marketlistOrder = MarketlistOrderItem::where('id', $item['id'])->first();
                $marketlistOrder->update(['qty' => $item['qty'], 'satuan' => $item['satuan']]);
            }
            $marketlist->update(['status' => 'purchasing_ml_2']);
        }

        return new MarketlistResource($marketlist->load(['user', 'site', 'bu', 'items']));
    }

    public function rejectData(Request $request)
    {
        $marketlist = Marketlist::where('id', $request->id)->first();
        
        $marketlist->update(['status' => 'cancel']);

        return new MarketlistResource($marketlist->load(['user', 'site', 'bu', 'items']));
    }

    public function report(Request $request)
    {
        $startDate = substr($request->startDate, 0, 10);
        $endDate = substr($request->endDate, 0, 10);
        $startDateLabel = Carbon::createFromFormat('Y-m-d', $startDate)->format('d F Y');
        $endDateLabel = Carbon::createFromFormat('Y-m-d', $endDate)->format('d F Y');
        $bu = Bu::findOrFail($request->bu_id)->name;
        $site = Site::findOrFail($request->site_id)->name;
        $now = Carbon::now()->format('d F Y');
        $marketlists = Marketlist::with('items')->where('bu_id', $request->bu_id)->where('site_id', $request->site_id)->whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at')->get();

        $pdf = PDF::loadview('reportMarketlist', ['marketlists' => $marketlists, 'now' => $now, 'startDate' => $startDateLabel, 'endDate' => $endDateLabel, 'bu' => $bu, 'site' => $site]);
    	return $pdf->stream();

    }
}
