<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [
        AuthController::class,
        'showLoginForm'
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'login'
    ])->name('login.process');

});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::post('/logout', [
    AuthController::class,
    'logout'
])->middleware('auth')->name('logout');


/*
|--------------------------------------------------------------------------
| Secretary Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:secretary'])
    ->prefix('secretary')
    ->name('secretary.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('dashboards.placeholder', [
                'roleTitle' => 'Secretary'
            ]);
        })->name('dashboard');

    });


/*
|--------------------------------------------------------------------------
| Approver Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:approver'])
    ->prefix('approver')
    ->name('approver.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('dashboards.placeholder', [
                'roleTitle' => 'Approver'
            ]);
        })->name('dashboard');

    });


/*
|--------------------------------------------------------------------------
| Administrator Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:administrator'])
    ->prefix('administrator')
    ->name('administrator.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('dashboards.placeholder', [
                'roleTitle' => 'Administrator'
            ]);
        })->name('dashboard');

    });


/*
|--------------------------------------------------------------------------
| Verifier Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:verifier'])
    ->prefix('verifier')
    ->name('verifier.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('dashboards.placeholder', [
                'roleTitle' => 'Verifier'
            ]);
        })->name('dashboard');

    });
