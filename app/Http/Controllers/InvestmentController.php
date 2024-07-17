<?php

namespace App\Http\Controllers;

use App\Events\UserBalance;
use App\Models\Investment;
use Bootstrap\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class InvestmentController extends Controller
{
    public $user;
    public function __construct() {
        $this->user = Auth::user();
    }
    public function index(Request $request)
    {

        $investments = [];
        $filterDate =  $request->query('filter-date-investment');



        $investmentsQuery = Investment::when(!is_null($filterDate) && $filterDate !== '', function ($query) use ($filterDate) {
            if (strpos($filterDate, ' to ') !== false) {
                list($startDate, $endDate) = explode(' to ', $filterDate);
                $query->whereBetween('date', [$startDate, $endDate]);
            } else {
                $query->where('date', $filterDate);
            }
        });
        $investments = $investmentsQuery->get();
        return view('investment', compact('investments'));
    }


    public function add(Request $request)
    {

        $validated = $request->validate([
            'name' => 'nullable',
            'amount' => 'required',
            'date' => 'nullable',
            'detail' => 'nullable',
        ]);

        empty($validated['date']) && $validated['date'] = Helper::currentDateTime();
        $investment = Investment::create($validated);
        if (!$investment) return response()->json(['status' => false, 'alertType' => 'error', 'msg' => 'could not add investment!']);
        try { 
            $balance = Event::dispatch(new UserBalance([
                'user_id' => '1',
                'amount' => $validated['amount'],
                'action' => '+',
                'purpose' =>'Investment'
            ]));   
        } catch (\Exception  $err) {
            $investment->delete();
            return response()->json(['status'=>false, 'alertType'=>'error', 'msg'=>$err->getMessage()]);
        }
        return response()->json([
            'status' => true,
            'alertType' => 'success',
            'msg' => 'Investemnt added successfully.',
            'balance' => $balance
        ]);
    }

    public function delete(Request $request)
    {
        $delRecords = $request->delRecords;
        DB::beginTransaction();
        try {
            foreach ($delRecords as  $id) {
                $investment = Investment::find($id);
                if ($investment) {
                    $amount = $investment->amount;
                    $investment->delete();
                    Event::dispatch(new UserBalance([
                        'user_id' => Auth::user()->id,
                        'amount' => $amount,
                        'action' => '-',
                        'purpose' =>'Investment'
                    ]));
                } 
            }

            DB::commit();
        } catch (\Exception $err) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'alertType' => 'error',
                'msg' => $err->getMessage()
            ]);
        }

        return response()->json([
            'status' => true,
            'alertType' => 'success',
            'msg' => 'Investment deleted successfully.'
        ]);
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        if (!$id) return;
        $data = Investment::find($id);

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function update(Request $request)
    {

        $id = $request->investmentid;
        $validated = $request->validate([
            'name' => 'nullable',
            'amount' => 'required',
            'date' => 'nullable',
            'detail' => 'nullable',
        ]);
        if(empty($validated['date'])) $validated['date'] = Helper::currentDateTime();
        $investment = Investment::find($id);
        $amount = $investment->amount; 
        DB::beginTransaction();
        try {
            $investment = $investment->update($validated);
            if (!$investment) return response(['status' => false, 'alertType' => 'error', 'msg' => 'Could not update Investment!'], 500);
            Event::dispatch(new UserBalance([
                'user_id'=>Auth::user()->id,
                'amount'=>$amount,
                'action'=>'-',
                'purpose' =>'Investment'
            ]));
            Event::dispatch(new UserBalance([
                'user_id'=>Auth::user()->id,
                'amount'=>$validated['amount'],
                'action'=>'+',
                'purpose' =>'Investment'
            ]));
            DB::commit();
        } catch (\Exception $err) {
            DB::rollBack();
            return response(['status' => false, 'alertType' => 'error', 'msg' => $err->getMessage()], 500);
        }

        return response(['status' => true, 'alertType' => 'success', 'msg' => 'Investment updated succesfully!'], 200);
    }
}
