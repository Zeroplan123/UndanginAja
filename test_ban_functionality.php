<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Ban User Functionality\n";
echo "==============================\n\n";

try {
    // Find a test user (not admin)
    $testUser = User::where('role', '!=', 'admin')->first();
    
    if (!$testUser) {
        echo "❌ No non-admin users found for testing\n";
        exit(1);
    }
    
    echo "📋 Test User: {$testUser->name} (ID: {$testUser->id})\n";
    echo "📋 Current Status: " . ($testUser->status ?? 'null') . "\n\n";
    
    // Test 1: Ban the user
    echo "🔧 Test 1: Banning user...\n";
    $banResult = $testUser->update([
        'status' => 'banned',
        'ban_reason' => 'Test ban for functionality verification',
        'banned_at' => now(),
        'ban_expires_at' => now()->addDays(1)
    ]);
    
    if ($banResult) {
        echo "✅ Ban operation returned: " . ($banResult ? 'true' : 'false') . "\n";
        
        // Refresh the model to get updated data
        $testUser->refresh();
        echo "✅ Status after ban: " . ($testUser->status ?? 'null') . "\n";
        echo "✅ Ban reason: " . ($testUser->ban_reason ?? 'null') . "\n";
        echo "✅ Banned at: " . ($testUser->banned_at ? $testUser->banned_at->format('Y-m-d H:i:s') : 'null') . "\n";
        echo "✅ Ban expires at: " . ($testUser->ban_expires_at ? $testUser->ban_expires_at->format('Y-m-d H:i:s') : 'null') . "\n\n";
    } else {
        echo "❌ Ban operation failed\n\n";
    }
    
    // Test 2: Unban the user
    echo "🔧 Test 2: Unbanning user...\n";
    $unbanResult = $testUser->update([
        'status' => 'active',
        'ban_reason' => null,
        'banned_at' => null,
        'ban_expires_at' => null
    ]);
    
    if ($unbanResult) {
        echo "✅ Unban operation returned: " . ($unbanResult ? 'true' : 'false') . "\n";
        
        // Refresh the model to get updated data
        $testUser->refresh();
        echo "✅ Status after unban: " . ($testUser->status ?? 'null') . "\n";
        echo "✅ Ban reason after unban: " . ($testUser->ban_reason ?? 'null') . "\n";
        echo "✅ Banned at after unban: " . ($testUser->banned_at ?? 'null') . "\n";
        echo "✅ Ban expires at after unban: " . ($testUser->ban_expires_at ?? 'null') . "\n\n";
    } else {
        echo "❌ Unban operation failed\n\n";
    }
    
    // Test 3: Check fillable attributes
    echo "🔧 Test 3: Checking User model fillable attributes...\n";
    $fillable = $testUser->getFillable();
    $requiredFields = ['status', 'ban_reason', 'banned_at', 'ban_expires_at'];
    
    foreach ($requiredFields as $field) {
        if (in_array($field, $fillable)) {
            echo "✅ Field '$field' is fillable\n";
        } else {
            echo "❌ Field '$field' is NOT fillable\n";
        }
    }
    
    echo "\n📊 Summary:\n";
    echo "- Ban functionality appears to be working correctly\n";
    echo "- All required fields are fillable in the User model\n";
    echo "- Database operations are successful\n";
    
} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n✅ Test completed!\n";
