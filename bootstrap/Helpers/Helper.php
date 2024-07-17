<?php

namespace Bootstrap\Helpers;

use App\Models\Contract;
use App\Models\Expense;
use Carbon\Carbon;

class Helper
{


  /**
   * @param $filter Array
   * @param $result Boolean
   * @param $query Object
   * @return query | Array
   */
  static function getExpense($filter = null,  $result = true, $query = null, $daily = true)
  {
    if (empty($query)) {
      $query = Expense::with('category')->orderBy('date', 'desc');
    } else {
      $query = clone $query;
    }
    if ($filter && isset($filter['start']) && isset($filter['end']) ) {
      $query->whereBetween('date', [$filter['start'], $filter['end']]);
    } else {
      $query->whereYear('date', Carbon::now()->year)
        ->whereMonth('date', Carbon::now()->month)
        ->when($daily, function ($query) {
          $query->whereDay('date', Carbon::now()->day);
        });
    }
    if ($result) {
      return $query->get()->toArray();
    } else {
      return $query;
    }
  }
  static function getContracts($day = null, $result = true, $query = null , $daily = true)
  {
    if(empty($query)) $query = Contract::with('company')->orderBy('date', 'desc');
    if ($day) {
      $startOfDay = Carbon::parse($day)->startOfDay();
      $endOfDay = Carbon::parse($day)->endOfDay();
      $query->whereBetween('date', [$startOfDay, $endOfDay]);
    } else {
      $query->whereYear('date', Carbon::now()->year)
        ->whereMonth('date', Carbon::now()->month)
        ->when($daily , function ($query){
          $query->whereDay('date', Carbon::now()->day);
        });
    }
    if ($result) {
      return $query->get()->toArray();
    } else {
      return $query;
    }
  }

  static public function getStartAndEndDateOfMonth($month, $year)
  {
    if (empty($month) || empty($year)) return;
    if (is_numeric($month)) {
      $monthNumber = $month;
    } else {
      $monthNumber = Carbon::parse("1 $month")->month;
    }
    $startDate = Carbon::createFromDate($year, $monthNumber, 1)->startOfMonth()->toDateString();
    $endDate = Carbon::createFromDate($year, $monthNumber, 1)->endOfMonth()->toDateString();
     // Remove time component from dates
     $startDate = Carbon::parse($startDate)->startOfDay()->toDateString();
     $endDate = Carbon::parse($endDate)->endOfDay()->toDateString();
    return [
      'start' => $startDate,
      'end' => $endDate
    ];
  }

  static public function currentDateTime(){
    return Carbon::createFromTimestamp(Carbon::now()->timestamp)->format('Y-m-d H:i:s');
  }
}
