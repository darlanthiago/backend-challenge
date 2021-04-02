<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
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
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|max:15',
            'document' => 'required|string|unique:users,document|min:11|max:18',
            'user_type' => 'in:user,store'
        ]);

        $user = new User();
        $user->email = $request->email;
        $user->name = $request->name;
        $user->user_type = $request->user_type;
        $user->code = Str::uuid();
        $user->password = bcrypt($request->password);

        $document = $this->clean_document($request->document);
        $user->document = $document;

        $user->saveOrFail();

        $wallet = new Wallet();
        $wallet->user_id = $user->id;
        $wallet->saveOrFail();

        return response()->json($user, 201);
    }


    protected function clean_document($value)
    {
        $value = trim($value);
        $value = str_replace(".", "", $value);
        $value = str_replace(",", "", $value);
        $value = str_replace("-", "", $value);
        $value = str_replace("/", "", $value);
        return $value;
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
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function me(Request $request)
    {
        return $request->user();
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
        $user = User::where('id', $id)->first();

        if (empty($user)) {
            return response()->json(["error" => "User Not Found"], 404);
        }

        $user->delete();

        return response()->json(["message" => "User successfull delete"], 204);
    }
}
