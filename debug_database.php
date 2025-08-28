<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Database Debug Information\n";
echo "==========================\n\n";

try {
    // Check if users table exists
    echo "1. Checking if users table exists...\n";
    if (Schema::hasTable('users')) {
        echo "✅ Users table exists\n\n";
        
        // Check columns
        echo "2. Checking users table columns...\n";
        $columns = Schema::getColumnListing('users');
        
        $requiredColumns = ['status', 'ban_reason', 'banned_at', 'ban_expires_at'];
        foreach ($requiredColumns as $column) {
            if (in_array($column, $columns)) {
                echo "✅ Column '$column' exists\n";
            } else {
                echo "❌ Column '$column' MISSING\n";
            }
        }
        
        echo "\nAll columns in users table:\n";
        foreach ($columns as $column) {
            echo "- $column\n";
        }
        
        // Check column details
        echo "\n3. Checking column details...\n";
        $statusColumn = DB::select("SHOW COLUMNS FROM users LIKE 'status'");
        if (!empty($statusColumn)) {
            echo "Status column type: " . $statusColumn[0]->Type . "\n";
            echo "Status column default: " . ($statusColumn[0]->Default ?? 'NULL') . "\n";
        }
        
        // Test a simple user query
        echo "\n4. Testing user query...\n";
        $userCount = DB::table('users')->count();
        echo "Total users in database: $userCount\n";
        
        if ($userCount > 0) {
            $sampleUser = DB::table('users')->where('role', '!=', 'admin')->first();
            if ($sampleUser) {
                echo "Sample user ID: {$sampleUser->id}\n";
                echo "Sample user status: " . ($sampleUser->status ?? 'NULL') . "\n";
                
                // Try to update the user
                echo "\n5. Testing direct database update...\n";
                $updateResult = DB::table('users')
                    ->where('id', $sampleUser->id)
                    ->update([
                        'status' => 'banned',
                        'ban_reason' => 'Test ban via direct DB',
                        'banned_at' => now(),
                        'ban_expires_at' => now()->addDays(1)
                    ]);
                    
                echo "Update result: " . ($updateResult ? 'SUCCESS' : 'FAILED') . "\n";
                
                // Check if update worked
                $updatedUser = DB::table('users')->where('id', $sampleUser->id)->first();
                echo "User status after update: " . ($updatedUser->status ?? 'NULL') . "\n";
                
                // Restore original status
                DB::table('users')
                    ->where('id', $sampleUser->id)
                    ->update([
                        'status' => 'active',
                        'ban_reason' => null,
                        'banned_at' => null,
                        'ban_expires_at' => null
                    ]);
                echo "User status restored to active\n";
            }
        }
        
    } else {
        echo "❌ Users table does NOT exist\n";
    }
    
    // Check migrations
    echo "\n6. Checking migrations...\n";
    if (Schema::hasTable('migrations')) {
        $migrations = DB::table('migrations')
            ->where('migration', 'like', '%add_status_to_users%')
            ->get();
            
        if ($migrations->count() > 0) {
            echo "✅ Status migration found in migrations table\n";
            foreach ($migrations as $migration) {
                echo "Migration: {$migration->migration} (batch: {$migration->batch})\n";
            }
        } else {
            echo "❌ Status migration NOT found in migrations table\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n✅ Database debug completed!\n";
