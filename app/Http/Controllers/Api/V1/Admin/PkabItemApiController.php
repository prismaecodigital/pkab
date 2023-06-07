<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePkabItemRequest;
use App\Http\Requests\UpdatePkabItemRequest;
use App\Http\Resources\Admin\PkabItemResource;
use App\Models\Bu;
use App\Models\Dept;
use App\Models\PkabItem;
use App\Models\Item;
use App\Models\StatusHistory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use App\Exports\PkabItemExport;
use Illuminate\Support\Facades\Http;

class PkabItemApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('pkab_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // $pkabItem = PkabItem::with('dept')->get();
        // foreach ($pkabItem as $pkab) {
        //     $pkab->update(['bu_id' => $pkab->dept->bu_id]);
        // }
                
        return new PkabItemResource(PkabItem::with(['user', 'dept.bu', 'bu'])
                ->advancedFilter()
                ->whereIn('dept_id', auth()->user()->dept()->pluck('dept_id'))
                ->whereNot('status','selesai')->whereNot('status','cancel')->paginate(request('limit', 10)));
        // return new PkabItemResource(PkabItem::with(['user', 'dept.bu'])->advancedFilter()->paginate(request('limit', 10)));
        
    }

    public function store(StorePkabItemRequest $request)
    {
        // return response()->json($request->items);
        $dept = Dept::findOrFail($request->dept_id);
        $bu = Bu::findOrFail($request->bu_id);
    
        $data = $request->only([
            'req_date',
            'ket',
            'status',
            'dept_id'
        ]);
    
        $data['user_id'] = auth()->user()->id;
        $data['created_at'] = Carbon::now();
        $data['code'] = $this->generateCode($dept->code, $bu->code, $data['created_at']);
    
        try {
            $pkabItem = PkabItem::create($data);
            $statusHistory = StatusHistory::create([
                'pkab_id' => $pkabItem->id,
                'status' => $data['status'],
                'user_id' => $data['user_id'],
                'created_at' => $data['created_at'],
            ]);
    
            foreach ($request->items as $itemData) {
                $item = Item::create([
                    'name' => $itemData['name'],
                    'merk' => $itemData['merk'],
                    'spesifikasi' => $itemData['spesifikasi'],
                    'qty' => $itemData['qty'],
                    'pkab_id' => $pkabItem->id,
                ]);
            }
    
            return (new PkabItemResource($pkabItem))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);
    
        } catch (\Exception $e) {
            // Handle the exception and return an error response
            return response()->json(['message' => 'Failed to create PKAB item'], 500);
        }

    }
    
    protected function generateCode($deptCode, $buCode, $createdAt)
    {
        $count = PkabItem::whereYear('created_at', $createdAt)
            ->whereMonth('created_at', $createdAt)
            ->count();
    
        $number = str_pad($count + 1, 3, "0", STR_PAD_LEFT);
        $dateCode = substr($createdAt, 2, 2) . substr($createdAt, 5, 2);
    
        return $buCode . $deptCode . $dateCode . $number;
    }

    public function create()
    {
        abort_if(Gate::denies('pkab_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $depts = auth()->user()->dept()->pluck('bu_id');

        return response([
            'meta' => [
                'bu'   => Bu::whereIn('id', $depts)->get(['id', 'name']),
                'status' => PkabItem::STATUS_SELECT,
            ],
        ]);
    }

    public function show(PkabItem $pkabItem)
    {
        abort_if(Gate::denies('pkab_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PkabItemResource($pkabItem->load(['user', 'dept.bu','items', 'statusHistory.user']));
    }

    public function update(UpdatePkabItemRequest $request, PkabItem $pkabItem)
    {
        foreach($request->items as $item) {
            Item::where('id', $item['id'])->update(['name' =>$item['name'], 'merk' => $item['merk'], 'spesifikasi' => $item['spesifikasi'], 'qty' => $item['qty'], 'pkab_id' => $pkabItem->id]);
        }

        if($pkabItem->status == 'user_acc') {
            $pkabItem->update(['status' => 'selesai']);
        }
        if($pkabItem->status == 'purchasing_acc_3') {
            $pkabItem->update(['status' => 'user_acc']);
        }
        if($pkabItem->status == 'purchasing_acc_2') {
            $pkabItem->update(['status' => 'purchasing_acc_3']);
        }
        if($pkabItem->status == 'purchasing_acc_1') {
            $pkabItem->update(['status' => 'purchasing_acc_2']);
        }
        if($pkabItem->status == 'leader_acc') {
            $pkabItem->update(['status' => 'purchasing_acc_1']);
        }

        $statusHistory = StatusHistory::create(['pkab_id' => $pkabItem->id,'status' => $pkabItem->status, 'user_id' => auth()->user()->id]);

        
        return new PkabItemResource(PkabItem::with(['user', 'dept.bu'])->whereNot('status','selesai')->whereNot('status','cancel')->advancedFilter()
        ->whereIn('dept_id', auth()->user()->dept()->pluck('dept_id'))
        ->whereNot('status','selesai')->whereNot('status','cancel')->paginate(request('limit', 10)));
    }

    public function rejectData(Request $request, PkabItem $pkabItem)
    {
        $pkabItem = PkabItem::where('id', $request->id)->first();
        $pkabItem->update(['status' => 'cancel', 'ket' => $request->ket]);
        $statusHistory = StatusHistory::create(['pkab_id' => $pkabItem->id,'status' => $pkabItem->status, 'user_id' => auth()->user()->id]);
        
        return new PkabItemResource(PkabItem::with(['user', 'dept.bu'])->whereNot('status','selesai')->whereNot('status','cancel')->advancedFilter()
        ->whereIn('dept_id', auth()->user()->dept()->pluck('dept_id'))
        ->whereNot('status','selesai')->whereNot('status','cancel')->paginate(request('limit', 10)));
    }

    public function edit(PkabItem $pkabItem)
    {
        abort_if(Gate::denies('pkab_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return response([
            'data' => new PkabItemResource($pkabItem->load(['dept.bu'])),
            'meta' => [
                'dept'   => Dept::get(['id', 'name']),
                'status' => PkabItem::STATUS_SELECT,
            ],
        ]);
    }

    public function destroy(PkabItem $pkabItem)
    {
        abort_if(Gate::denies('pkab_item_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pkabItem->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
    
    public function approve(PkabItem $pkabItem, Request $request)
    {
        $pkab = new PkabItemResource(PkabItem::findOrFail($request->id));

        abort_if(Gate::denies($pkab->status), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if($pkab->status == 'user_acc') {
            $pkab->update(['status' => 'selesai']);
        }
        if($pkab->status == 'purchasing_acc_3') {
            $pkab->update(['status' => 'user_acc']);
        }
        if($pkab->status == 'purchasing_acc_2') {
            $pkab->update(['status' => 'purchasing_acc_3']);
        }
        if($pkab->status == 'purchasing_acc_1') {
            $pkab->update(['status' => 'purchasing_acc_2']);
        }
        if($pkab->status == 'leader_acc') {
            $pkab->update(['status' => 'purchasing_acc_1']);
        }

        $statusHistory = StatusHistory::create(['pkab_id' => $pkab->id,'status' => $pkab->status, 'user_id' => auth()->user()->id]);

        return new PkabItemResource($pkab->load(['user', 'dept','items']));
    }

    public function reject(Request $request)
    {
        // dd($request);
        $pkab = new PkabItemResource(PkabItem::findOrFail($request->id));
        abort_if(Gate::denies($pkab->status), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $pkab->update(['status' => 'cancel', 'ket' => $request->ket]);

        $statusHistory = StatusHistory::create(['pkab_id' => $pkab->id,'status' => $pkab->status, 'user_id' => auth()->user()->id]);

        return new PkabItemResource($pkab->load(['user', 'dept','items']));
    }

    public function export(PkabItem $pkabItem)
    {
        $pkab = explode(',' , $pkabItem);
         
    }

    public function updateSimesra(Request $request) {
        
    }
}