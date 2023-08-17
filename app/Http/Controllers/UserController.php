<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function fetchUser()
    {
        $user = auth()->user();

        $id = $user->id;
        $name = $user->name;
        $email = $user->email;
        $phone = $user->phone;
        $photoUrl = $user->photoUrl;
        $backgroundUrl = $user->backgroundUrl;
        $createdAt = $user->created_at;

        $result = [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'phone'=> $phone,
            'photoUrl' => $photoUrl,
            'backgroundUrl'=>$backgroundUrl,
            'createdAt' => $createdAt,
        ];

        $response = [
            'status' => 'OK',
            'result' => $result,
        ];

        return Response($response, 200);
    }
    public function updateUserPhoto()
    {}
}
