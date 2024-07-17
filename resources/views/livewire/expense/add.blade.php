<div class="row">
    <div id="flRegistrationForm" class="col-lg-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4 class="capitalize"> {{ $formType }} Expense </h4>
                    </div>
                    <div class="col">
                        @if(session()->has('message'))
                        <x-alert type="success" :message="session('message')" />
                        @endif
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form wire:submit.prevent='add'>
                    <div wire:ignore class="form-group mb-4">
                        <label for="name">Name </label>
                        <select wire:model='expense_category_id' id="expense_category_id_select"  class="form-control form-small">
                            <option value="">Select Expense Category</option>
                            @foreach ($expenseCategories as $category)
                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                            @endforeach
                        </select>
                        @error('expense_category_id') <span class="text-danger"> {{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mb-4">
                        <label for="amount">Amount </label>
                        <input type="number" class="form-control" wire:model='amount' id="amount">
                        @error('amount') <span class="text-danger error"> {{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mb-4">
                        <label for="basicFlatpickr">Date </label>
                        <input id="basicFlatpickr" wire:model='date'
                            class="form-control flatpickr flatpickr-input active" type="text"
                            placeholder="Select Date..">
                        @error('date') <span class="text-danger"> {{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="description">Description </label>
                        <textarea class="form-control" id="description" wire:model='description' rows="3"></textarea>
                        @error('description') <span class="text-danger"> {{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Submit</button>
                </form>

            </div>
        </div>
    </div>
    @push('expenseaddscripts')

    <script>
        $(document).ready(()=>{
            $('.expense_page #expense_category_id_select').select2({tags: true, });
            $('.expense_page #expense_category_id_select').on('change', function () {
                var expenseCategoryId = $('.expense_page #expense_category_id_select').select2('val');
                @this.set('expense_category_id',expenseCategoryId)
            });
        });
        
    </script>
    @endpush
</div>