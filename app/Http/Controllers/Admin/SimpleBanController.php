<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SimpleBanController extends Controller
{
    /**
     * Ban a user
     */
    public function ban(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'reason' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        if ($user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot ban admin users'
            ], 403);
        }

        if ($user->isBanned()) {
            return response()->json([
                'success' => false,
                'message' => 'User is already banned'
            ], 400);
        }

        $success = $user->ban($request->reason);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'User has been banned successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'is_banned' => $user->is_banned,
                    'banned_at' => $user->banned_at,
                    'ban_reason' => $user->ban_reason
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to ban user'
        ], 500);
    }

    /**
     * Unban a user
     */
    public function unban(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        if (!$user->isBanned()) {
            return response()->json([
                'success' => false,
                'message' => 'User is not banned'
            ], 400);
        }

        $success = $user->unban();

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'User has been unbanned successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'is_banned' => $user->is_banned
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to unban user'
        ], 500);
    }
}
