<?php

namespace App\Http\Livewire\Company;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;

class View extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $editCompanyIndex = null;
    public $editCompanyField = null;
    public $deleteCompanyIndex = null;
    public $companies = [];

    protected $rules = [
        "companies.*.name" => "required",
        "companies.*.contact" => "nullable",
        "companies.*.status" => "boolean",
    ];

    protected $validationAttributes = [
        "companies.*.name" => "name",
        "companies.*.contact" => "contact",
        "companies.*.status" => "status",
    ];

    public function editCompany($companyIndex)
    {
        $this->editCompanyIndex = $companyIndex;
    }

    public function cancelEditCompany()
    {
        $this->editCompanyIndex = null;
        $this->editCompanyField = null;
    }


    public function editCompanyField($editCompanyIndex, $fieldName)
    {
        $this->editCompanyField = $editCompanyIndex . '.' . $fieldName;
    }


    public function saveCompanyUpdate($index)
    {
        $this->validate();

        $company = $this->companies[$index] ?? NULL;
        if (!is_null($company)) {
            $company = optional(Company::find($company['id']))->update($company);
        }
        $this->editCompanyIndex = null;
        $this->editCompanyField = null;
        if ($company) {
            session()->flash('message', ['type' => 'success', 'content' =>  'Company updated successfully.']);
        } else {
            session()->flash('message', ['type' => 'danger', 'content' =>  'Failed to update!']);
        }
    }

    public function updateCompanyStatus($index)
    {
        $company = $this->companies[$index] ?? NULL;
        $company = Company::find($company['id']);
        if (!is_null($company)) {
            $company = $company->update([
                'status' => $company->status ? 0 : 1
            ]);

            if ($company) {
                session()->flash('message', ['type' => 'success', 'content' =>  'Company status updated successfully.']);
            } else {
                session()->flash('message', ['type' => 'danger', 'content' =>  'Failed to update status!']);
            }
        }
    }
    public function deleteCompanyIndex($index)
    {
        $this->deleteCompanyIndex = $index;
    }
    public function deleteCompany()
    {
        $index = $this->deleteCompanyIndex;
        $company = $this->companies[$index] ?? NULL;
        $company = Company::find($company['id']);
        if (!is_null($company)) {
            $company =  $company->delete();

            if ($company) {
                session()->flash('message', ['type' => 'success', 'content' => 'Company deleted successfully.']);
            } else {
                session()->flash('message', ['type' => 'danger', 'content' => 'Failed to delete!']);
            }
        }
    }


    public function render()
    {
        $query = '%' . $this->search . '%';

        $companiesData = Company::where('name', 'like', $query)
            ->orWhere('contact', 'like', $query)
            ->paginate(10);
        $this->companies = $companiesData->items();


        return view('livewire.company.view', ['companies' => $this->companies, 'companiesData' => $companiesData]);
    }
}
