<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== MIGRATION & DATABASE ANALYSIS ===\n\n";

try {
    // 1. Check if migrations table exists
    echo "1. Checking migrations table...\n";
    if (Schema::hasTable('migrations')) {
        echo "✅ Migrations table exists\n";
        
        // Check if status migration exists
        $statusMigration = DB::table('migrations')
            ->where('migration', 'like', '%add_status_to_users%')
            ->first();
            
        if ($statusMigration) {
            echo "✅ Status migration found: {$statusMigration->migration}\n";
            echo "   Batch: {$statusMigration->batch}\n";
            echo "   Run at: {$statusMigration->created_at}\n";
        } else {
            echo "❌ Status migration NOT found in migrations table\n";
            echo "   This means the migration hasn't been run!\n";
        }
    } else {
        echo "❌ Migrations table doesn't exist\n";
    }
    
    echo "\n2. Checking users table structure...\n";
    if (Schema::hasTable('users')) {
        echo "✅ Users table exists\n";
        
        // Get all columns
        $columns = Schema::getColumnListing('users');
        echo "All columns: " . implode(', ', $columns) . "\n\n";
        
        // Check specific columns
        $requiredColumns = ['status', 'ban_reason', 'banned_at', 'ban_expires_at'];
        foreach ($requiredColumns as $column) {
            if (in_array($column, $columns)) {
                echo "✅ Column '$column' exists\n";
            } else {
                echo "❌ Column '$column' MISSING\n";
            }
        }
        
        // Try to describe the status column if it exists
        if (in_array('status', $columns)) {
            try {
                $statusInfo = DB::select("DESCRIBE users status");
                if (!empty($statusInfo)) {
                    $info = $statusInfo[0];
                    echo "\nStatus column details:\n";
                    echo "- Type: {$info->Type}\n";
                    echo "- Null: {$info->Null}\n";
                    echo "- Default: " . ($info->Default ?? 'NULL') . "\n";
                }
            } catch (Exception $e) {
                echo "Could not get status column details: " . $e->getMessage() . "\n";
            }
        }
        
    } else {
        echo "❌ Users table doesn't exist\n";
    }
    
    echo "\n3. Testing simple database operations...\n";
    
    // Test basic connection
    try {
        $result = DB::select('SELECT 1 as test');
        echo "✅ Database connection working\n";
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    }
    
    // Count users
    try {
        $userCount = DB::table('users')->count();
        echo "✅ Users count: $userCount\n";
    } catch (Exception $e) {
        echo "❌ Could not count users: " . $e->getMessage() . "\n";
    }
    
    echo "\n4. Checking if we can run the migration...\n";
    
    // Check if migration file exists
    $migrationFile = 'database/migrations/2025_08_26_092000_add_status_to_users_table.php';
    if (file_exists($migrationFile)) {
        echo "✅ Migration file exists: $migrationFile\n";
    } else {
        echo "❌ Migration file missing: $migrationFile\n";
    }
    
    echo "\n=== SUMMARY ===\n";
    if (!in_array('status', Schema::getColumnListing('users'))) {
        echo "🔥 PROBLEM FOUND: The 'status' column doesn't exist in users table!\n";
        echo "📝 SOLUTION: Run the migration with: php artisan migrate\n";
    } else {
        echo "✅ Database structure looks correct\n";
        echo "🔍 The issue might be elsewhere (validation, permissions, etc.)\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error during analysis: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n✅ Analysis completed!\n";
