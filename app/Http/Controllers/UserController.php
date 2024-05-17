<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\GetUserDetailRequest;
use App\Http\Requests\User\GetUserRoleListRequest;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\GetUserListRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:api')->except(['getUserRoleList']);
    }

    public function getUserRoleList(GetUserRoleListRequest $request)
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page - 1) * $size;

        $userRoles = UserRole::select('id', 'name')->offset($offset)->limit($size)->get();

        $total = UserRole::count();

        $result = [
            'list' => $userRoles,
            'metadata' => [
                'total_data' => $total,
                'total_page' => ceil($total / $size),
            ],
        ];
        return $this->sendResponse($result);
    }

    /**
     * Display a listing of the resource.
     */
    public function getUserList(GetUserListRequest $request)
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $offset = ($page - 1) * $size;

        $users = User::select('id', 'name', 'created_at', 'updated_at', 'user_role_id', 'tts_id', 'last_active_at')
            ->where('user_role_id', '!=', 'admin')->with(['userRole:id,name', 'tempatTimbulanSampah:id,nama_tempat'])
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', '=', $request->status);
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', '=', $request->status);
            })
            ->offset($offset)
            ->limit($size)
            ->get();

        $total = User::where('user_role_id', '!=', 'admin')
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', '=', $request->status);
            })->count();

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
    public function createUser(CreateUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = new User();
            $user->id = Str::uuid()->toString();
            $user->name = $request->name;
            $user->user_role_id = $request->user_role_id;
            $user->phone_number = $request->phone_number;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('User registration failed', ["error" => $e->getMessage()]);
        }
        DB::commit();
        return $this->sendResponse(['id' => $user->id]);
    }

    /**
     * Display the specified resource.
     */
    public function getUserDetail(GetUserDetailRequest $request)
    {
        if ($request->id === 'me' || $request->id == auth()->user()->id) {
            return $this->sendResponse(auth()->user()->load(['userRole:id,name', 'createdBy:id,name', 'updatedBy:id,name', 'tempatTimbulanSampah:id,nama_tempat']));
        }
        if (auth()->user()->user_role_id !== 'admin') {
            return $this->sendError('Unauthorized', [], 401);
        }
        $user = User::where('id', $request->id)->with(['userRole:id,name', 'createdBy:id,name', 'updatedBy:id,name', 'tempatTimbulanSampah:id,nama_tempat'])->first();
        if (!$user) {
            return $this->sendError('User not found.', [], 404);
        }
        return $this->sendResponse($user);
    }
    /**
     * Update the specified resource in storage.
     */
    public function updateUser(UpdateUserRequest $request)
    {
        if ($request->id === 'me' || $request->id === auth()->user()->id) {
            $user = auth()->user();
        } else {
            if (auth()->user()->user_role_id !== 'admin') {
                return $this->sendError('Unauthorized', [], 401);
            }
            $user = User::find($request->id);
            if (!$user) {
                return $this->sendError('User not found.', [], 404);
            }
        }
        DB::beginTransaction();
        try {
            if ($request->name) {
                $user->name = $request->name;
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
            if ($request->status) {
                $user->status = $request->status;
            }
            $user->save();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('User update failed', ["error" => $e->getMessage()]);
        }
        DB::commit();
        return $this->sendResponse($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if ($id === 'me' || $id === auth()->user()->id || auth()->user()->user_role_id !== 'admin') {
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
            return $this->sendError('User deletion failed', ["error" => $e->getMessage()]);
        }
        DB::commit();
        return $this->sendResponse(['id' => $id]);
    }
}
