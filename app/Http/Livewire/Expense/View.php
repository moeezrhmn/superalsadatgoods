<?php

namespace App\Http\Livewire\Expense;

use App\Models\Expense as Expenses;
use App\Models\ExpenseCategory;
use Livewire\Component;
use Livewire\WithPagination;

class View extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $editExpenseIndex = null;
    public $editExpenseField = null;
    public $deleteExpenseIndex = null;
    public $expenses = [];

    protected $rules = [
        "expenses.*.expense_category_id" => "required",
        "expenses.*.amount" => "required",
        "expenses.*.description" => "nullable",
        "expenses.*.date" => "nullable",
    ];

    protected $validationAttributes = [
        "expenses.*.expense_category_id" => "expense_category_id",
        "expenses.*.amount" => "amount",
        "expenses.*.description" => "description",
        "expenses.*.date" => "date",
    ];

    public function editExpense($expenseIndex)
    {
        $this->editExpenseIndex = $expenseIndex;
    }

    public function cancelEditExpense()
    {
        $this->editExpenseIndex = null;
        $this->editExpenseField = null;
    }


    public function editExpenseField($editExpenseIndex, $fieldName)
    {
        $this->editExpenseField = $editExpenseIndex . '.' . $fieldName;
    }


    public function saveExpenseUpdate($index)
    {
        $this->validate();

        $expense = $this->expenses[$index] ?? NULL;
        if (!is_null($expense)) {
            $expense = optional(Expenses::find($expense['id']))->update($expense);

            if ($expense) {
                session()->flash('message', ['type' => 'success', 'content' =>  'Expense updated successfully.']);
            } else {
                session()->flash('message', ['type' => 'danger', 'content' =>  'Failed to update !']);
            }
        }
        $this->editExpenseIndex = null;
        $this->editExpenseField = null;
    }

  
    public function deleteExpenseIndex($index)
    {
        $this->deleteExpenseIndex = $index;
    }
    public function deleteExpense()
    {
        $index = $this->deleteExpenseIndex;
        $expense = $this->expenses[$index] ?? NULL;
        $expense = Expenses::find($expense['id']);
        if (!is_null($expense)) {
            $expense =  $expense->delete();

            if ($expense) {
                session()->flash('message', ['type' => 'success', 'content' => 'Expense deleted successfully.']);
            } else {
                session()->flash('message', ['type' => 'danger', 'content' => 'Failed to delete!']);
            }
        }
    }


    
    public function render()
    {
        $query = '%' . $this->search . '%';

        $expenseData = Expenses::with('expenseCategories')
        ->orWhere('amount', 'like', $query)
        ->orWhere('date', 'like', $query)
        ->orWhere('description', 'like', $query)
        ->orWhereHas('expenseCategories', function ($expenseCatQuery) use ($query) {
            $expenseCatQuery->where('name', 'like', $query);
        })
        ->paginate(10);
        // $this->expenses = Expenses::all()->toArray();
        $this->expenses = (array) $expenseData->items();

        $expenseCategories = ExpenseCategory::all(); 

        return view('livewire.expense.view', ['expenses' => $this->expenses, 'expenseData' => $expenseData, 'expenseCategories'=>$expenseCategories]);
    }
}
