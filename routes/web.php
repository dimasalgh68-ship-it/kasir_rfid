<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RfidController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/nfc/check', [DashboardController::class, 'checkNfc'])
    ->middleware(['auth'])->name('nfc.check');

Route::post('/midtrans/callback', [\App\Http\Controllers\TopupController::class, 'callback'])->name('midtrans.callback');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Orders (all authenticated users)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/rfid', [RfidController::class, 'index'])->name('rfid.index');
        Route::post('/rfid/register', [RfidController::class, 'register'])->name('rfid.register');
        Route::patch('/rfid/{card}', [RfidController::class, 'update'])->name('rfid.update');
        Route::patch('/rfid/{card}/toggle', [RfidController::class, 'toggleStatus'])->name('rfid.toggle');
        Route::delete('/rfid/{card}', [RfidController::class, 'destroy'])->name('rfid.destroy');

        Route::get('/topup', [TopupController::class, 'index'])->name('topup.index');
        Route::delete('/topup/{topup}', [TopupController::class, 'destroy'])->name('topup.destroy');
    });

    // Canteen / Admin Routes
    Route::middleware('role:admin,canteen')->prefix('canteen')->name('canteen.')->group(function () {
        Route::get('/cashier', [\App\Http\Controllers\CashierController::class, 'index'])->name('cashier.index');
        Route::post('/cashier/process', [\App\Http\Controllers\CashierController::class, 'process'])->name('cashier.process');
        Route::get('/cashier/poll-scan', [\App\Http\Controllers\CashierController::class, 'pollScan'])->name('cashier.poll');

        Route::get('/menu', [MenuItemController::class, 'index'])->name('menu.index');
        Route::post('/menu', [MenuItemController::class, 'store'])->name('menu.store');
        Route::patch('/menu/{menuItem}', [MenuItemController::class, 'update'])->name('menu.update');
        Route::patch('/menu/{menuItem}/toggle', [MenuItemController::class, 'toggleAvailability'])->name('menu.toggle');
        Route::delete('/menu/{menuItem}', [MenuItemController::class, 'destroy'])->name('menu.destroy');
    });

    // Student Routes
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/topup', [TopupController::class, 'index'])->name('topup.index');
    });

    // Shared Top-up Route
    Route::post('/topup', [TopupController::class, 'store'])->name('topup.store');
});

require __DIR__.'/auth.php';
