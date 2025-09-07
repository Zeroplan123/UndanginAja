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
    
    // Communication Routes
    Route::get('/user/invitations/{slug}/communication', [App\Http\Controllers\CommunicationController::class, 'show'])->name('communication.show');
    Route::post('/user/invitations/{slug}/send-email', [App\Http\Controllers\CommunicationController::class, 'sendEmail'])->name('communication.send-email');
    Route::post('/user/invitations/{slug}/send-whatsapp', [App\Http\Controllers\CommunicationController::class, 'sendWhatsApp'])->name('communication.send-whatsapp');
    Route::post('/user/invitations/{slug}/whatsapp-link', [App\Http\Controllers\CommunicationController::class, 'generateWhatsAppLink'])->name('communication.whatsapp-link');
    Route::get('/user/invitations/{slug}/temp-pdf', [App\Http\Controllers\CommunicationController::class, 'generateTempPDF'])->name('communication.temp-pdf');
});

// Test Email Routes (for debugging)
Route::get('/test-email-form', [App\Http\Controllers\TestEmailController::class, 'showTestForm'])->name('test.email.form');
Route::post('/test-email', [App\Http\Controllers\TestEmailController::class, 'testEmail'])->name('test.email');
Route::post('/test-direct-email', [App\Http\Controllers\EmailTestController::class, 'testDirectEmail'])->name('test.direct.email');

