<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marketlist;
use App\Models\Bu;
use App\Models\Dept;
use App\Models\User;
use App\Models\MarketlistItem;
use App\Models\MarketlistOrderItem;
use App\Http\Requests\StoreMarketlistRequest;
use App\Http\Requests\UpdateMarketlistRequest;
use App\Http\Resources\Admin\MarketlistDoneResource;
use App\Http\Resources\Admin\MarketlistOrderItemResource;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

class MarketlistDoneApiController extends Controller
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
        return new MarketlistDoneResource(Marketlist::with(['bu', 'dept', 'user', 'site'])->advancedFilter()->whereIn('status', ['selesai','cancel'])->paginate(request('limit', 10)));
        }
        else {
        return new MarketlistDoneResource(Marketlist::with(['bu', 'dept', 'user', 'site'])->advancedFilter()->whereIn('status', ['selesai','cancel'])->whereIn('bu_id', auth()->user()->bu->pluck('id'))->paginate(request('limit', 10)));            
        }

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

        $depts = auth()->user()->dept()->pluck('bu_id');

        return response([
            'meta' => [
                'bu'   => Bu::whereIn('id', $depts)->get(['id', 'name']),
                'item' => MarketlistItem::get(['id','name'])
            ],
        ]);       

        return new MarketlistDoneResource(Marketlist::with('items')->advancedFilter()->paginate(request('limit', 10)));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMarketlistRequest  $request // no
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $dept = Dept::findOrFail($request->dept_id);
        $bu = Bu::findOrFail($request->bu_id);
    
        $data = $request->only([
            'event',
            'bu_id',
            'dept_id'
        ]);
    
        $data['user_id'] = auth()->user()->id;
        $data['created_at'] = Carbon::now();
        $data['status'] = 'purchasing_ml_1';
        $data['code'] = $this->generateCode($dept->code, $bu->code, $data['created_at']);
    
            $marketlist = Marketlist::create($data);
    
            foreach ($request->items as $itemData) {
                $item = MarketlistOrderItem::create([
                    'item_id' => $itemData['item_id'],
                    'ml_id' => $marketlist->id,
                    'required_date' => $itemData['required_date'],
                    'qty' => $itemData['qty'],
                    'satuan' => $itemData['satuan'],
                    'notes' => $itemData['notes'],
                ]);
            }
    
            return (new MarketlistDoneResource($marketlist))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marketlist  $marketlist
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // abort if gate denies
        $marketlist = Marketlist::where('id', $id)->first();
        return new MarketlistDoneResource($marketlist->load(['bu','dept','user','items', 'site']));
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
            'data' => new MarketlistDoneResource($marketlist->load(['bu','dept','user','items'])),
            'meta' => [
                'bu'   => Bu::get(['id', 'name']),
                'dept'   => Dept::get(['id', 'name']),
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
        //
        // return response()->json($request->all());

        if($marketlist->status === 'user_acc') {
            foreach($request->items as $item) {
                MarketlistOrderItem::where('id', $item['id'])->update(['approved_qty' => $item['approved_qty'], 'approved_date' => $item['approved_date']]);
            }
            if($request->isClosed = 1) {
                $marketlist->update(['status' => 'selesai']);
            }            
        }
        if($marketlist->status === 'purchasing_ml_2') {
            $marketlist->update(['status' => 'user_acc']);
        }
        if($marketlist->status === 'purchasing_ml_1') {
            $marketlist->update(['status' => 'purchasing_ml_2']);
        }

        return new MarketlistDoneResource($marketlist->load(['user', 'dept', 'bu', 'items']));

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
    }

    protected function generateCode($deptCode, $buCode, $createdAt)
    {
        $count = Marketlist::whereYear('created_at', $createdAt)
            ->whereMonth('created_at', $createdAt)
            ->count();
    
        $number = str_pad($count + 1, 3, "0", STR_PAD_LEFT);
        $dateCode = substr($createdAt, 2, 2) . substr($createdAt, 5, 2);
    
        return $buCode . $deptCode . $dateCode . $number;
    }

    public function approveData(Request $request)
    {
        return response()->json($request->all());
        $marketlist = Marketlist::where('id', $request->id)->first();

        // foreach($request->items as $item) {
        //     MarketlistItem::where('id', $item['id'])->update(['name' =>$item['name'], 'merk' => $item['merk'], 'spesifikasi' => $item['spesifikasi'], 'qty' => $item['qty'], 'satuan' => $item['satuan'], 'pkab_id' => $pkabItem->id]);
        // }

        if($marketlist->status === 'purchasing_ml_3') {
            $marketlist->update(['status' => 'selesai']);
        }
        if($marketlist->status === 'purchasing_ml_2') {
            $marketlist->update(['status' => 'purchasing_ml_3']);
        }
        if($marketlist->status === 'purchasing_ml_1') {
            $marketlist->update(['status' => 'purchasing_ml_2']);
        }

        return new MarketlistDoneResource($marketlist->load(['user', 'dept.bu','items', 'statusHistory.user']));
    }

    public function rejectData(Request $request)
    {
        $marketlist = Marketlist::where('id', $request->id)->first();
        
        $marketlist->update(['status' => 'cancel']);

        return new MarketlistDoneResource($marketlist->load(['user', 'dept', 'bu', 'items']));
    }
}
