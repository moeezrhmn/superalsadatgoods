<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contract;
use App\Models\Expense;
use App\Models\Investment;
use Carbon\Carbon;
use App\Http\Controllers\ContractController;
use App\Models\Balance;
use Barryvdh\DomPDF\Facade\Pdf;
use Bootstrap\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashbaordController extends Controller 
{
    public $user;
    public $ContractController;
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ContractController $ContractController)
    {
        $this->middleware('auth');
        $this->ContractController = $ContractController;
    }
    
    public function index()
    {
        $this->user = Auth::user();
        $user = $this->user;
        $calculations = [];
        $dailyExpenses = Helper::getExpense(null,false);
        $dailyContracts = Helper::getContracts(null, false);
        $dailyReport = $this->ContractController->report($dailyContracts, $dailyExpenses);

        $investments = $this->get_investment();
        $expenses = Expense::with('category')->orderBy('date','desc');
        $report = $this->ContractController->report(null, $expenses);
        // dd($report);
        $expenses = $expenses->get()->toArray();
        $companies = $this->get_companies();
        $contracts = $report['total_contracts'];
        
        $calculations['balance'] = $this->get_balance();

        $calculations['today']['approved_profit'] =$dailyReport['calculations']['contracts']['approved']['profit'];
        $calculations['today']['pending_profit'] =$dailyReport['calculations']['contracts']['pending']['profit'];
        $calculations['today']['current_invested'] =$dailyReport['calculations']['contracts']['pending']['approved_purchase'];
        $calculations['today']['expenses'] =$dailyReport['calculations']['expenses']['amount'];

        $calculations['approved_profit'] = $report['calculations']['contracts']['approved']['profit'];
        $calculations['pending_profit'] = $report['calculations']['contracts']['pending']['profit'];
        $calculations['current_invested'] = $report['calculations']['contracts']['pending']['approved_purchase'];
        $calculations['expenses'] = $report['calculations']['expenses']['amount'];

        // dd($calculations);
        return view('index', compact(
            "expenses",
            "investments",
            "contracts",
            "companies",
            "report",
            "dailyReport",
            "calculations",
            "user"
        ));
    }



    public function get_investment()
    {
        $investment_record = Investment::all();
        $total_investment = Investment::sum('amount');
        $today_investment = Investment::whereYear('date', Carbon::now()->year)
        ->whereMonth('date', Carbon::now()->month)
        ->whereDay('date', Carbon::now()->day)->sum('amount');
        $monthly_investment = Investment::whereYear('date', Carbon::now()->year)
        ->whereMonth('date', Carbon::now()->month)->sum('amount');
        $investments = [
            "investment_record" => $investment_record,
            "total_investment" => $total_investment,
            "today_investment" => $today_investment,
            "monthly_investment" => $monthly_investment,
        ];
        return $investments;
    }
    public function get_balance()
    {
        $user = Auth::user();
        return Balance::where('user_id', $user->id)->sum('amount');
    }

 
    public function get_expenses()
    {
        $totalExpense = Expense::sum('amount');
        $todayTotalExpense =  Expense::where('date', Carbon::today()->toDateString())->sum('amount');

        $expense = [
            "totalExpense" => $totalExpense,
            "todayTotalExpense" => $todayTotalExpense
        ];

        return $expense;
    }

    public function get_contracts()
    {
        $allContracts = Contract::all();

        $todayAllContracts = Contract::where("date", Carbon::today()->toDateString())->get();

        // PENDING CONTRACTS
        $pendingContracts = Contract::where("status", "=", false)->get();

        $todayPendingContracts = Contract::where('date', Carbon::today()->toDateString())->where("status", "=", false)->get();

        // FREIGHT PENDING CONTRACTS
        $freightPendingContracts = Contract::where("freight_paid", "=", false)->where("status", "!=", null)->get();

        $todayFreightPendingContracts = Contract::where('date', Carbon::today()->toDateString())->where("freight_paid", "=", false)->where("status", "!=", null)->get();

        // APPROVED CONTRACTS
        $approvedContracts = Contract::where("status", "=", true)->get();

        $todayApprovedContracts = Contract::where('date', Carbon::today()->toDateString())->where("status", "=", true)->get();

        // CANCELED CONTRACTS
        $canceledContracts = Contract::where("status", "=", null)->get();

        $todayCanceledContracts = Contract::where('date', Carbon::today()->toDateString())->where("status", "=", null)->get();

        $contracts = [
            "allContracts" => $allContracts,
            "todayAllContracts" => $todayAllContracts,
            "pendingContracts" => $pendingContracts,
            "todayPendingContracts" => $todayPendingContracts,
            "approvedContracts" => $approvedContracts,
            "todayApprovedContracts" => $todayApprovedContracts,
            "canceledContracts" => $canceledContracts,
            "todayCanceledContracts" => $todayCanceledContracts,
            "freightPendingContracts" => $freightPendingContracts,
            "todayFreightPendingContracts" => $todayFreightPendingContracts,
        ];

        return $contracts;
    }

    public function get_companies()
    {
        $active_companies = Company::where('status', '=', true)->get();
        $inactive_companies = Company::where('status', '=', false)->get();
        $companies = [
            "active_companies" => $active_companies,
            "inactive_companies" => $inactive_companies
        ];
        return $companies;
    }

    public function generatePDF(Request $request)
    {   
        $data = $request->input('report');
        $filters = $request->input('filters');
        $contracts = $request->input('contracts');
        $daily = $request->input('daily');
        if(!$data) return view('contracts.index'); 
        $data = json_decode($data);
        $filters = json_decode($filters);
        $contracts = json_decode($contracts);
        if(!empty($filters)){
            $company = Company::find($filters->company_id);
            if($company){
                $filters->company_id = $company->name;
            }
        }
        $meta = [ 'title' => 'Contract Report', 'author' => 'Super Al Sadaat Goods Transport' ];
        $pdf = pdf::setPaper('A3', 'portrait');
        $pdf->loadView('pdf.contract_report', compact('data', 'meta', 'filters', 'daily', 'contracts')); 
        $pdf->render();
        return $pdf->stream();
        // return $pdf->download('contractsReport.pdf');
    }
}
