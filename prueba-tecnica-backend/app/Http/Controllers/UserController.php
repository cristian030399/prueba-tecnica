<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterAuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class UserController extends Controller
{
    public function register(Request $request)
    {
        if(!$this->isAdmin($request)) {
            return response()->json([
                'success' => false,
                'message' => 'you do not have permissions to register users'
            ], 500);
        }
        $user = new User();
        $user->id = $request->id;
        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->direction = $request->direction;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role = $request->role;
        try {
            $user->save();
            return response()->json([
                'success' => true,
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User already exists'
            ], 500);
        }
    }

    public function registerAdmin()
    {
        $user = new User();
        $user->id = 123456789;
        $user->name = 'admin';
        $user->lastname = 'admin';
        $user->direction = 'direccion 1';
        $user->email = 'admin@admin.com';
        $user->password = bcrypt('admin');
        $user->role = 'admin';
        try {
            $user->save();
            return response()->json([
                'success' => true,
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User already exists'
            ], 500);
        }

    }

    public function login(Request $request)
    {
        $input = $request->only('email', 'password');
        $jwt_token = null;

        if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], 401);
        }
        return response()->json([
            'success' => true,
            'token' => $jwt_token,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, User with id ' . $id . ' cannot be found'
            ], 400);
        }
        $updated = $user->fill($request->all())
            ->save();
        if ($updated) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, User could not be updated'
            ], 500);
        }
    }

    public function logout(Request $request)
    { 
        try {
            JWTAuth::invalidate();
            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], 500);
        }
    }

    public function getAll(Request $request)
    {
        if(!$this->isAdmin($request)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permissions'
            ], 500);
        }
        $data = User::get();
        return response()->json($data, 200);
    }

    public function get($id, Request $request)
    {
        if(!$this->isAdmin($request)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permissions'
            ], 500);
        }
        $data = User::find($id);
        return response()->json($data, 200);
    }

    public function getAuthUser(Request $request)
    {
        $user = JWTAuth::authenticate();
        return response()->json(['user' => $user]);
    }

    public function destroy($id,Request  $request)
    {
        if(!$this->isAdmin($request)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permissions'
            ], 500);
        }
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, User with id ' . $id . ' cannot be found'
            ], 400);
        }
        if ($user->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User could not be deleted'
            ], 500);
        }
    }

    private function isAdmin()
    {
        $user = JWTAuth::authenticate();
        
        return ($user->role==='administrador');
    }
}
