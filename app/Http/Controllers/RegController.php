<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RegController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $parent_id = $request->parent_id;

        return view('user.reg', compact('parent_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required|phone:CN,mobile|unique:users,mobile',
            'captcha' => 'required',
            'password' => 'required|string|min:6|confirmed',
            'parent_id' => 'required|string',
            'safe_code' => 'required|string',
        ]);
        // print_r($request->all());

        $mobile = $request->phone;
        $decode_id = \Hashids::decode($request->parent_id);// 解密传递的 ID
        if (empty($decode_id)) {
            return back()->withErrors(['邀请码不正确！'])->withInput();
        }
        $safe_code = config('website.safe_code');// 安全码
        if ($request->safe_code != $safe_code) {
            return back()->withErrors(['安全码不正确！'])->withInput();
        }

        $parent_id = $decode_id[0];// 解密后的 ID

        // 创建用户 TODO
        $user = User::create([
            'mobile' => $mobile,
            'name' => $mobile,
            'nickname' => $mobile,
//            'email' => $mobile . '@qq.com',
            'password' => bcrypt($request->password),
            'parent_id' => $parent_id,
        ]);
        //session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('user.show', [$user]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
