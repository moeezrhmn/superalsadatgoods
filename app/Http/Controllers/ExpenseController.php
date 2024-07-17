<?php

namespace App\Http\Controllers;

use App\Events\UserBalance;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Bootstrap\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Nette\Schema\Expect;

class ExpenseController extends Controller
{
    //
    public function index(Request $request)
    {
        $filters = [
            'expense_category' => $request->expense_category,
            'month'=> $request->month,
            'year'=> $request->year,
            'filter'=>$request->filter
        ];
        if (isset($request->date) && !empty($request->date)) {
            $filters['date'] = $request->date;
        } else {
            if (empty($request->month)) {
                $filters['date'] = '';
            } else {
                $date = Helper::getStartAndEndDateOfMonth($request->month, ($request->year ?? Carbon::now()->year));
                $filters['date'] = "$date[start] to $date[end]";
            }
        }
        if (strpos($filters['date'], ' to ') !== false) {
            list($startDate, $endDate) = explode(' to ', $filters['date']);
            $filters['start'] = $startDate;
            $filters['end'] = $endDate;
        }
        
        $daily['expense'] = Helper::getExpense(null, false, null, true);
        $daily['amount'] = $this->report($daily['expense']);
        $daily['expense'] = $daily['expense']->get()->toArray();

        $expenses['expense'] = $request->filter ?  $this->filter(null, $filters, false) : Helper::getExpense(null, false, null, false);
        $expenses['amount'] = $this->report($expenses['expense']);
        $expenses['expense'] = $expenses['expense']->get()->toArray();
        $category = ExpenseCategory::all();
        return view('expenses', compact('expenses', 'category', 'daily' , 'filters'));
    }
    public function add(Request $request)
    {
        $user_id = Auth::user()->id;
        $validated = $request->validate([
            'expense_category_id' => 'required',
            'amount' => 'required',
            'date' => 'nullable',
            'description' => 'nullable',
        ]);
        if (empty($validated['date'])) $validated['date'] = Helper::currentDateTime();
        $id = $request->input('expense_id');
        if (!empty($id)) {
            $expense = Expense::find($id);
            if (!empty($expense)) {
                DB::beginTransaction();
                try {
                    $amount = $expense->amount;
                    $expense->update($validated);
                    $expense['category'] = $expense->category;
                    Event::dispatch(new UserBalance([
                        'user_id' => $user_id,
                        'amount' => $amount,
                        'action' => '+', // add old subtracted Expense Amount into balance.  
                        "purpose" => "Expense updated  - Old amount added back "
                    ]));
                    Event::dispatch(new UserBalance([
                        'user_id' => $user_id,
                        'amount' => $validated['amount'],
                        'action' => '-', // subtract Expense Amount from balance.  
                        "purpose" => "Expense updated - New amount subtracted"
                    ]));
                    DB::commit();
                    return response(['status' => true, 'msg' => 'Expense Updated successfully.', 'alertType' => 'success', 'data' => $expense], 200);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return response(['status' => false, 'msg' => $th->getMessage(), 'alertType' => 'error'], 500);
                }
            }
        }
        $expense = Expense::create($validated);
        $expense['category'] = $expense->category;
        if (empty($expense)) return response(['status' => false, 'alertType' => 'error', 'msg' => 'Could not add expense!'], 500);
        try {
            Event::dispatch(new UserBalance([
                'user_id' => $user_id,
                'amount' => $validated['amount'],
                'action' => '-', // subtract expense amount from balance
                "purpose" => "Expense"
            ]));
            return response(['status' => true, 'alertType' => 'success', 'msg' => 'Expense added successfully!', 'data' => $expense], 200);
        } catch (\Exception $err) {
            $expense->delete();
            return response(['status' => false, 'alertType' => 'error', 'msg' => $err->getMessage()], 500);
        }
    }
    public function edit(Request $request)
    {
        $id = $request->id;
        if (empty($id)) return response(['status' => false, 'msg', 'Expense id not found! Required', 'alertType' => 'error'], 422);
        try {
            $expense = Expense::with('category')->find($id);
            return response(['status' => true, 'msg' => 'Expense found successfully.', 'data' => $expense, 'alertType' => 'success']);
        } catch (\Throwable $th) {
            return response(['status' => false, 'msg', $th->getMessage(), 'alertType' => 'error'], 500);
        }
    }
    public function delete(Request $request)
    {
        $id = $request->id;
        if (empty($id)) return response(['status' => false, 'msg' => 'Expense id not found!'], 422);
        $expense = Expense::find($id);
        DB::beginTransaction();
        try {
            $expense->delete();
            Event::dispatch(new UserBalance([
                'user_id' => Auth::user()->id,
                'amount' => $expense->amount,
                'action' => '+',
                "purpose" => "Expense Deleted "
            ]));
            DB::commit();
            return response(['status' => true, 'msg' => 'Deleted successfully.'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response(['status' => false, 'msg' => $th->getMessage()], 500);
        }
    }

    public function categoryAdd(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required',
            'detail' => 'nullable',
        ]);
        $category_id = $request->input('category_id');
        if (!empty($category_id)) {
            $category = ExpenseCategory::where('id', $category_id);
            if (empty($category)) $category->create($validated);
            $category->update($validated);
            return response(['status' => true, 'alertType' => 'success', 'msg' => 'Category updated successfully.'], 200);
        }
        $expenseCat = ExpenseCategory::where('name', $request->name)->first();
        if ($expenseCat) return response(['status' => false, 'alertType' => 'error', 'msg' => "This $request->catname category already exist!  "], 500);

        $expenseCat = ExpenseCategory::create($validated);
        if ($expenseCat) return response(['status' => true, 'alertType' => 'success', 'msg' => 'Category added successfully.'], 200);
    }

    public function categoryEdit(Request $request)
    {
        $id = $request->id;
        if (empty($id)) return response(['status' => false, 'alertType' => 'error', 'msg' => 'Param id is required!'], 500);
        try {
            $category = ExpenseCategory::find($id);
            return response(['status' => true, 'alertType' => 'success', 'msg' => 'Category sucessfully found.', 'data' => $category], 200);
        } catch (\Exception $err) {
            return response(['status' => false, 'alertType' => 'error', 'msg' => $err], 500);
        }
    }

    public function filter($query = null, $filters = [], $result = true)
    {
        $query =  empty($query) ?  Expense::with('category')->orderBy('date', 'desc') : clone $query;
        $query->when(isset($filters['expense_category']) && !empty($filters['expense_category']), function ($query) use ($filters) {
            $query->where('expense_category_id', $filters['expense_category']);
        })->when(isset($filters['date']) && !empty($filters['date']), function ($query) use ($filters) {
            if (strpos($filters['date'], ' to ') !== false) {
                list($start, $end) = explode(' to ', $filters['date']);
                $query->whereBetween('date', [$start, $end]);
            } else {
                $query->where('date', $filters['date']);
            }
        });
        if ($result) {
            return $query->get()->toArray();
        } else {
            return $query;
        }
    }
    public function report($query = null)
    {   
        $expense = empty($query) ? Expense::query() : clone $query;
        return $expense->sum('amount');
    }
}
