<?php

namespace App\Listeners;

use App\Events\UserBalance;
use App\Models\Balance;
use App\Models\TransactionHistory;
use App\Models\User;
use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use stdClass;

class UserBalanceUpdate implements ShouldQueue
{

    use InteractsWithQueue;
    /**
     * Handle the event.
     */
    public function handle(UserBalance $event)
    {
        $data = $event->data;
        if (!isset($data['user_id'], $data['amount'], $data['action'])) {
            Log::error('Invalid event data: ' . json_encode($data));
            return;
        }
        $userId = $data['user_id'];
        $user = User::find($userId);
        $amount = $data['amount'];
        $action = $data['action'];
        $purpose = $data['purpose'] ?? 'Unknown';
        $currentMonth = Carbon::now()->format('M Y');
        $description = "Month: $currentMonth  || User: $user->name (id:$userId) || Action: ( $action ) ";
      
        try {
            $userBalance = Balance::firstOrCreate([
                'user_id' => $userId,
                'month' => $currentMonth,
            ]);
            TransactionHistory::create([
                'purpose'=>$purpose, 'description'=>$description,
                'amount' => ($action == '+') ? +$amount : -$amount,
            ]);
          
            if ($action == '+') {
                $userBalance->amount += $amount;
            } elseif ($action == '-') {
                $userBalance->amount -= $amount;
            }
            $userBalance->save();
            return $userBalance;
        } catch (\Throwable $th) {
            Log::error('Failed to update user balance: ' . $th->getMessage());
            throw $th;
        }
    }
}
