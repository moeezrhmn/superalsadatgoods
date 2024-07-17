<?php

namespace App\Http\Controllers;

use App\Events\UserBalance;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Expense;
use Bootstrap\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use App\Http\Controllers\ExpenseController;

class ContractController extends Controller
{
  public $expenseController;
  /**
   * New contract controller
   *
   * @return void
   */
  public function __construct(ExpenseController $expenseController)
  {
    $this->middleware('auth');
    $this->expenseController = $expenseController;
  }
  public function index()
  {

    $bility = Contract::orderByRaw("CAST(bility AS UNSIGNED)")->pluck('bility');
    $dailyContracts = Helper::getContracts(null, false); 
    $expenseDaily = Helper::getExpense(null, false);
    $reportDaily = $this->report($dailyContracts, $expenseDaily);
    // dd($reportDaily);
    // Montlhy Record
    $expense = Helper::getExpense(null, false, null, false);
    $contracts = Helper::getContracts(null, false, null, false);
    $report = $this->report($contracts, $expense);
    $contracts = $report['total_contracts'];
    // dd($contracts);
    $companies = Company::where('status', '!=', false)->get()->toArray();
    return view('contracts', compact('contracts', 'companies', 'report', 'reportDaily', 'bility'));
  }

  /**
   * Get All Contracts
   * 
   * @return array | object 
   */
  public function get(Request $request)
  {
    $type = $request->type;
    $bility = Contract::orderByRaw("CAST(bility AS UNSIGNED)")->pluck('bility');
    $filters = [
      'company_id'=>$request->company_id,
      'purchase_status'=>$request->purchase_status,
      'status'=>$request->status,
      'month'=> $request->month, 
      'year'=> $request->year,
    ];
    if(isset($request->date)){
      $filters['date'] = $request->date;
    }else{
      if($request->month==''){
        $filters['date'] = '';
      }else{
        $date = Helper::getStartAndEndDateOfMonth($request->month, $request->year);
        $filters['date'] = "$date[start] to $date[end]";
      }
    }
    if (strpos($filters['date'], ' to ') !== false) {
      list($startDate, $endDate) = explode(' to ', $filters['date']);
      $filters['start'] = $startDate;
      $filters['end'] = $endDate;
    } 
    // return response(['data'=>$filters],200);
    if($type=='reset') $filters = [];
    $expense = $this->expenseController->filter(null, $filters, false);
    $contract = $this->filter($filters, null, false);
    $contracts = clone $contract;
    $expenses = clone $expense;
    $expenses = $expenses->get()->toArray();
    $contracts = $contracts->get()->toArray();
    // dd($expenses);
    $report = $this->report($contract, $expense);
    if (is_null($contract) || $contract == '') return response(['msg' => 'Contracts not found!', 'status' => false], 404);
    return response(['contracts' => $contracts, 'expenses'=>$expenses, 'report' => $report, 'filters' => $filters, 'bility'=>$bility, 'status' => true], 200);
  }

  public function filter($filters=null, $query=null, $result = true)
  {
    if(empty($query)) $query = Contract::with('company')->orderBy('date','desc');
    if(empty($filters)){
      return $result ? $query->get()->toArray() : $query;
    }
    $company_id = $filters['company_id'];
    $date = $filters['date'];
    $purchaseStatus = $filters['purchase_status'];
    $status = $filters['status'];

    $query = $query
      ->when(!empty($date), function ($query) use ($date) { // DATE FILTER
        if (strpos($date, ' to ') !== false) {
          list($startDate, $endDate) = explode(' to ', $date);
          $query->whereBetween('date', [$startDate, $endDate]);
        } else {
          $query->where('date', $date);
        }
      })
      ->when(!empty($company_id), function ($query) use ($company_id) { // COMPANY FILTER
        $query->where('company_id', $company_id);
      })
      ->when(!empty($status), function ($query) use ($status) { // STATUS FILTER
        $query->where('status',$status);
      })
      ->when(!empty($purchaseStatus), function ($query) use ($purchaseStatus) { // freight PAID FILTER
        $query->where('purchase_status',$purchaseStatus);
      });
    return $result ? $query->get()->toArray() : $query;
  }

