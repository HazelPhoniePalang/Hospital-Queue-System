<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientKioskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VisitController;
use App\Models\Counter;
use App\Models\QueueEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('kiosk.index');
});

// Patient Kiosk Routes (Public)
Route::prefix('kiosk')->name('kiosk.')->group(function () {
    Route::get('/', [PatientKioskController::class, 'index'])->name('index');
    Route::get('/form', [PatientKioskController::class, 'showForm'])->name('form');
    Route::post('/store', [PatientKioskController::class, 'store'])->name('store');
    Route::get('/success/{id}', [PatientKioskController::class, 'success'])->name('success');
});

// Protected Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Main Dashboard (role-based routing)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Staff Dashboard
    Route::get('/staff/dashboard', [DashboardController::class, 'staff'])
        ->name('staff.dashboard');

    // Patient Dashboard
    Route::get('/patient/dashboard', [DashboardController::class, 'patient'])
        ->name('patient.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Queue Actions
    Route::prefix('queue')->name('queue.')->group(function () {
        Route::post('/call/{id}', [QueueController::class, 'call'])->name('call');
        Route::post('/complete/{id}', [QueueController::class, 'complete'])->name('complete');
        Route::post('/assign-doctor/{id}', [QueueController::class, 'assignDoctor'])->name('assign-doctor');
        Route::post('/cancel/{id}', [QueueController::class, 'cancel'])->name('cancel');
        Route::post('/toggle-counter', [QueueController::class, 'toggleCounter'])->name('toggle-counter');
    });

    // Payment Routes
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/form/{id}', [QueueController::class, 'showPaymentForm'])->name('form');
        Route::post('/process/{id}', [QueueController::class, 'processPayment'])->name('process');
        Route::get('/receipt/{paymentId}', [QueueController::class, 'paymentReceipt'])->name('receipt');
        Route::get('/receipt/{paymentId}/export', [QueueController::class, 'exportReceiptPdf'])->name('receipt.export');
    });

    // Visit Actions
    Route::prefix('visits')->name('visits.')->group(function () {
        Route::get('/consultation/{queue_id}', [VisitController::class, 'show'])->name('consultation');
        Route::post('/store/{queue_id}', [VisitController::class, 'store'])->name('store');
        Route::get('/download-pdf/{filename}', [VisitController::class, 'downloadPdf'])->name('download-pdf');
    });

    // Admin Actions
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');

        Route::get('/departments', [AdminController::class, 'departments'])->name('departments');
        Route::post('/departments', [AdminController::class, 'storeDepartment'])->name('departments.store');
        Route::put('/departments/{id}', [AdminController::class, 'updateDepartment'])->name('departments.update');
        Route::delete('/departments/{id}', [AdminController::class, 'deleteDepartment'])->name('departments.delete');

        Route::get('/services', [AdminController::class, 'services'])->name('services');
        Route::post('/services', [AdminController::class, 'storeService'])->name('services.store');
        Route::put('/services/{id}', [AdminController::class, 'updateService'])->name('services.update');
        Route::delete('/services/{id}', [AdminController::class, 'deleteService'])->name('services.delete');

        Route::get('/counters', [AdminController::class, 'counters'])->name('counters');
        Route::post('/counters', [AdminController::class, 'storeCounter'])->name('counters.store');
        Route::put('/counters/{id}', [AdminController::class, 'updateCounter'])->name('counters.update');
        Route::delete('/counters/{id}', [AdminController::class, 'deleteCounter'])->name('counters.delete');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });
});

// Public Queue Display Board
Route::get('/display', function () {
    $calledQueues = QueueEntry::with(['department', 'service'])
        ->where('status', 'called')
        ->whereDate('created_at', Carbon::today())
        ->orderBy('called_at', 'desc')
        ->limit(10)
        ->get();

    $counters = Counter::with(['currentQueue', 'department'])
        ->where('status', 'busy')
        ->get();

    return view('display', compact('calledQueues', 'counters'));
})->name('display');

require __DIR__.'/auth.php';
