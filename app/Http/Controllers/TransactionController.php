<?php

namespace App\Http\Controllers;

use App\Events\CreateTransactionEvent;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'value' => 'required|numeric|min:0.01',
            'payeer_id' =>  'required|exists:users,id',
        ]);

        if ($request->payeer_id === $request->user()->id) {
            return response()->json(['message' => 'Payer and Payeer are equals'], 422);
        }

        $walletPayer = Wallet::where('user_id', $request->user()->id)->first();

        if ($walletPayer->value < $request->value) {
            return response()->json(['message' => 'Value on wallet is less than on value to send'], 422);
        }

        $trasanction = new Transaction();
        $trasanction->code = Str::uuid();
        $trasanction->value = $request->value;
        $trasanction->user_id = $request->user()->id;
        $trasanction->payeer_id = $request->payeer_id;

        $trasanction->saveOrFail();

        $trasanction = Transaction::with(['user', 'payeer'])->find($trasanction->id);

        event(new CreateTransactionEvent($trasanction));

        return response()->json($trasanction);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
