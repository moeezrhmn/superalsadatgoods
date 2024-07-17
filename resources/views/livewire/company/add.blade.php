<div>
    <div class="row">
        <div id="flRegistrationForm" class="col-lg-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4> Add Company </h4>
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
                        <div class="form-group mb-4">
                            <label for="name">Name </label>
                            <input type="text" class="form-control" id="name" wire:model.lazy='name'>
                            @error('name') <span class="text-danger"> {{ $message }}</span> @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label for="contact">Contact </label>
                            <input type="tel" class="form-control" wire:model.lazy='contact' id="contact">
                            @error('contact') <span class="text-danger error"> {{ $message }}</span> @enderror
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" wire:model='status'  class="custom-control-input" id="customCheck1">
                            <label class="custom-control-label" for="customCheck1">Status</label>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Submit</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>