  public function report($contractsQuery=null, $expenseQuery=null)
  {
    empty($contractsQuery) ? $contractsQuery = Contract::with('company')->orderBy('date','desc') : $contractsQuery = clone $contractsQuery;
    empty($expenseQuery) ? $expenseQuery = Expense::orderBy('date','desc') : $expenseQuery = clone $expenseQuery;
    
    $report = [
      'total_contracts'=>null,
      'approved_status' => null,
      'pending_status' => null,
      'approved_purchase' => null,
      'pending_purchase' => null,
    ];
    foreach ($report as $k => $v) {
      $report[$k] = clone $contractsQuery;
    }
    $report = [
      'total_contracts' => $report['total_contracts']->get()->toArray(),
      'approved_status' => $report['approved_status']->where('status', 'approved')->get()->toArray(),
      'pending_status' => $report['pending_status']->where('status','pending')->get()->toArray(),
      'approved_purchase' => $report['approved_purchase']->where('purchase_status', 'approved')->get()->toArray(),
      'pending_purchase' => $report['pending_purchase']->where('purchase_status','pending')->get()->toArray(),
      'calculations'=>[
        'contracts'=>[
          'approved'=>$this->saleCalculations($contractsQuery, 'approved'),
          'pending'=>$this->saleCalculations($contractsQuery),
        ],
        'expenses'=>[
          'amount'=>$expenseQuery->sum('amount')
        ]
      ],
    ];
    return $report;
  }



  public function saleCalculations($query, $status='pending')
  {
    if (empty($query)) return [];
    $query = clone $query;
    $query = $query->where('status',$status);
    $params = array(
      'tax_amount' => null,
      'sale_total' => null,
    );
    foreach ($params as $k => $v) $params[$k] = clone $query;
    $params = [
      'approved_purchase' => $this->purchaseCalculations($query,'approved'),
      'pending_purchase' =>$this->purchaseCalculations($query),
      'tax_amount' => $params['tax_amount']->sum('tax_amount'),
      'sale_total' => $params['sale_total']->sum('sale_total'),
    ];
    $params['total_purchase'] = $params['approved_purchase']['total']+$params['pending_purchase']['total'];
    $params['profit'] = $params['sale_total']-$params['total_purchase']-$params['tax_amount'];
    return $params;
  }
  public function purchaseCalculations($query=null, $purchase_status='pending'){
    if(!$query) return;
    $query = clone $query;
    $query = $query->where('purchase_status',$purchase_status);
    $purchase = ['freight'=>null,'charge_per_day'=>null,'stop_charges'=>null,'labour_charges'=>null,];
    foreach ($purchase as $k => $v) $purchase[$k] = clone $query;
    $purchase = [
      'freight'=>$purchase['freight']->sum('freight'),
      'charge_per_day'=>$purchase['charge_per_day']->sum('charge_per_day'),
      'stop_charges'=>$purchase['stop_charges']->sum('stop_charges'),
      'labour_charges'=>$purchase['labour_charges']->sum('labour_charges'),
    ];
    $purchase['total'] = $purchase['freight']+$purchase['charge_per_day']+$purchase['stop_charges']+$purchase['labour_charges']; 
    return $purchase;
  }


