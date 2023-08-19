<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // signUp
    public function signUp(Request $request)
    {$type = 1;

        $request->validate([
            'name' => 'required',
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::where('email', $request['email'])->first();

        if ($user) {
            $response = [
                'status' => 'ERROR',
                'message' => 'Already sign up, Please Sign in',
            ];
            return Response($response, 400);
        }

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
            'type' => $type,
        ]);

        $mapped = implode(['name' => $user->name, 'email' => $user->email]);

        $token = $user->createToken($mapped)->plainTextToken;

        $response = [
            'status' => 'ok',
            'message' => 'Successfully logged in',
            'token' => $token,
        ];

        return Response($response, 200);
    }

    // signIn
    public function signIn(Request $request)
    {
        $type = 1;

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request['email'])->first();

        if (!$user) {
            $response = [
                'status' => 'ERROR',
                'message' => 'No associated user, Please sign up',
            ];
            return Response($response, 400);
        }

        if ($user->type != $type) {
            $message = $user->type == 2 ? "google" : "facebook";

            $response = [
                'status' => 'ERROR',
                'message' => 'User sign up using ' . $message . ', please use that',
            ];
            return Response($response, 400);
        }

        if (Hash::check($request['password'], $user->password)) {
            $response = [
                'status' => 'ERROR',
                'message' => 'please check your email address or password',
            ];
            return Response($response, 400);
        }

        $mapped = implode(['name' => $user->name, 'email' => $user->email]);

        $token = $user->createToken($mapped)->plainTextToken;

        $response = [
            'status' => 'ok',
            'message' => 'Successfully logged in',
            'token' => $token,
        ];

        return Response($response, 200);
    }

    // socialAuth
    public function socialAuth(Request $request)
    {
        $name = $request['name'];
        $email = $request['email'];
        $password = $request['password'];
        $photoUrl = $request['photoUrl'];
        $type = $request['type'];

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'photoUrl' => $photoUrl,
                'type' => $type,
            ]);
        }

        $mapped = implode(['name' => $name, 'email' => $email]);

        $token = $user->createToken($mapped)->plainTextToken;

        $response = [
            'status' => 'ok',
            'message' => 'Successfully logged in',
            'token' => $token,
        ];

        return Response($response, 200);
    }

}
