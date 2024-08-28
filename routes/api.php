<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('v1/users', function () {
    return response(User::all())
        ->header('Cache-Control', 'public, max-age=3600'); // 1 heure de cache
});

Route::get('v1/users/{id}', function (int $id) {
    return User::find($id);
});

Route::prefix('v1/users')->as('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/{id}', [UserController::class, 'show'])->name('show');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::patch('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);

Route::middleware(['auth:api', 'cache.headers:public;max_age=3600;etag'])->prefix('/v1')->group(function () {
    Route::resource('clients', ClientController::class);
});
