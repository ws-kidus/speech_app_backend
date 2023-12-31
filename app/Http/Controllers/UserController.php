<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

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
            'phone' => $phone,
            'photoUrl' => $photoUrl,
            'backgroundUrl' => $backgroundUrl,
            'createdAt' => $createdAt,
        ];

        $response = [
            'status' => 'OK',
            'result' => $result,
        ];

        return Response($response, 200);
    }

    public function updateUserDetails(Request $request)
    {
        $user = auth()->user();

        $name = $request['name'];
        $phone = $request['phone'];

        if ($name) {
            User::where('id', $user->id)->update(['name' => $name]);
            $response = [
                'status' => 'OK',
                'message' => 'successfully changed name',
            ];

            return Response($response, 200);
        }

        if ($phone) {
            $response = [
                'status' => 'OK',
                'message' => 'successfully changed name',
            ];

            return Response($response, 200);
        }
    }

    public function updateUserBackgroundImage(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'image' => 'required',
        ]);

        $this->deleteImageIfExists($user->backgroundUrl);

        $image = $request['image'];
        $imageName = time() . $user->name;
        $imagePath = public_path() . '/uploads';

        $image->move($imagePath, $imageName);

        $link = '/uploads/' . $imageName;

        User::where('id', $user->id)->update(['backgroundUrl' => $link]);

        $response = [
            'status' => 'OK',
            'message' => 'successfully changed background image',
        ];

        return Response($response, 200);

    }

    public function updateUserProfileImage(Request $request){
        $user = auth()->user();

        $request->validate([
            'image' => 'required',
        ]);

        $this->deleteImageIfExists($user->photoUrl);

        $image = $request['image'];
        $imageName = time() . $user->name;
        $imagePath = public_path() . '/uploads';

        $image->move($imagePath, $imageName);

        $link = '/uploads/' . $imageName;

        User::where('id', $user->id)->update(['photoUrl' => $link]);

        $response = [
            'status' => 'OK',
            'message' => 'successfully changed profile image',
        ];

        return Response($response, 200);
    }

    public function deleteImageIfExists($imagePath)
    {
        if ($imagePath) {
            $path =  public_path() .$imagePath;
            if (File::exists($path)) {
                File::delete($path);
            }
        }
    }

}
