<?php

use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashbaordController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\TransactionHistoryController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [DashbaordController::class, 'index'])->middleware("auth")->name('home');
Route::post('/generate-pdf', [DashbaordController::class, 'generatePDF'])->middleware('auth')->name('generatePDF');

Route::get('/home', function(){
    return redirect(route("home"));
})->middleware('auth');

Route::get('/queuejobs', function(){
    $queuejobs = DB::table('jobs')->select('*')->orderBy('created_at','desc')->get();
    return view('queuejobs', compact('queuejobs'));
})->middleware('auth')->name('queuejobs');
Auth::routes();



Route::get('/companies', function(){    
    return view('companies');
})->middleware('auth');
Route::get('/contracts', [ContractController::class, 'index'])->middleware('auth')->name('contracts.index') ;


// Route::get('/contracts', [ContractController::class, 'index'])->middleware('auth')->name('contracts.index') ;

Route::prefix('expenses')->name('expenses.')->controller(ExpenseController::class)->group(function (){
    Route::get('/', [ExpenseController::class, 'index'])->middleware('auth')->name('index') ;
    Route::post('/add/{id?}', [ExpenseController::class, 'add'])->middleware('auth')->name('add') ;
    Route::get('/edit{id}', [ExpenseController::class, 'edit'])->middleware('auth')->name('edit') ;
    Route::delete('/delete/{id}', [ExpenseController::class, 'delete'])->middleware('auth')->name('delete') ;
    //  Categories of expensess
    Route::post('/category/add', [ExpenseController::class, 'categoryAdd'])->middleware('auth')->name('categoryAdd');
    Route::get('/category/edit/{id}', [ExpenseController::class, 'categoryEdit'])->middleware('auth')->name('categoryEdit');
});


Route::prefix('investments')->name('investment.')->controller(InvestmentController::class)->group( function (){
    Route::get('/', 'index')->middleware('auth')->name('index');
    Route::post('/add', 'add')->middleware('auth')->name('add');
    Route::post('/delete', 'delete')->middleware('auth')->name('delete');
    Route::post('/edit', 'edit')->middleware('auth')->name('edit');
    Route::post('/update', 'update')->middleware('auth')->name('update');
});



Route::get("/transactions", [TransactionHistoryController::class, 'index'])->middleware('auth')->name('transactions');
// contarcts api requests
Route::prefix('api/contract')->name('api.contract.')->middleware('auth:sanctum')->controller(ContractController::class)->group(function (){
    Route::get('/get', 'get')->name('get');
    Route::post('/add', 'add')->name('add');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::put('/update/{id}', 'update')->name('update');
    Route::delete('/delete/{id}', 'delete')->name('delete');
    
    Route::post('/status-update/{id}/{status}', 'status_update')->name('statusUpdate');
    Route::post('/purchase-status-update/{id}/{purchase_status}', 'purchase_status_update')->name('purchase_status_update');
    Route::get('/get_all_bilities', 'get_all_bilities')->name('get_all_bilities');
}); 