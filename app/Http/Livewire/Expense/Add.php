<?php

namespace App\Http\Livewire\Expense;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Livewire\Component;

class Add extends Component
{

    public $expense_category_id, $amount, $date, $description;
    public $formType;
    public $expenseCategories = [];
  

    protected $rules = [
        'expense_category_id' => 'required',
        'amount' => 'required',
        'date' => 'nullable',
        'description' => 'nullable',
    ];


    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function add()
    {

        $validated = $this->validate();
        
        $expenseCategoryId = $validated['expense_category_id'];

        if((int) $expenseCategoryId == 0 ){
            ExpenseCategory::create([
                'name'=>$expenseCategoryId
            ]);

            $latestRecord = ExpenseCategory::latest()->first();
            $validated['expense_category_id'] = $latestRecord->id;
        }

        Expense::create($validated);

        $this->expense_category_id = '';
        $this->amount = '';
        $this->date = '';
        $this->description = '';
        $this->emit('refreshView');
        session()->flash('message', 'Expense added successfully.');
    }

    public function hydrate(){
        $this->emit('select2-add-expense');
    }

    public function render()
    {
        $this->expenseCategories = ExpenseCategory::all();
        $expenseCategories = $this->expenseCategories;
        return view('livewire.expense.add', compact('expenseCategories'));
    }
}
