<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\GetUserDetailRequest;
use App\Http\Requests\User\GetUserRoleListRequest;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\DeleteUserRequest;
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

        $userRoles = UserRole::select('id', 'nama')->offset($offset)->limit($size)->get();

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

        $users = User::select('id', 'nama', 'status', 'created_at', 'updated_at', 'user_role_id', 'tts_id', 'last_active_at')
            ->where('user_role_id', '!=', 'admin')->with(['userRole:id,nama', 'tempatTimbulanSampah:id,nama_tempat'])
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', '=', $request->status);
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', '=', $request->status);
            })
            ->when($request->user_role_id, function ($query) use ($request) {
                $query->where('user_role_id', '=', $request->user_role_id);
            })
            ->orderBy('nama', 'asc')
            ->offset($offset)->limit($size)->get();

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
            $user->nama = $request->nama;
            $user->user_role_id = $request->user_role_id;
            $user->nomor_telepon = $request->nomor_telepon;
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
        $withData = [
            'userRole:id,nama',
            'createdBy:id,nama',
            'updatedBy:id,nama',
            'tempatTimbulanSampah:id,nama_tempat,tts_kategori_id,tts_sektor_id,alamat_provinsi,alamat_kota,alamat_rw,alamat_rt,alamat_lengkap,alamat_latitude,alamat_longitude,luas_lahan,luas_bangunan,panjang,lebar,sisa_lahan,kepemilikan_lahan,foto_tempat,status',
            'tempatTimbulanSampah.tempatTimbulanSampahKategori:id,nama',
            'tempatTimbulanSampah.tempatTimbulanSampahSektor:id,nama'
        ];
        if ($request->id == auth()->user()->id) {
            return $this->sendResponse(auth()->user()->load($withData));
        }
        $user = User::where('id', $request->id)->with($withData)->first();
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
        if ($request->id === auth()->user()->id) {
            $user = auth()->user();
        } else {
            $user = User::find($request->id);
            if (!$user) {
                return $this->sendError('User not found.', [], 404);
            }
        }
        DB::beginTransaction();
        try {
            if ($request->nama) {
                $user->nama = $request->nama;
            }
            if ($request->nomor_telepon && $request->nomor_telepon !== $user->nomor_telepon) {
                $checkPhone = User::where('nomor_telepon', $request->nomor_telepon)->first();
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
    public function deleteUser(DeleteUserRequest $request)
    {
        $user = User::find($request->id);
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
        return $this->sendResponse(['id' => $request->id]);
    }
}
