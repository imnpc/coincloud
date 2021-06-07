<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

//Auth::routes();

Route::get('/home', [HomeController::class, 'index']); // 注册表单

Route::get('/users/register', [UserController::class, 'create']); // 注册表单
Route::post('/users/store', [UserController::class, 'store'])->name('userregister'); // 注册动作
Route::post("sendcode", [UserController::class, 'sendcode'])->name('sendcode');//发送验证码
Route::get('/users/{user}', [UserController::class, 'show'])->name('user.show');
Route::get('/download', [UserController::class, 'download'])->name('download');

Route::get('/user/reg', [RegController::class, 'create']); // 注册表单
Route::post("/users/regstore", [RegController::class, 'store'])->name('regstore');;//注册动作