// Debug Routes
Route::get('/debug/database', function () {
    try {
        $info = [];
        
        // Check if users table exists
        $info['users_table_exists'] = \Schema::hasTable('users');
        
        if ($info['users_table_exists']) {
            // Check columns
            $columns = \Schema::getColumnListing('users');
            $requiredColumns = ['status', 'ban_reason', 'banned_at', 'ban_expires_at'];
            
            $info['columns'] = [];
            foreach ($requiredColumns as $column) {
                $info['columns'][$column] = in_array($column, $columns);
            }
            
            // Get column details for status
            $statusColumn = \DB::select("SHOW COLUMNS FROM users LIKE 'status'");
            $info['status_column_details'] = $statusColumn;
            
            // Count users
            $info['user_count'] = \DB::table('users')->count();
            
            // Get sample user
            $sampleUser = \DB::table('users')->where('role', '!=', 'admin')->first();
            if ($sampleUser) {
                $info['sample_user'] = [
                    'id' => $sampleUser->id,
                    'status' => $sampleUser->status ?? 'NULL',
                    'ban_reason' => $sampleUser->ban_reason ?? 'NULL'
                ];
            }
        }
        
        // Check migrations
        $info['migrations'] = [];
        if (\Schema::hasTable('migrations')) {
            $migrations = \DB::table('migrations')
                ->where('migration', 'like', '%add_status_to_users%')
                ->get();
            $info['migrations'] = $migrations->toArray();
        }
        
        return response()->json($info, 200, [], JSON_PRETTY_PRINT);
        
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->middleware(['auth', 'is_admin']);

// Debug database and ban functionality
Route::get('/debug/database-check', function () {
    try {
        $info = [];
        
        // Check database connection
        $pdo = DB::connection()->getPdo();
        $info['database_connection'] = 'OK';
        $info['database_name'] = DB::connection()->getDatabaseName();
        
        // Check users table structure
        $columns = Schema::getColumnListing('users');
        $info['users_table_columns'] = $columns;
        $info['has_status_column'] = in_array('status', $columns);
        
        // Get sample users
        $totalUsers = DB::table('users')->count();
        $info['total_users'] = $totalUsers;
        
        $sampleUser = DB::table('users')->where('role', '!=', 'admin')->first();
        if ($sampleUser) {
            $info['sample_user'] = [
                'id' => $sampleUser->id,
                'name' => $sampleUser->name,
                'email' => $sampleUser->email,
                'role' => $sampleUser->role,
                'status' => $sampleUser->status ?? 'NULL'
            ];
        }
        
        return response()->json($info, 200, [], JSON_PRETTY_PRINT);
        
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->middleware(['auth', 'is_admin']);

// Test ban functionality directly
Route::post('/debug/test-ban/{user}', function ($userId) {
    try {
        $user = \App\Models\User::findOrFail($userId);
        
        if ($user->role === 'admin') {
            return response()->json(['error' => 'Cannot ban admin users'], 403);
        }
        
        $originalStatus = $user->status;
        
        // Test ban using same logic as controller
        $updateResult = $user->update([
            'status' => 'banned',
            'ban_reason' => 'Debug test ban',
            'banned_at' => now(),
            'ban_expires_at' => null
        ]);
        
        $user->refresh();
        
        $result = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'original_status' => $originalStatus,
            'update_result' => $updateResult,
            'current_status' => $user->status,
            'banned_at' => $user->banned_at,
            'ban_reason' => $user->ban_reason,
            'success' => $user->status === 'banned'
        ];
        
        // Restore original status for testing
        $user->update([
            'status' => $originalStatus ?? 'active',
            'ban_reason' => null,
            'banned_at' => null,
            'ban_expires_at' => null
        ]);
        
        return response()->json($result, 200, [], JSON_PRETTY_PRINT);
        
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->middleware(['auth', 'is_admin']);

// Original test route
Route::get('/debug/test-ban-old/{user}', function ($userId) {
    try {
        $user = \App\Models\User::findOrFail($userId);
        
        if ($user->role === 'admin') {
            return response()->json(['error' => 'Cannot ban admin users'], 403);
        }
        
        // Check if status column exists
        $columns = \Schema::getColumnListing('users');
        $hasStatusColumn = in_array('status', $columns);
        
        $originalStatus = $hasStatusColumn ? $user->status : 'unknown';
        
        $result = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'columns_in_users_table' => $columns,
            'has_status_column' => $hasStatusColumn,
            'original_status' => $originalStatus,
        ];
        
        if ($hasStatusColumn) {
            // Test ban
            $user->status = 'banned';
            $user->ban_reason = 'Debug test ban';
            $user->banned_at = now();
            $user->ban_expires_at = now()->addDays(1);
            
            $saveResult = $user->save();
            $user->refresh();
            
            $result['save_result'] = $saveResult;
            $result['current_status'] = $user->status;
            $result['ban_reason'] = $user->ban_reason;
            $result['banned_at'] = $user->banned_at;
            $result['ban_expires_at'] = $user->ban_expires_at;
            
            // Restore original status
            $user->status = 'active';
            $user->ban_reason = null;
            $user->banned_at = null;
            $user->ban_expires_at = null;
            $user->save();
            
            $result['restored'] = true;
        } else {
            $result['error'] = 'Status column does not exist in users table';
            $result['solution'] = 'Run: php artisan migrate';
        }
        
        return response()->json($result, 200, [], JSON_PRETTY_PRINT);
        
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
})->middleware(['auth', 'is_admin']);

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
    Route::post('/ban', [App\Http\Controllers\Admin\SimpleBanController::class, 'ban'])->name('admin.users.ban');
    Route::post('/unban', [App\Http\Controllers\Admin\SimpleBanController::class, 'unban'])->name('admin.users.unban');
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

// Broadcast Routes (Admin)
Route::prefix('admin/broadcasts')->middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\BroadcastController::class, 'index'])->name('admin.broadcasts.index');
    Route::get('/create', [App\Http\Controllers\BroadcastController::class, 'create'])->name('admin.broadcasts.create');
    Route::post('/', [App\Http\Controllers\BroadcastController::class, 'store'])->name('admin.broadcasts.store');
    Route::get('/{broadcast}', [App\Http\Controllers\BroadcastController::class, 'show'])->name('admin.broadcasts.show');
    Route::get('/{broadcast}/edit', [App\Http\Controllers\BroadcastController::class, 'edit'])->name('admin.broadcasts.edit');
    Route::put('/{broadcast}', [App\Http\Controllers\BroadcastController::class, 'update'])->name('admin.broadcasts.update');
    Route::delete('/{broadcast}', [App\Http\Controllers\BroadcastController::class, 'destroy'])->name('admin.broadcasts.destroy');
    Route::post('/{broadcast}/send', [App\Http\Controllers\BroadcastController::class, 'send'])->name('admin.broadcasts.send');
    Route::post('/{broadcast}/cancel', [App\Http\Controllers\BroadcastController::class, 'cancel'])->name('admin.broadcasts.cancel');
    Route::get('/analytics/data', [App\Http\Controllers\BroadcastController::class, 'analytics'])->name('admin.broadcasts.analytics');
});

// Broadcast API Routes (User)
Route::middleware('auth')->group(function () {
    Route::get('/api/broadcasts', [App\Http\Controllers\BroadcastController::class, 'getUserBroadcasts'])->name('api.broadcasts.user');
    Route::post('/api/broadcasts/{broadcast}/read', [App\Http\Controllers\BroadcastController::class, 'markAsRead'])->name('api.broadcasts.read');
});

// User Gallery Routes
Route::prefix('user/gallery')->middleware(['auth', 'is_user'])->group(function () {
    Route::get('/', [App\Http\Controllers\UserGalleryController::class, 'index'])->name('user.gallery.index');
    Route::get('/create', [App\Http\Controllers\UserGalleryController::class, 'create'])->name('user.gallery.create');
    Route::post('/', [App\Http\Controllers\UserGalleryController::class, 'store'])->name('user.gallery.store');
    Route::get('/{gallery}', [App\Http\Controllers\UserGalleryController::class, 'show'])->name('user.gallery.show');
    Route::get('/{gallery}/edit', [App\Http\Controllers\UserGalleryController::class, 'edit'])->name('user.gallery.edit');
    Route::put('/{gallery}', [App\Http\Controllers\UserGalleryController::class, 'update'])->name('user.gallery.update');
    Route::delete('/{gallery}', [App\Http\Controllers\UserGalleryController::class, 'destroy'])->name('user.gallery.destroy');
    Route::get('/{gallery}/download', [App\Http\Controllers\UserGalleryController::class, 'download'])->name('user.gallery.download');
    Route::post('/bulk-delete', [App\Http\Controllers\UserGalleryController::class, 'bulkDelete'])->name('user.gallery.bulk-delete');
    Route::get('/api/statistics', [App\Http\Controllers\UserGalleryController::class, 'statistics'])->name('user.gallery.statistics');
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
