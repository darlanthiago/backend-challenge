<?php

namespace App\Listeners;

use App\Events\NotificationTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class CreateTransactionListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {

        try {
            $reponse =  Http::retry(3, 100)->get('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');

            if ($reponse->successful()) {

                $transaction = Transaction::find($event->transaction->id);

                $transaction->status = 'approved';

                //Payer Wallet

                $walletPayer = Wallet::where('user_id', $event->transaction->user_id)->first();

                if ($walletPayer->value < $event->transaction->value) {
                    throw new Exception('Value on wallet is less than on value to send');
                }

                $walletPayer->value = $walletPayer->value - $event->transaction->value;
                $walletPayer->saveOrFail();


                //Payeer Wallet

                $walletPayeer = Wallet::where('user_id', $event->transaction->payeer_id)->first();
                $walletPayeer->value = $walletPayeer->value + $event->transaction->value;
                $walletPayeer->saveOrFail();


                $transaction->saveOrFail();

                // Notificação de sucesso

                $payeer = User::find($event->transaction->payeer_id);

                event(new NotificationTransaction($transaction, $payeer));

                Log::debug('Sucesso');
            }
        } catch (\Throwable $th) {

            $transaction = Transaction::find($event->transaction->id);

            $transaction->status = 'failed';

            $transaction->saveOrFail();

            // Notificação de Falha

            Log::debug('falha');
        }
    }
}
