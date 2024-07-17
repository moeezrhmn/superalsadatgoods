<?php

namespace App\Http\Livewire\Company;

use App\Models\Company;
use Livewire\Component;

class Add extends Component
{

    public $name , $contact, $status = true;
    protected $rules = [
        'name' => 'required',
        'contact' => 'nullable',
        'status' => 'boolean',
        
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function add(){

        $validated = $this->validate();

        Company::create($validated);

        $this->name = '';
        $this->contact = '';
        $this->status = true;
        session()->flash('message', 'Company added successfully.');
    }

    public function render()
    {
        return view('livewire.company.add');
    }
}
