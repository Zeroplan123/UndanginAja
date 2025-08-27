<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
            $query->where('status', $request->status);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Get users with pagination
        $users = $query->orderBy('created_at', 'desc')->paginate(25);

        // Calculate statistics
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $bannedUsers = User::where('status', 'banned')->count();
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
            'status' => $user->status ?? 'active',
            'created_at' => $user->created_at,
            'last_login_at' => $user->last_login_at,
            'invitations_count' => $user->invitations->count(),
            'avatar' => $user->avatar ?? null,
            'ban_reason' => $user->ban_reason,
            'banned_at' => $user->banned_at,
            'ban_expires_at' => $user->ban_expires_at,
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
            'status' => 'required|in:active,banned,suspended',
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
        $user->status = $request->status;

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
     * Ban user
     */
    public function ban(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:500',
            'duration' => 'required|in:permanent,1,7,30,90',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::findOrFail($request->user_id);

        // Don't allow banning admins
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mem-ban user admin'
            ], 403);
        }

        $user->status = 'banned';
        $user->ban_reason = $request->reason;
        $user->banned_at = now();

        // Set expiration date if not permanent
        if ($request->duration !== 'permanent') {
            $user->ban_expires_at = now()->addDays((int)$request->duration);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil di-ban'
        ]);
    }

    /**
     * Unban user
     */
    public function unban(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::findOrFail($request->user_id);

        $user->status = 'active';
        $user->ban_reason = null;
        $user->banned_at = null;
        $user->ban_expires_at = null;

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil di-unban'
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

        // Delete user's invitations first
        $user->invitations()->delete();
        
        // Delete user
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:ban,unban,delete,change_role',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'new_role' => 'required_if:action,change_role|in:user,admin',
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

        foreach ($users as $user) {
            // Skip admin users for certain actions
            if ($user->role === 'admin' && in_array($action, ['ban', 'delete'])) {
                continue;
            }

            switch ($action) {
                case 'ban':
                    $user->update([
                        'status' => 'banned',
                        'banned_at' => now(),
                        'ban_reason' => 'Bulk ban action'
                    ]);
                    $successCount++;
                    break;

                case 'unban':
                    $user->update([
                        'status' => 'active',
                        'banned_at' => null,
                        'ban_reason' => null,
                        'ban_expires_at' => null
                    ]);
                    $successCount++;
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
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk action berhasil dijalankan untuk {$successCount} user"
        ]);
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
            $query->where('status', $request->status);
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
                $user->status ?? 'active',
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
