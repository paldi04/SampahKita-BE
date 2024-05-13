<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\ListUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */
    public function list(ListUserRequest $request)
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page - 1) * $size;

        $users = User::where('user_role_id', '!=', '1')->with('userRole')
            ->select('id', 'fullname', 'created_at', 'updated_at', 'user_role_id', 'last_active_at')
            ->offset($offset)
            ->limit($size)
            ->get();

        $total = User::where('user_role_id', '!=', '1')->count();

        $result = [
            'list' => $users,
            'metadata' => [
                'total_data' => $total,
                'total_page' => ceil($total / $size),
            ],
        ];
        return $this->sendResponse($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();
        $id = Str::uuid()->toString();
        try {
            $user = new User();
            $user->id = $id;
            $user->nik = $request->nik;
            $user->fullname = $request->fullname;
            $user->user_role_id = $request->user_role_id;
            $user->phone_number = $request->phone_number;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('User registration failed', [ "error" => $e->getMessage() ], 500);
        }
        DB::commit();
        return $this->sendResponse([ 'id' => $id ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if ($id === 'me' || $id == auth()->user()->id) {
            return $this->sendResponse(auth()->user()->with('userRole')->first());
        }
        if (auth()->user()->user_role_id !== 1) {
            return $this->sendError('Unauthorized', [], 401);
        }
        $user = User::where('id', $id)->with('userRole')->first();
        if (!$user) {
            return $this->sendError('User not found.', [], 404);
        }
        return $this->sendResponse($user);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update($id, UpdateUserRequest $request)
    {
        if ($id === 'me' || $id === auth()->user()->id) {
            $user = auth()->user();
        } else {
            if (auth()->user()->user_role_id !== 1) {
                return $this->sendError('Unauthorized', [], 401);
            }
            $user = User::find($id);
            if (!$user) {
                return $this->sendError('User not found.', [], 404);
            }
        }
        if ($request->phone_number && $request->phone_number !== $user->phone_number) {
            $checkPhone = User::where('phone_number', $request->phone_number)->first();
            if ($checkPhone) {
                return $this->sendError('Phone number already exists.', [], 400);
            }
        }
        if ($request->email && $request->email !== $user->email) {
            $checkEmail = User::where('email', $request->email)->first();
            if ($checkEmail) {
                return $this->sendError('Email already exists.', [], 400);
            }
        }
        DB::beginTransaction();
        try {
            $user->fullname = $request->fullname;
            $user->phone_number = $request->phone_number;
            $user->email = $request->email;
            $user->save();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('User update failed', [ "error" => $e->getMessage() ], 500);
        }
        DB::commit();
        return $this->sendResponse($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if ($id === 'me' || $id === auth()->user()->id || auth()->user()->user_role_id !== 1) {
            return $this->sendError('Unauthorized', [], 401);
        }
        $user = User::find($id);
        if (!$user) {
            return $this->sendError('User not found.', [], 404);
        }
        DB::beginTransaction();
        try {
            $user->delete();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('User deletion failed', [ "error" => $e->getMessage() ], 500);
        }
        DB::commit();
        return $this->sendResponse([ 'id' => $id ]);
    }
}
