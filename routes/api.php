<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
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


Route::prefix('/v1')->group(function () {
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class,'register']);
Route::post('refresh', [AuthController::class,'refreshToken']);
Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:api'])->prefix('/v1')->group(function () {
    Route::resource('clients', ClientController::class);
    Route::get('clients/{id}/dettes', [ClientController::class, 'showDettes'])->name('clients.showDettes');
    Route::get('clients/{id}/user', [ClientController::class, 'showUser'])->name('clients.showUser');

});
Route::middleware([  'auth:api',])->prefix('/v1')->group(function () {
    Route::resource('users', UserController::class);
    Route::get('register', [UserController::class,'register'])->name('users.register');

});




Route::middleware([ 'auth:api'])->prefix('v1')->group(function () {
    Route::apiResource('articles', ArticleController::class);
    Route::post('articles/stock', [ArticleController::class, 'updateStock']);
    Route::post('articles/libelle', [ArticleController::class, 'searchByLibelle']);

});
