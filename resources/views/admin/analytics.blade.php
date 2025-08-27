<x-app-layout>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <h1 class="text-3xl font-bold text-gray-900">Analytics & Statistics</h1>
                <p class="mt-2 text-gray-600">Comprehensive insights and data analysis for UndanginAja platform</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Time Period Filter -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex flex-wrap items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 sm:mb-0">Time Period</h2>
                    <div class="flex space-x-2">
                        <button class="period-btn px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white" data-period="7">
                            7 Days
                        </button>
                        <button class="period-btn px-4 py-2 text-sm font-medium rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300" data-period="30">
                            30 Days
                        </button>
                        <button class="period-btn px-4 py-2 text-sm font-medium rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300" data-period="90">
                            90 Days
                        </button>
                        <button class="period-btn px-4 py-2 text-sm font-medium rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300" data-period="365">
                            1 Year
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900" id="total-users">1,234</p>
                        <p class="text-sm text-green-600">
                            <i class="fas fa-arrow-up"></i> +12.5% from last period
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Invitations -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-envelope text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Invitations</p>
                        <p class="text-2xl font-bold text-gray-900" id="total-invitations">5,678</p>
                        <p class="text-sm text-green-600">
                            <i class="fas fa-arrow-up"></i> +8.3% from last period
                        </p>
                    </div>
                </div>
            </div>

            <!-- Template Usage -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Templates Used</p>
                        <p class="text-2xl font-bold text-gray-900" id="templates-used">892</p>
                        <p class="text-sm text-blue-600">
                            <i class="fas fa-arrow-up"></i> +15.7% from last period
                        </p>
                    </div>
                </div>
            </div>

            <!-- Revenue -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Revenue</p>
                        <p class="text-2xl font-bold text-gray-900" id="total-revenue">Rp 45.2M</p>
                        <p class="text-sm text-green-600">
                            <i class="fas fa-arrow-up"></i> +22.1% from last period
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- User Registration Trends -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">User Registration Trends</h3>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                            <span class="text-sm text-gray-600">New Users</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-sm text-gray-600">Active Users</span>
                        </div>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="userTrendsChart"></canvas>
                </div>
            </div>

            <!-- Invitation Creation Trends -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Invitation Creation Trends</h3>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                            <span class="text-sm text-gray-600">Invitations Created</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-orange-500 rounded-full mr-2"></div>
                            <span class="text-sm text-gray-600">Invitations Sent</span>
                        </div>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="invitationTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Template Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Popular Templates -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Popular Templates</h3>
                    <select class="text-sm border-gray-300 rounded-md" id="template-period">
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
                <div class="h-80">
                    <canvas id="templatePopularityChart"></canvas>
                </div>
            </div>

            <!-- Template Categories -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Template Categories</h3>
                <div class="h-80">
                    <canvas id="templateCategoriesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Detailed Analytics Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Top Templates Table -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Top Performing Templates</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Growth</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="top-templates-table">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-star text-white"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">Elegant Modern</div>
                                            <div class="text-sm text-gray-500">Wedding</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">1,234</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+15.3%</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-heart text-white"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">Classic Green</div>
                                            <div class="text-sm text-gray-500">Birthday</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">987</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+12.7%</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-gift text-white"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">Elegant Gold</div>
                                            <div class="text-sm text-gray-500">Anniversary</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">756</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+8.9%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- User Activity -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">User Activity Insights</h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Average Session Duration</p>
                            <p class="text-2xl font-bold text-blue-600">8m 32s</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-blue-600"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Bounce Rate</p>
                            <p class="text-2xl font-bold text-green-600">23.4%</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-green-600"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Page Views per Session</p>
                            <p class="text-2xl font-bold text-purple-600">4.7</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-eye text-purple-600"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Conversion Rate</p>
                            <p class="text-2xl font-bold text-orange-600">67.8%</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-percentage text-orange-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Geographic and Device Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Geographic Distribution -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Geographic Distribution</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img src="https://flagcdn.com/w20/id.png" alt="Indonesia" class="w-5 h-5 mr-3">
                            <span class="text-sm font-medium text-gray-900">Indonesia</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 85%"></div>
                            </div>
                            <span class="text-sm text-gray-600">85%</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img src="https://flagcdn.com/w20/my.png" alt="Malaysia" class="w-5 h-5 mr-3">
                            <span class="text-sm font-medium text-gray-900">Malaysia</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 8%"></div>
                            </div>
                            <span class="text-sm text-gray-600">8%</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img src="https://flagcdn.com/w20/sg.png" alt="Singapore" class="w-5 h-5 mr-3">
                            <span class="text-sm font-medium text-gray-900">Singapore</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: 4%"></div>
                            </div>
                            <span class="text-sm text-gray-600">4%</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img src="https://flagcdn.com/w20/th.png" alt="Thailand" class="w-5 h-5 mr-3">
                            <span class="text-sm font-medium text-gray-900">Thailand</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-orange-600 h-2 rounded-full" style="width: 3%"></div>
                            </div>
                            <span class="text-sm text-gray-600">3%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Device Analytics -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Device & Browser Analytics</h3>
                <div class="h-80">
                    <canvas id="deviceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global variables for charts
    let userTrendsChart, invitationTrendsChart, templatePopularityChart, templateCategoriesChart, deviceChart;
    let currentPeriod = 7;

    // Chart configurations
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                grid: {
                    borderDash: [5, 5]
                },
                beginAtZero: true
            }
        }
    };

    // Initialize charts with empty data
    function initializeCharts() {
        // User Registration Trends Chart
        const userTrendsCtx = document.getElementById('userTrendsChart').getContext('2d');
        userTrendsChart = new Chart(userTrendsCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'New Users',
                    data: [],
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Active Users',
                    data: [],
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: chartOptions
        });

        // Invitation Creation Trends Chart
        const invitationTrendsCtx = document.getElementById('invitationTrendsChart').getContext('2d');
        invitationTrendsChart = new Chart(invitationTrendsCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Invitations Created',
                    data: [],
                    borderColor: '#8B5CF6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Invitations Sent',
                    data: [],
                    borderColor: '#F59E0B',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: chartOptions
        });

        // Template Popularity Chart
        const templatePopularityCtx = document.getElementById('templatePopularityChart').getContext('2d');
        templatePopularityChart = new Chart(templatePopularityCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Usage Count',
                    data: [],
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#8B5CF6',
                        '#EF4444',
                        '#06B6D4'
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                ...chartOptions,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Template Categories Chart
        const templateCategoriesCtx = document.getElementById('templateCategoriesChart').getContext('2d');
        templateCategoriesChart = new Chart(templateCategoriesCtx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#8B5CF6',
                        '#EF4444',
                        '#6B7280'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Device Chart
        const deviceCtx = document.getElementById('deviceChart').getContext('2d');
        deviceChart = new Chart(deviceCtx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }

    // Fetch and update analytics data
    async function loadAnalyticsData(period = 7) {
        try {
            console.log('Fetching analytics data for period:', period);
            
            const response = await fetch(`/admin/analytics/data?period=${period}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Response error:', errorText);
                throw new Error(`HTTP ${response.status}: ${errorText}`);
            }

            const data = await response.json();
            console.log('Received data:', data);
            
            if (data.error) {
                throw new Error(data.message || 'Server returned an error');
            }
            
            updateDashboard(data);
        } catch (error) {
            console.error('Error loading analytics data:', error);
            // Show error message to user
            showNotification(`Error loading analytics data: ${error.message}`, 'error');
            
            // Load fallback dummy data
            loadFallbackData();
        }
    }
    
    // Fallback dummy data function
    function loadFallbackData() {
        console.log('Loading fallback dummy data');
        const fallbackData = {
            overview: {
                totalUsers: 1234,
                userGrowth: 12.5,
                totalInvitations: 5678,
                invitationGrowth: 8.3,
                templatesUsed: 892,
                templateGrowth: 15.7,
                revenue: 'Rp 45.2M',
                revenueGrowth: 22.1
            },
            userTrends: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                newUsers: [65, 78, 90, 81, 95, 105, 120],
                activeUsers: [45, 55, 70, 65, 75, 85, 95]
            },
            invitationTrends: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                created: [120, 145, 180, 165, 200, 220, 250],
                sent: [100, 125, 155, 140, 175, 190, 215]
            },
            templatePopularity: {
                labels: ['Elegant Modern', 'Classic Green', 'Elegant Gold', 'Simple Elegant', 'Romantic Pink', 'Modern Blue'],
                data: [1234, 987, 756, 654, 543, 432]
            },
            templateCategories: {
                labels: ['Wedding', 'Birthday', 'Anniversary', 'Corporate', 'Baby Shower', 'Others'],
                data: [45, 25, 15, 8, 4, 3]
            },
            devices: {
                labels: ['Mobile', 'Desktop', 'Tablet'],
                data: [65, 30, 5]
            },
            userActivity: {
                avgSessionDuration: '8m 32s',
                bounceRate: 23.4,
                pageViewsPerSession: 4.7,
                conversionRate: 67.8
            },
            geographic: [
                {country: 'Indonesia', flag: 'id', percentage: 85},
                {country: 'Malaysia', flag: 'my', percentage: 8},
                {country: 'Singapore', flag: 'sg', percentage: 4},
                {country: 'Thailand', flag: 'th', percentage: 3}
            ]
        };
        
        updateDashboard(fallbackData);
    }

    // Update dashboard with real data
    function updateDashboard(data) {
        // Update overview stats
        updateOverviewStats(data.overview);
        
        // Update charts
        updateUserTrendsChart(data.userTrends);
        updateInvitationTrendsChart(data.invitationTrends);
        updateTemplatePopularityChart(data.templatePopularity);
        updateTemplateCategoriesChart(data.templateCategories);
        updateDeviceChart(data.devices);
        
        // Update user activity
        updateUserActivity(data.userActivity);
        
        // Update geographic data
        updateGeographicData(data.geographic);
        
        // Load top templates table
        loadTopTemplatesTable();
    }

    // Update overview statistics
    function updateOverviewStats(overview) {
        document.getElementById('total-users').textContent = overview.totalUsers.toLocaleString();
        document.getElementById('total-invitations').textContent = overview.totalInvitations.toLocaleString();
        document.getElementById('templates-used').textContent = overview.templatesUsed.toLocaleString();
        document.getElementById('total-revenue').textContent = overview.revenue;
        
        // Update growth indicators
        updateGrowthIndicator('total-users', overview.userGrowth);
        updateGrowthIndicator('total-invitations', overview.invitationGrowth);
        updateGrowthIndicator('templates-used', overview.templateGrowth);
        updateGrowthIndicator('total-revenue', overview.revenueGrowth);
    }

    function updateGrowthIndicator(elementId, growth) {
        const element = document.getElementById(elementId).parentElement.querySelector('.text-green-600, .text-red-600');
        if (element) {
            const isPositive = growth >= 0;
            element.className = `text-sm ${isPositive ? 'text-green-600' : 'text-red-600'}`;
            element.innerHTML = `<i class="fas fa-arrow-${isPositive ? 'up' : 'down'}"></i> ${isPositive ? '+' : ''}${growth}% from last period`;
        }
    }

    // Update chart functions
    function updateUserTrendsChart(data) {
        userTrendsChart.data.labels = data.labels;
        userTrendsChart.data.datasets[0].data = data.newUsers;
        userTrendsChart.data.datasets[1].data = data.activeUsers;
        userTrendsChart.update();
    }

    function updateInvitationTrendsChart(data) {
        invitationTrendsChart.data.labels = data.labels;
        invitationTrendsChart.data.datasets[0].data = data.created;
        invitationTrendsChart.data.datasets[1].data = data.sent;
        invitationTrendsChart.update();
    }

    function updateTemplatePopularityChart(data) {
        templatePopularityChart.data.labels = data.labels;
        templatePopularityChart.data.datasets[0].data = data.data;
        templatePopularityChart.update();
    }

    function updateTemplateCategoriesChart(data) {
        templateCategoriesChart.data.labels = data.labels;
        templateCategoriesChart.data.datasets[0].data = data.data;
        templateCategoriesChart.update();
    }

    function updateDeviceChart(data) {
        deviceChart.data.labels = data.labels;
        deviceChart.data.datasets[0].data = data.data;
        deviceChart.update();
    }

    // Update user activity section
    function updateUserActivity(data) {
        const activitySection = document.querySelector('.space-y-6');
        if (activitySection) {
            activitySection.children[0].querySelector('.text-2xl').textContent = data.avgSessionDuration;
            activitySection.children[1].querySelector('.text-2xl').textContent = data.bounceRate + '%';
            activitySection.children[2].querySelector('.text-2xl').textContent = data.pageViewsPerSession;
            activitySection.children[3].querySelector('.text-2xl').textContent = data.conversionRate + '%';
        }
    }

    // Update geographic data
    function updateGeographicData(data) {
        const geoSection = document.querySelector('.space-y-4');
        if (geoSection && data.length > 0) {
            geoSection.innerHTML = '';
            data.forEach(country => {
                const countryElement = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img src="https://flagcdn.com/w20/${country.flag}.png" alt="${country.country}" class="w-5 h-5 mr-3">
                            <span class="text-sm font-medium text-gray-900">${country.country}</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: ${country.percentage}%"></div>
                            </div>
                            <span class="text-sm text-gray-600">${country.percentage}%</span>
                        </div>
                    </div>
                `;
                geoSection.innerHTML += countryElement;
            });
        }
    }

    // Load top templates table
    async function loadTopTemplatesTable() {
        try {
            const response = await fetch(`/admin/analytics/templates?period=30`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (response.ok) {
                const templates = await response.json();
                updateTopTemplatesTable(templates);
            }
        } catch (error) {
            console.error('Error loading top templates:', error);
        }
    }

    function updateTopTemplatesTable(templates) {
        const tableBody = document.getElementById('top-templates-table');
        if (tableBody && templates.length > 0) {
            tableBody.innerHTML = '';
            templates.slice(0, 3).forEach((template, index) => {
                const icons = ['fa-star', 'fa-heart', 'fa-gift'];
                const colors = ['from-blue-500 to-purple-600', 'from-green-500 to-blue-600', 'from-yellow-500 to-orange-600'];
                
                const row = `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r ${colors[index]} rounded-lg flex items-center justify-center">
                                    <i class="fas ${icons[index]} text-white"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">${template.name}</div>
                                    <div class="text-sm text-gray-500">${template.category}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${template.usage_count}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">+${template.growth_percentage.toFixed(1)}%</td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        }
    }

    // Show notification function
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Initialize everything
    initializeCharts();
    loadAnalyticsData(currentPeriod);

    // Period filter functionality
    const periodButtons = document.querySelectorAll('.period-btn');
    periodButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            periodButtons.forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
            });
            
            // Add active class to clicked button
            this.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
            this.classList.add('bg-blue-600', 'text-white');
            
            // Update charts based on selected period
            const period = parseInt(this.dataset.period);
            currentPeriod = period;
            loadAnalyticsData(period);
        });
    });

    // Auto-refresh data every 5 minutes
    setInterval(() => {
        loadAnalyticsData(currentPeriod);
    }, 300000); // 5 minutes
});
</script>
</x-app-layout>