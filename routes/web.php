<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EtabDashboardController;
use App\Http\Controllers\RegistrationCompletionController;
use App\Http\Controllers\FormationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', fn() => view('welcome'));

// 🔹 Demande d’inscription
Route::get('/request', [RequestController::class, 'create']);
Route::post('/request', [RequestController::class, 'store'])->name('request.store');
Route::get('/request/statut', [RequestController::class, 'showStatusForm'])
     ->name('request.status.form');
Route::post('/request/statut', [RequestController::class, 'checkStatus'])
     ->name('request.status.check');

// 🔹 Complétion de l’inscription (lien envoyé par email)
Route::get('/complete-registration/{token}', 
    [RegistrationCompletionController::class, 'showForm']
)->name('registration.complete');
Route::post('/complete-registration/{token}', 
    [RegistrationCompletionController::class, 'submitForm']
)->name('registration.submit');

// 🔹 Authentification
Route::get('/login',  [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// 🔹 Dashboard « user »
Route::middleware(['auth','role:user'])->group(function () {
    Route::get('/user/dashboard', fn() => view('user.dashboard'))
         ->name('user.dashboard');
});

// 🔹 Dashboard validateur établissement (« etab »)
Route::middleware(['auth','role:etab'])->group(function () {
    // Liste & actions sur les demandes
    Route::get('/etab/dashboard', [EtabDashboardController::class, 'index'])
         ->name('etab.dashboard');
    Route::post('/etab/demande/{demande}/update-status', 
         [EtabDashboardController::class, 'updateStatus']
    )->whereNumber('demande');
    Route::post('/etab/demande/{demande}/decline', 
         [EtabDashboardController::class, 'decline']
    )->whereNumber('demande');
    Route::post('/etab/demandes/batch-update', 
         [EtabDashboardController::class, 'batchUpdate']
    )->name('etab.demandes.batchUpdate');
    Route::delete('/etab/demande/{demande}', 
         [EtabDashboardController::class, 'destroy']
    )->whereNumber('demande');
    Route::delete('/etab/demandes/batch-delete', 
         [EtabDashboardController::class, 'batchDestroy']
    )->name('etab.demandes.batchDestroy');
});

Route::middleware(['auth','role:univ'])
     ->prefix('univ')
     ->name('univ.')
     ->group(function () {
    // Dashboard home
    Route::view('dashboard','univ.dashboard')->name('dashboard');

    // Full CRUD for Formations
    Route::resource('formations', FormationController::class);
});


// 🔹 Dashboard super-utilisateur (« super »)
Route::middleware(['auth','role:super'])->group(function () {
    Route::get('/super/dashboard', fn() => view('super.dashboard'))
         ->name('super.dashboard');
});
