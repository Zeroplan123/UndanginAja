<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with('invitations');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'banned') {
                $query->where('is_banned', true);
            } elseif ($request->status === 'active') {
                $query->where('is_banned', false);
            }
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Get users with pagination
        $users = $query->orderBy('created_at', 'desc')->paginate(25);

        // Calculate statistics
        $totalUsers = User::count();
        $activeUsers = User::where('is_banned', false)->count();
        $bannedUsers = User::where('is_banned', true)->count();
        $adminUsers = User::where('role', 'admin')->count();

        return view('admin.user_control', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'bannedUsers',
            'adminUsers'
        ));
    }

    /**
     * Show user details (AJAX)
     */
    public function show(User $user)
    {
        $user->load('invitations');
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'is_banned' => $user->is_banned,
            'status' => $user->is_banned ? 'banned' : 'active',
            'created_at' => $user->created_at,
            'invitations_count' => $user->invitations->count(),
            'avatar' => $user->avatar ?? null,
            'ban_reason' => $user->ban_reason,
            'banned_at' => $user->banned_at,
        ]);
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin',
            'password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diupdate'
        ]);
    }


    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Don't allow deleting admins
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus user admin'
            ], 403);
        }

        try {
            // Delete user's invitations first
            $user->invitations()->delete();
            
            // Delete user
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete user exception', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk actions - ENHANCED VERSION
     */
    public function bulkAction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:ban,unban,delete,change_role',
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'integer|exists:users,id',
                'new_role' => 'required_if:action,change_role|in:user,admin',
                'ban_reason' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $users = User::whereIn('id', $request->user_ids)->get();
            $action = $request->action;
            $successCount = 0;
            $skippedCount = 0;
            $errors = [];

            foreach ($users as $user) {
                // Skip admin users for certain actions
                if ($user->role === 'admin' && in_array($action, ['ban', 'delete'])) {
                    $skippedCount++;
                    $errors[] = "Skipped admin user: {$user->name}";
                    continue;
                }

                try {
                    switch ($action) {
                        case 'ban':
                            if (!$user->is_banned) {
                                $user->update([
                                    'is_banned' => true,
                                    'banned_at' => now(),
                                    'ban_reason' => $request->ban_reason ?? 'Bulk ban action'
                                ]);
                                $successCount++;
                            } else {
                                $skippedCount++;
                                $errors[] = "User {$user->name} already banned";
                            }
                            break;

                        case 'unban':
                            if ($user->is_banned) {
                                $user->update([
                                    'is_banned' => false,
                                    'banned_at' => null,
                                    'ban_reason' => null
                                ]);
                                $successCount++;
                            } else {
                                $skippedCount++;
                                $errors[] = "User {$user->name} not banned";
                            }
                            break;

                        case 'delete':
                            $user->invitations()->delete();
                            $user->delete();
                            $successCount++;
                            break;

                        case 'change_role':
                            $user->update(['role' => $request->new_role]);
                            $successCount++;
                            break;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error processing user {$user->name}: " . $e->getMessage();
                    Log::error('Bulk action error', [
                        'user_id' => $user->id,
                        'action' => $action,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $message = "Bulk action completed: {$successCount} successful";
            if ($skippedCount > 0) {
                $message .= ", {$skippedCount} skipped";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'details' => [
                    'successful' => $successCount,
                    'skipped' => $skippedCount,
                    'errors' => $errors
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Bulk action exception', [
                'action' => $request->action ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk action failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export users
     */
    public function export(Request $request)
    {
        $query = User::with('invitations');

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'banned') {
                $query->where('is_banned', true);
            } elseif ($request->status === 'active') {
                $query->where('is_banned', false);
            }
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        $csvData = [];
        $csvData[] = ['ID', 'Name', 'Email', 'Role', 'Status', 'Invitations Count', 'Joined Date', 'Last Login'];

        foreach ($users as $user) {
            $csvData[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->is_banned ? 'banned' : 'active',
                $user->invitations->count(),
                $user->created_at->format('Y-m-d H:i:s'),
                $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never'
            ];
        }

        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}