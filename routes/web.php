<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
   if (auth()->check()) {
      return redirect()->route(auth()->user()->role === 'admin' ? 'admin.dashboard' : 'dashboard');
    }
    return view('welcome');
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin/dashboard', [DashboardAdminController::class, 'index'])
    ->middleware(['auth', 'is_admin'])
    ->name('admin.dashboard');

Route::middleware('auth', 'is_user')->group(function () {
    // Main user dashboard
    Route::get('/user/dashboard', [UserDashboardController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/templates/{id}/preview', [UserDashboardController::class, 'preview'])
    ->name('templates.preview');

     Route::get('/user/history', [UserDashboardController::class, 'index'])->name('user.history');
    
    // Invitation CRUD Routes
    Route::resource('invitations', InvitationController::class);
    Route::get('/invitations/{invitation}/preview', [InvitationController::class, 'preview'])->name('invitations.preview');

    // Legacy routes (keep for backward compatibility)
    Route::get('/user/invitations', [UserDashboardController::class, 'myInvitations'])->name('user.my-invitations');
    Route::get('/user/invitations/create/{template}', [UserDashboardController::class, 'createInvitation'])->name('user.create-invitation');
    Route::post('/user/invitations/create/{template}', [UserDashboardController::class, 'storeInvitation'])->name('user.store-invitation');
    Route::get('/user/invitations/{slug}', [UserDashboardController::class, 'previewInvitation'])->name('user.invitation.preview');
    Route::get('/user/invitations/{slug}/export', [UserDashboardController::class, 'exportPDF'])->name('user.export-pdf');
    Route::delete('/user/invitations/{slug}', [UserDashboardController::class, 'deleteInvitation'])->name('user.delete-invitation');
});

Route::get('/admin/penyemangat', function () {
    return view('admin.penyemangat');
})->middleware(['auth', 'is_admin'])->name('admin.penyemangat');

// Analytics Routes
Route::prefix('admin/analytics')->middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('admin.analytics');
    Route::get('/data', [App\Http\Controllers\AnalyticsController::class, 'getData'])->name('admin.analytics.data');
    Route::get('/templates', [App\Http\Controllers\AnalyticsController::class, 'getTopTemplates'])->name('admin.analytics.templates');
});

// Admin User Control Routes
Route::prefix('admin/users')->middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::get('/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('admin.users.show');
    Route::put('/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/ban', [App\Http\Controllers\Admin\UserController::class, 'ban'])->name('admin.users.ban');
    Route::post('/unban', [App\Http\Controllers\Admin\UserController::class, 'unban'])->name('admin.users.unban');
    Route::post('/bulk-action', [App\Http\Controllers\Admin\UserController::class, 'bulkAction'])->name('admin.users.bulk-action');
    Route::get('/export', [App\Http\Controllers\Admin\UserController::class, 'export'])->name('admin.users.export');
});


// Template Routes - Semua menggunakan prefix /admin/templates untuk konsistensi
Route::prefix('admin/templates')->middleware(['auth', 'is_admin'])->group(function () {
    // Index - List semua templates
    Route::get('/', [TemplateController::class, 'index'])->name('templates.index');

    
    // Create - Form tambah template
    Route::get('/create', [TemplateController::class, 'create'])->name('templates.create');
    
    // Store - Simpan template baru
    Route::post('/', [TemplateController::class, 'store'])->name('templates.store');
    
    // Show - Detail template (opsional)
    Route::get('/{template}', [TemplateController::class, 'show'])->name('templates.show');
    
    // Edit - Form edit template
    Route::get('/{template}/edit', [TemplateController::class, 'edit'])->name('templates.edit');
    
    // Update - Update template
    Route::put('/{template}', [TemplateController::class, 'update'])->name('templates.update');
    
    // Delete - Hapus template
    Route::delete('/{template}', [TemplateController::class, 'destroy'])->name('templates.destroy');
    
    // Preview - Preview template dengan sample data
    Route::get('/{template}/preview', [TemplateController::class, 'preview'])->name('admin.templates.preview');
    
    // Generate - Download PDF template dengan data real
    Route::post('/{template}/generate-pdf', [TemplateController::class, 'generatePdf'])->name('templates.generate.pdf');
    
    // Generate - Download HTML template dengan data real (backup)
    Route::post('/{template}/generate', [TemplateController::class, 'generateHtml'])->name('templates.generate');
});

// Chat Routes
Route::middleware('auth')->group(function () {
    // User Chat Routes
    Route::prefix('chat')->group(function () {
        Route::get('/', [ChatController::class, 'userIndex'])->name('chat.index');
        Route::get('/{conversation}', [ChatController::class, 'userShow'])->name('chat.show');
        Route::post('/', [ChatController::class, 'store'])->name('chat.store');
        Route::post('/{conversation}/message', [ChatController::class, 'sendMessage'])->name('chat.send');
        Route::get('/{conversation}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
        Route::post('/{conversation}/read', [ChatController::class, 'markAsRead'])->name('chat.read');
        Route::get('/api/unread-count', [ChatController::class, 'getUnreadCount'])->name('chat.unread.count');
    });

    // Admin Chat Routes
    Route::prefix('admin/chat')->middleware('is_admin')->group(function () {
        Route::get('/', [ChatController::class, 'adminIndex'])->name('admin.chat.index');
        Route::get('/{conversation}', [ChatController::class, 'adminShow'])->name('admin.chat.show');
        Route::post('/{conversation}/message', [ChatController::class, 'sendMessage'])->name('admin.chat.send');
        Route::get('/{conversation}/messages', [ChatController::class, 'getMessages'])->name('admin.chat.messages');
        Route::post('/{conversation}/read', [ChatController::class, 'markAsRead'])->name('admin.chat.read');
        Route::put('/{conversation}/status', [ChatController::class, 'updateStatus'])->name('admin.chat.status');
    });
});

require __DIR__.'/auth.php';
