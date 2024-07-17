<div class="table-responsive" x-data="{}">
    <style>
        ul li {
            list-style: none;
        }

        .table-controls>li>a svg {
            height: 28px;
            width: 28px;
        }

        table .form-control {
            background: #1b2e4b14;
            padding: 0 0.2rem;
            height: calc(1em + 1rem + 5px);
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>

    <div class="row">
        <div class="col">
            @if(session()->has('message'))
            <x-alert type="{{session('message.type')}}" :message="session('message.content')" />
            @endif
        </div>
    </div>
    <div class="row my-3">
        <div class="col">
            <div class="text-search">
                <input type="text" style="max-width:150px; height:35px; background: transparent; "
                    class="form-control ml-auto " wire:model.live="search" placeholder="Search here">
            </div>
        </div>
    </div>
    <table class="table mb-4">

        <thead>
            <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach( $companiesData as $index=>$company )
            <tr wire:key="{{ $index }}">
                <td class="text-primary">
                    @if($editCompanyIndex === $index || $editCompanyField === $index. '.name' )
                    <input type="text"
                        @click.away="$wire.editCompanyField === '{{$index}}.name' ? $wire.saveCompanyUpdate({{ $index }}) : null "
                        id="name" class="form-control" name="name" wire:model.defer="companies.{{ $index }}.name">

                    @if($errors->has('companies.'.$index.'.name') )
                    <span class="  text-danger"> {{ $errors->first('companies.'.$index.'.name') }}</span>
                    @endif

                    @else
                    <span class="cursor-pointer" wire:click="editCompanyField({{$index}} , 'name')"> {{ $company['name']
                        }} </span>

                    @endif
                </td>
                <td>
                    @if($editCompanyIndex === $index || $editCompanyField === $index. '.contact' )
                    <input type="tel"
                        @click.away="$wire.editCompanyField === '{{$index}}.contact' ? $wire.saveCompanyUpdate({{ $index }}) : null "
                        id="contact" class="form-control" name="contact"
                        wire:model.defer="companies.{{ $index }}.contact">
                    @else
                    <span class="cursor-pointer" wire:click="editCompanyField({{$index}} , 'contact')"> {{
                        $company['contact'] == '' ? '--' : $company['contact'] }} </span>
                    @endif
                </td>
                <td>
                    <label class="switch s-icons  s-outline s-outline-success ">
                        <input type="checkbox" wire:change="updateCompanyStatus({{$index}})"
                            wire:model.defer="companies.{{$index}}.status">
                        <span class="slider round"></span>
                    </label>
                </td>
                <td>
                    @if( $editCompanyIndex !== null && $editCompanyIndex === $index || $editCompanyField === $index.
                    '.name' || $editCompanyField === $index. '.contact' )
                    <ul class="table-controls d-flex  " style="justify-content: space-between;">
                        <li>
                            <button wire:click="cancelEditCompany()"
                                class="btn btn-sm btn-outline-warning btn-rounded mb-2">cancel</button>
                        </li>
                        <li>
                            <button wire:click="saveCompanyUpdate({{ $index }})"
                                class="btn btn-sm btn-outline-primary btn-rounded mb-2">save</button>
                        </li>
                        @else
                        <ul class="table-controls d-flex  " style="justify-content: space-evenly;">
                            <li><a href="javascript:void(0);" wire:click="editCompany({{ $index }})" class="bs-tooltip"
                                    data-toggle="modal" data-target="#expenseEditModel" data-toggle="tooltip"
                                    data-placement="top" title="" data-original-title="Edit"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-edit-2 p-1 br-6 mb-1">
                                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                    </svg></a></li>
                            <!-- <li><a href="javascript:void(0);" wire:click="deleteCompanyIndex({{$index}})"
                                    data-toggle="modal" data-target="#deleteConfirm" class="bs-tooltip"
                                    data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete">

                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-trash p-1 br-6 mb-1">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path
                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                        </path>
                                    </svg>

                                </a></li> -->
                        </ul>
                        @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row mt-2">
        <div class="col">
            <div class="pagination-contracts">
                {{ $companiesData->links() }}
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div wire:ignore class="modal fade profile-modal" id="deleteConfirm" tabindex="-1" role="dialog"
        aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content" style="background: #060818">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="modal-body text-center">
                    <h4 class="mt-2">Do you want to delete this?</h4>
                </div>
                <div class="modal-footer justify-content-center mb-4">
                    <button type="button" class=" btn btn-secondary " data-dismiss="modal" aria-label="Close"
                        class="btn">No</button>
                    <button type="button" wire:click="deleteCompany()" class=" btn btn-danger"
                        data-dismiss="modal" aria-label="Close" class="btn">Yes</button>
                </div>
            </div>
        </div>
    </div>

</div>