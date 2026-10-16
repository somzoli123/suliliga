<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\TeamRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/elo-meccsek', [HomeController::class, 'live'])->name('matches.live');

Route::get('/csapat-nevezes', function () {
    return view('teams.register');
})->name('teams.register');

Route::post('/csapat-nevezes/mezszamok', [
    TeamRegistrationController::class,
    'step2',
])->middleware('throttle:10,1')->name('teams.register.step2');

Route::post('/csapat-nevezes/veglegesites', [
    TeamRegistrationController::class,
    'store',
])->middleware('throttle:5,1')->name('teams.register.store');