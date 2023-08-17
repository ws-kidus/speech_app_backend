<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    // signUp
    public function signUp(Request $request)
    {   $type = 1;
        // check for user is already signup
    }

    // signIn
    public function signIn(Request $request)
    {
         $type = 1;
        // prevent usage of signIn if user already social Auth
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
                'type'=>$type,
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
