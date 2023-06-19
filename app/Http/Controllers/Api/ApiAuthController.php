<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class ApiAuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'path' => null
        ]);


        $token = $user->createToken('ApiAuth')->accessToken;

        return response()->json(['token' => $token], 200);
    }

    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($data)) {
            $token = Auth::user()->createToken('ApiAuth')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function showUsers()
    {

        $users = User::all();
        return response()->json(['data' => $users], 200);
    }

    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $path = null;
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        if ($request->hasFile('photo')) {
            $path = $this->getImagePath($request);
        } 

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'path' => $path
        ]);
        $token = $user->createToken('ApiAuth')->accessToken;
        return response()->json(['success' => 'User created successfully'], 200);
    }

    public function getImagePath($request)
    {
        $allowedfileExtension = ['jpg', 'png', 'jpeg'];
        $file = $request->file('photo');
        $extension = $file->getClientOriginalExtension();
        $check = in_array($extension, $allowedfileExtension);
        if ($check) {
            $path = $file->store('public/images');
            return $path;
        } else {
            return response()->json(['error' => 'Invalid file format'], 422);
        }
    }

    public function updateUser(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'first_name' => ['string', 'max:255', 'nullable'],
            'last_name' => ['string', 'max:255', 'nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user = User::find($id);
        if ($user === null) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $path = null;
        if ($request->hasFile('photo')) {
            $path = $this->getImagePath($request);
            $user->path = $path;
        } 
   
        $user->first_name = isset($request->first_name) ? $request->first_name : $user->first_name;
        $user->last_name = isset($request->last_name) ? $request->last_name : $user->last_name;
        $user->save();
        return response()->json(['success' => 'User updated successfully'], 200);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if ($user === null) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if ($user->delete() === false) {
            return response()->json(['error' => 'Could not delete this user'], 403);
        } else {
            return response()->json(['success' => 'Record deleted successfully'], 200);
        }
    }
}
