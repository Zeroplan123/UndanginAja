<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\template as modelTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use League\Uri\UriTemplate\Template;

class DashboardAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Basic counts
        $totalUsers = User::count();
        $totalTemplates = modelTemplate::count();
        $totalInvitations = Invitation::count();
        
        // Calculate user growth
        $userGrowth = $this->calculateUserGrowth();
        $templateGrowth = $this->calculateTemplateGrowth();
        $invitationGrowth = $this->calculateInvitationGrowth();
        
        // Calculate revenue (if you have payment/subscription table)
        $revenueData = $this->calculateRevenue();
        
        // Recent activities (you can customize this based on your needs)
        $recentActivities = $this->getRecentActivities();
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalTemplates', 
            'totalInvitations',
            'userGrowth',
            'templateGrowth',
            'invitationGrowth',
            'revenueData',
            'recentActivities'
        ));
    }
    
    /**
     * Calculate user growth percentage
     */
    private function calculateUserGrowth()
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        
        // Total users hingga akhir bulan ini
        $totalUsersThisMonth = User::where('created_at', '<=', $currentMonth->endOfMonth())->count();
        
        // Total users hingga akhir bulan lalu
        $totalUsersLastMonth = User::where('created_at', '<=', $lastMonth->endOfMonth())->count();
        
        // Users baru bulan ini
        $newUsersThisMonth = User::whereBetween('created_at', [
            $currentMonth->startOfMonth(),
            $currentMonth->endOfMonth()
        ])->count();
        
        // Calculate growth percentage
        $growthPercentage = 0;
        if ($totalUsersLastMonth > 0) {
            $growthPercentage = (($totalUsersThisMonth - $totalUsersLastMonth) / $totalUsersLastMonth) * 100;
        } elseif ($totalUsersThisMonth > 0) {
            $growthPercentage = 100; // 100% growth if no users last month
        }
        
        return [
            'percentage' => round($growthPercentage, 1),
            'is_positive' => $growthPercentage >= 0,
            'new_users' => $newUsersThisMonth,
            'total_last_month' => $totalUsersLastMonth
        ];
    }
    
    /**
     * Calculate template growth
     */
    private function calculateTemplateGrowth()
    {
        $now = Carbon::now();
        
        // Templates created this week (Monday to today)
        $templatesThisWeek = modelTemplate::whereBetween('created_at', [
            $now->copy()->startOfWeek(Carbon::MONDAY),
            $now->copy()->endOfWeek(Carbon::SUNDAY)
        ])->count();
        
        // Templates created last week
        $lastWeekStart = $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY);
        $lastWeekEnd = $now->copy()->subWeek()->endOfWeek(Carbon::SUNDAY);
        
        $templatesLastWeek = modelTemplate::whereBetween('created_at', [
            $lastWeekStart,
            $lastWeekEnd
        ])->count();
        
        // Debug: untuk melihat tanggal yang digunakan
        \Log::info('Template Growth Debug:', [
            'current_week_start' => $now->copy()->startOfWeek(Carbon::MONDAY)->toDateTimeString(),
            'current_week_end' => $now->copy()->endOfWeek(Carbon::SUNDAY)->toDateTimeString(),
            'templates_this_week' => $templatesThisWeek,
            'last_week_start' => $lastWeekStart->toDateTimeString(),
            'last_week_end' => $lastWeekEnd->toDateTimeString(),
            'templates_last_week' => $templatesLastWeek
        ]);
        
        return [
            'new_templates' => $templatesThisWeek,
            'last_week_templates' => $templatesLastWeek
        ];
    }
    
    /**
     * Calculate invitation growth
     */
    private function calculateInvitationGrowth()
    {
        $currentWeek = Carbon::now();
        $lastWeek = Carbon::now()->subWeek();
        
        // Total invitations hingga minggu ini
        $totalInvitationsThisWeek = Invitation::where('created_at', '<=', $currentWeek->endOfWeek())->count();
        
        // Total invitations hingga minggu lalu
        $totalInvitationsLastWeek = Invitation::where('created_at', '<=', $lastWeek->endOfWeek())->count();
        
        // Calculate weekly growth percentage
        $growthPercentage = 0;
        if ($totalInvitationsLastWeek > 0) {
            $growthPercentage = (($totalInvitationsThisWeek - $totalInvitationsLastWeek) / $totalInvitationsLastWeek) * 100;
        } elseif ($totalInvitationsThisWeek > 0) {
            $growthPercentage = 100;
        }
        
        return [
            'percentage' => round($growthPercentage, 1),
            'is_positive' => $growthPercentage >= 0
        ];
    }
    
    /**
     * Calculate revenue data
     */
    private function calculateRevenue()
    {
        // Jika Anda memiliki tabel payments atau subscriptions, gunakan ini
        // Untuk sekarang, saya akan simulate dengan data invitation
        
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        
        // Simulasi: anggap setiap invitation premium = Rp 50,000
        $avgInvitationPrice = 50000;
        
        // Invitations bulan ini (simulasi revenue)
        $invitationsThisMonth = Invitation::whereBetween('created_at', [
            $currentMonth->startOfMonth(),
            $currentMonth->endOfMonth()
        ])->count();
        
        // Invitations bulan lalu
        $invitationsLastMonth = Invitation::whereBetween('created_at', [
            $lastMonth->startOfMonth(),
            $lastMonth->endOfMonth()
        ])->count();
        
        $revenueThisMonth = $invitationsThisMonth * $avgInvitationPrice;
        $revenueLastMonth = $invitationsLastMonth * $avgInvitationPrice;
        
        $revenueGrowth = 0;
        if ($revenueLastMonth > 0) {
            $revenueGrowth = (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100;
        } elseif ($revenueThisMonth > 0) {
            $revenueGrowth = 100;
        }
        
        return [
            'total' => $revenueThisMonth,
            'formatted' => number_format($revenueThisMonth / 1000000, 1) . 'M', // Convert to millions
            'growth_percentage' => round($revenueGrowth, 1),
            'is_positive' => $revenueGrowth >= 0
        ];
    }
    
    /**
     * Get recent activities
     */
    private function getRecentActivities()
    {
        $activities = collect();
        
        // Recent user registrations
        $recentUsers = User::latest()
            ->limit(3)
            ->get()
            ->map(function($user) {
                return [
                    'type' => 'user',
                    'message' => 'Pengguna baru mendaftar: ' . $user->name,
                    'time' => $user->created_at->diffForHumans(),
                    'priority' => 'high'
                ];
            });
        
        // Recent templates
        $recentTemplates = modelTemplate::latest()
            ->limit(2)
            ->get()
            ->map(function($template) {
                return [
                    'type' => 'template',
                    'message' => 'Template "' . $template->name . '" berhasil dipublikasi',
                    'time' => $template->created_at->diffForHumans(),
                    'priority' => 'medium'
                ];
            });
        
        // Recent invitations
        $recentInvitations = Invitation::latest()
            ->limit(2)
            ->get()
            ->map(function($invitation) {
                return [
                    'type' => 'order',
                    'message' => 'Undangan baru dibuat oleh ' . ($invitation->user->name ?? 'User'),
                    'time' => $invitation->created_at->diffForHumans(),
                    'priority' => 'high'
                ];
            });
        
        // Combine and sort by time
        $activities = $activities->concat($recentUsers)
                               ->concat($recentTemplates)
                               ->concat($recentInvitations)
                               ->sortByDesc('time')
                               ->take(5);
        
        return $activities->values()->toArray();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}