  public function add(Request $request)
  {
    $user_id = Auth::user()->id;
    // dd($request->all());
    $validated = $request->validate([
      'company_id' => 'required',
      'date' => 'nullable',
      'vehicle_number' => 'required',
      'vehicle_name' => 'nullable',
      'bility' => 'required',
      'quantity' => 'nullable',
      'item' => 'nullable',
      'freight' => 'required',
      'purchase_status' => 'required',
      'charge_per_day' => 'nullable',
      'stop_charges' => 'nullable',
      'labour_charges' => 'nullable',
      'purchase_total' => 'required',
      'sale_total' => 'required',
      'tax_percent' => 'nullable',
      'tax_amount' => 'nullable',
      'remarks' => 'nullable',
      'img' => 'nullable',
      'status' => 'required',
    ]); 
    $id = (int)$request->update_contract_id;
    if(!is_numeric($validated['bility'])) return response(['status' =>false, 'msg' => "Bility should be in number!" ], 200);
    if(empty($id)){
      $checkBility = Contract::where('bility', $validated['bility'])->first();
      if(!empty($checkBility)) return response(['status' =>false, 'msg' => "Bility number already exist!" ], 200);
    }
    if(empty($validated['date'])) $validated['date'] = Helper::currentDateTime();
    $imageName = null;
    if ($request->hasFile('img')) {
      $image = $request->file('img');
      $imageName = $image->store('contractImages', 'public');
    }
    if (!empty($request->old_img) && empty($request->hasFile('img'))) {
      $imageName =  $request->old_img;
    }
    $validated['img'] = $imageName;

    if (!empty($id)) { // update old contract
      $contract = Contract::find($id);
      $oldData = [
        'purchase_status' => $contract->purchase_status, 
        'purchase_total' => $contract->purchase_total, 
        'sale_total' => $contract->sale_total, 
        'status' => $contract->status, 
        'tax_amount' => $contract->tax_amount, 
        'bility' => $contract->bility, 
      ];
      DB::beginTransaction();
      try {
        $contract->update($validated);
        $contract['company'] = $contract->company;
        $this->balanceUpdate($validated, $user_id, $oldData);
        DB::commit();
        return response(['status' => true, 'msg' => "Contract saved successfully", 'data'=>$contract], 200);
      } catch (\Throwable $th) {
        DB::rollBack();
        return response(['status' =>false, 'msg' => $th->getMessage() ], 200);
      }
    } else { // create new contract
      $contract = Contract::create($validated);
      if (empty($contract)) return response(['status' => false, 'msg' => "Could not save contract!"], 200);
      try {
        $this->balanceUpdate($validated, $user_id);
      } catch (\Throwable $th) {
        $contract->delete();
        return response(['status' => false, 'msg' => $th->getMessage()], 200);
      }
    }
    if (empty($contract)) return response(['status' => false, 'msg' => "Could not save contract!"], 200);
    $contract['company'] = $contract->company; 
    return response(['status' => true, 'msg' => 'Contract saved successfully.', 'data'=>$contract], 200);
  }
  public function balanceUpdate($validated = null, $user_id = null, $oldData = null)
  {
    if (empty($validated)) return;
    if (empty($validated)) return;
    if ($oldData) { // handling old amounts 
      if ($oldData['purchase_status']=='approved') {
        $purchase = (float) $oldData['purchase_total'];
        Event::dispatch(new UserBalance([
          'user_id' => $user_id,
          'amount' => $purchase,
          'action' => '+', // add old purchase into balance.
          'purpose' =>"Contract updated - New Purchase Status: $validated[purchase_status], Old Purchase Status: $oldData[purchase_status] {bility => $oldData[bility]}"
        ]));
      }
      if ($oldData['status']=='approved') {
        $sale =  (float) $oldData['sale_total'];
        if(!empty($oldData['tax_amount'])) $sale = $sale - ((float)$oldData['tax_amount']);
        Event::dispatch(new UserBalance([
          'user_id' => $user_id,
          'amount' => $sale,
          'action' => '-', // subtract old sale amount from balance.
          'purpose' =>"Contract updated - New Status: $validated[status], Old Status: $oldData[status] (tax:$oldData[tax_amount]) , { bility => $oldData[bility] }"
        ]));
      }  
    }
    
    if ($validated['purchase_status']=='approved') {
      $purchase = (float) $validated['purchase_total'];
      Event::dispatch(new UserBalance([
        'user_id' => $user_id,
        'amount' => $purchase,
        'action' => '-', // subtract purchase from balance.
        'purpose' =>"Contract saved - New purchase status: $validated[purchase_status] , { bility => $validated[bility] }"
      ]));
    }
    if ($validated['status']=='approved') {
      $sale =  (float) $validated['sale_total'];
      if(!empty($validated['tax_amount'])) $sale = $sale - ((float)$validated['tax_amount']); 
      Event::dispatch(new UserBalance([
        'user_id' => $user_id,
        'amount' => $sale,
        'action' => '+', // add sale amount into balance.
        'purpose' =>"Contract saved - New status: $validated[status] (tax:$validated[tax_amount]) , {bility => $validated[bility]}"
      ]));
    }
  }
  public function delete(Request $request){
    $user_id = Auth::user()->id;
    $id = $request->id;
    if (empty($id)) return response(["status" => false, "msg" => "Not found!"], 404);

    $contract = Contract::find($id);
    if (empty($contract)) return response(["status" => false, "msg" => "Contract not found!"], 404);
    
    DB::beginTransaction();
    try {
      if($contract->purchase_status=='approved'){
        $purchase = $contract->purchase_total;
        Event::dispatch(new UserBalance([
          'user_id'=>$user_id,
          'amount'=>$purchase,
          'action'=>'+',
          'purpose' =>"Contract Deleted - purhcase status: $contract->purchase_status ,  { bility: $contract->bility }"
        ]));
      }
      if($contract->status=='approved'){
        $sale = $contract->sale_total;
        if(!empty($contract->tax_amount)) $sale = $sale - ((float)$contract->tax_amount);
        Event::dispatch(new UserBalance([
          'user_id'=>$user_id,
          'amount'=>$sale,
          'action'=>'-',
          'purpose' =>"Contract Deleted - status: $contract->status  ( tax: $contract->tax_amount ), { bility: $contract->bility } "
        ]));
      }
      $contract->delete();
      DB::commit();
      return response(['status' => true, 'id' => $request->id, 'msg' => "Contract deleted successfully!"], 200);
    } catch (\Throwable $th) {
      DB::rollBack();
      return response(['status' => false, 'id' => $request->id, 'msg' => $th->getMessage()], 200);
    }
  }

