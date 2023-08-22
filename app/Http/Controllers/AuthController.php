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
    {
        $type = 1;

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
        $type = $request['type'];

        $user = User::where('email', $email)->first();

        $photoUrl = null;
        if ($request['photoUrl']) {
            $imageName = $this->storeSocialImage($name, $request['photoUrl']);
            $photoUrl = '/uploads/' . $imageName;
        }

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

    public function storeSocialImage($name, $url)
    {
        // Initialize the cURL session
        $ch = curl_init($url);

        // Initialize directory name where
        // file will be save
        $dir = public_path() . '/uploads' . '/';

        // Use basename() function to return
        // the base name of file
        $file_name = time().$name;

        // Save file into file location
        $save_file_loc = $dir . $file_name;

        // Open file
        $fp = fopen($save_file_loc, 'wb');

        // It set an option for a cURL transfer
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // Perform a cURL session
        curl_exec($ch);

        // Closes a cURL session and frees all resources
        curl_close($ch);

        // Close file
        fclose($fp);
        return $file_name;
    }

}
