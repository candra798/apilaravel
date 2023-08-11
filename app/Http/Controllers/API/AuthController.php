<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Facade\FlareClient\Api;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ada kesalahan',
                'data' => $validator->errors()
            ]);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $success['token'] = $user->createToken('auth_token')->plainTextToken;
        $success['name'] = $user->name;

        return response()->json([
            'success' => true,
            'message' => 'Sukses register',
            'data' => $success,

        ]);
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $auth = Auth::user();
            $success['id'] = $auth->id;
            $success['token'] = $auth->createToken('auth_token')->plainTextToken;
            $success['name'] = $auth->name;
            $success['email'] = $auth->email;

            return response()->json([
                'success' => true,
                'message' => 'Login Sukses',
                'data' => $success,

            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Cek email dan password kembali',
                'data' => null
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    }


    // public function update(Request $request)
    // {
    //     $validator = Validator($request->all(), [
    //         'name' => 'required',
    //         'email' => 'required|email',
    //         // 'password' => 'required',
    //         // 'confirm_password' => 'required|same:password'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'ada kesalahan',
    //             'data' => $validator->errors()
    //         ]);
    //     }

    //     $input = $request->all();
    //     // $input['password'] = bcrypt($input['password']);
    //     $user = User::create($input);

    //     //$success['token'] = $user->createToken('auth_token')->plainTextToken;
    //     $success['name'] = $user->name;
    //     $success['email'] = $user->email;

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Sukses edit',
    //         'data' => $success,

    //     ]);
    // }
    // public function update(Request $request, $id)
    // {
    //     // Validasi request sesuai kebutuhan Anda
    //     $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email',
    //     ]);

    //     $data = Auth::user($id);

    //     // Lakukan update data sesuai request
    //     $data->update([
    //         'name' => $request->input('name'),
    //         'email' => $request->input('email'),
    //         // ...
    //     ]);

    //     return response()->json(['message' => 'Data updated successfully']);
    // }

    // public function update(Request $request)
    // {
    //     $validator = Validator($request->all(), [
    //         'name' => 'required',
    //         'email' => 'required|email',
    //         // 'password' => 'required',
    //         // 'confirm_password' => 'required|same:password'
    //     ]);

    //     $validator->update([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //     ]);
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'berhasil di edit',
    //         // 'data' => $success,

    //     ]);
    // }
}