  public function edit(Request $request)
  {
    $id = $request->id;
    if (empty($id)) return response(['status' => false, "msg" => "Not found!"], 404);


    $contract = Contract::find($id);
    if (empty($contract)) {
      return response(['status' => false, 'msg' => "Contract not found!"], 404);
    }
    return response(['status' => true, "contract" => $contract], 200);
  }

  public function status_update(Request $request)
  {
    $user_id = Auth::user()->id;
    $id  = $request->id;
    $status = $request->status;
    
    if (empty($id) || empty($status)) return  response(['status' => false, 'msg' => 'Invalid inputs!'], 200);
    $contract = Contract::find($id);
    if (empty($contract)) return  response(['status' => false, 'msg' => 'Contract Not found!'], 404);
    $oldstatus = $contract->status;
    DB::beginTransaction();
    try {
      $sale = $contract->sale_total;
      if(!empty($contract->tax_amount)) $sale = $sale - (float)$contract->tax_amount;
      $purpose = "Contract status update - old: $oldstatus , new: $status , {bility => $contract->bility}";
      if($oldstatus=='approved' && $status=='pending'){
        Event::dispatch(new UserBalance([
          'user_id'=>$user_id,
          'amount'=>$sale,
          'action'=>'-',
          'purpose' =>"$purpose (tax:$contract->tax_amount)"
        ]));
      }elseif($oldstatus=='pending' && $status=='approved'){
        Event::dispatch(new UserBalance([
          'user_id'=>$user_id,
          'amount'=>$sale,
          'action'=>'+',
          'purpose' =>"$purpose (tax:$contract->tax_amount)"
        ]));
      }
      $contract->update(['status' => $status]);
      DB::commit();
      return response(['status' => true, 'data' => [$id, $status], "msg" => "Status updated successfully."], 200);
    } catch (\Throwable $th) {
      DB::rollBack();
      return response(['status' => false, 'data' => [$id, $status], "msg" => $th->getMessage()], 200);
    }
  }

  public function purchase_status_update(Request $request)
  {
    $user_id = Auth::user()->id;
    $id = $request->id;
    $purchase_status = $request->purchase_status;
    if (empty($purchase_status) || empty($id)) return  response(['status' => false, 'msg' => 'Invalid inputs!'], 200);

    $contract = Contract::find($id);
    if (empty($contract)) return response(['status' => false, 'msg' => 'Not found!'], 200);
    $old_purchase_status = $contract->purchase_status;
    DB::beginTransaction();
    try {
      $purpose ="Contract purchase status update - old:$old_purchase_status , new:$purchase_status , {bility => $contract->bility}";
      if($old_purchase_status=='pending' && $purchase_status=='approved'){
        Event::dispatch(new UserBalance([
          'user_id'=>$user_id,
          'amount'=>$contract->purchase_total,
          'action'=>'-',
          'purpose' => $purpose
        ]));
      }elseif($old_purchase_status=='approved' && $purchase_status=='pending'){
        Event::dispatch(new UserBalance([
          'user_id'=>$user_id,
          'amount'=>$contract->purchase_total,
          'action'=>'+',
          'purpose' =>$purpose
        ]));
      }
      $contract->update(['purchase_status' => $purchase_status]);
      DB::commit();
      return response(['status' => true, 'data' => [$id, $purchase_status], "msg" => "Purchase Status updated successfully."], 200);      
    } catch (\Throwable $th) {
      DB::rollBack();
      return response(['status' =>false, 'data' => [$id, $purchase_status], "msg" => $th->getMessage()], 200);      
    }

  }
  function getMonthlyRecords($month = null, $result = true)
  {
    $query = Contract::with('company')->orderBy('date', 'desc');

    if ($month) {
      $startOfMonth = Carbon::parse($month)->startOfMonth();
      $endOfMonth = Carbon::parse($month)->endOfMonth();
      $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
    } else {
      $query->whereYear('date', Carbon::now()->year)
        ->whereMonth('date', Carbon::now()->month);
    }
    if ($result) {
      return $query->get()->toArray();
    } else {
      return $query;
    }
  }

  function get_all_bilities(){
    $bility = Contract::orderByRaw("CAST(bility AS UNSIGNED)")->pluck('bility');
    return response(['status'=>true, 'bilities'=>$bility], 200);
  }
 
}
