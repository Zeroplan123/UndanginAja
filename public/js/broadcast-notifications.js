/**
 * Broadcast Notifications System
 * Handles real-time broadcast notifications for users
 */

class BroadcastNotifications {
    constructor() {
        this.notifications = [];
        this.checkInterval = 30000; // Check every 30 seconds
        this.intervalId = null;
        this.isVisible = true;
        this.init();
    }

    init() {
        this.createNotificationContainer();
        this.startPolling();
        this.handleVisibilityChange();
        
        // Check immediately on page load
        this.checkForBroadcasts();
    }

    createNotificationContainer() {
        if (document.getElementById('broadcast-notifications')) return;

        const container = document.createElement('div');
        container.id = 'broadcast-notifications';
        container.className = 'fixed top-4 right-4 z-50 space-y-3';
        container.style.maxWidth = '400px';
        document.body.appendChild(container);
    }

    async checkForBroadcasts() {
        try {
            const response = await fetch('/api/broadcasts', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) return;

            const data = await response.json();
            
            if (data.broadcasts && data.broadcasts.length > 0) {
                data.broadcasts.forEach(broadcast => {
                    this.showNotification(broadcast);
                });
            }
        } catch (error) {
            console.error('Error fetching broadcasts:', error);
        }
    }

    showNotification(broadcast) {
        // Check if notification already shown
        if (this.notifications.includes(broadcast.id)) return;

        this.notifications.push(broadcast.id);

        const notification = this.createNotificationElement(broadcast);
        const container = document.getElementById('broadcast-notifications');
        
        // Add with animation
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        container.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        }, 100);

        // Auto-hide after delay based on priority
        const hideDelay = this.getHideDelay(broadcast.priority);
        if (hideDelay > 0) {
            setTimeout(() => {
                this.hideNotification(notification, broadcast.id);
            }, hideDelay);
        }

        // Play notification sound for high priority
        if (broadcast.priority === 3) {
            this.playNotificationSound();
        }
    }

    createNotificationElement(broadcast) {
        const notification = document.createElement('div');
        notification.className = `broadcast-notification professional-card transition-all duration-300 cursor-pointer ${this.getBorderColor(broadcast.type)}`;
        notification.dataset.broadcastId = broadcast.id;

        const priorityIcon = this.getPriorityIcon(broadcast.priority);
        const typeIcon = this.getTypeIcon(broadcast.type);

        notification.innerHTML = `
            <div class="professional-gradient p-4 rounded-lg shadow-sm border">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center professional-icon-bg">
                            ${typeIcon}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-semibold text-gray-900 truncate">${this.escapeHtml(broadcast.title)}</h4>
                            <div class="flex items-center space-x-2 ml-2">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium professional-priority-${broadcast.priority}">
                                    ${priorityIcon} ${broadcast.priority_text}
                                </span>
                                <button onclick="broadcastNotifications.hideNotification(this.closest('.broadcast-notification'), ${broadcast.id})" 
                                        class="professional-close-btn p-1 rounded transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-700 mb-3 line-clamp-3 leading-relaxed">${this.escapeHtml(broadcast.message)}</p>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">${this.formatDate(broadcast.sent_at)}</span>
                            <button onclick="broadcastNotifications.markAsRead(${broadcast.id})" 
                                    class="professional-read-btn px-3 py-1 rounded text-xs font-medium transition-colors duration-200">
                                <i class="fas fa-check mr-1"></i>Mark as read
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Click to expand/collapse
        notification.addEventListener('click', (e) => {
            if (e.target.tagName !== 'BUTTON') {
                this.toggleNotificationExpanded(notification);
            }
        });

        return notification;
    }

    async markAsRead(broadcastId) {
        try {
            const response = await fetch(`/api/broadcasts/${broadcastId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (response.ok) {
                const notification = document.querySelector(`[data-broadcast-id="${broadcastId}"]`);
                if (notification) {
                    this.hideNotification(notification, broadcastId);
                }
            }
        } catch (error) {
            console.error('Error marking broadcast as read:', error);
        }
    }

    hideNotification(notification, broadcastId) {
        if (!notification) return;

        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';

        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);

        // Remove from notifications array
        const index = this.notifications.indexOf(broadcastId);
        if (index > -1) {
            this.notifications.splice(index, 1);
        }
    }

    toggleNotificationExpanded(notification) {
        const message = notification.querySelector('.line-clamp-3');
        if (message.classList.contains('line-clamp-3')) {
            message.classList.remove('line-clamp-3');
            notification.classList.add('expanded');
        } else {
            message.classList.add('line-clamp-3');
            notification.classList.remove('expanded');
        }
    }

    getBorderColor(type) {
        const colors = {
            'promo': 'romantic-border-promo',
            'update': 'romantic-border-update',
            'maintenance': 'romantic-border-maintenance',
            'announcement': 'romantic-border-announcement'
        };
        return colors[type] || 'romantic-border-announcement';
    }

    getPriorityIcon(priority) {
        const icons = {
            3: '<i class="fas fa-exclamation text-xs"></i>',
            2: '<i class="fas fa-info text-xs"></i>',
            1: '<i class="fas fa-circle text-xs"></i>'
        };
        return icons[priority] || '';
    }

    getTypeIcon(type) {
        const icons = {
            'promo': '<i class="fas fa-gift text-sm text-blue-600"></i>',
            'update': '<i class="fas fa-info-circle text-sm text-blue-600"></i>',
            'maintenance': '<i class="fas fa-tools text-sm text-orange-600"></i>',
            'announcement': '<i class="fas fa-bullhorn text-sm text-gray-600"></i>'
        };
        return icons[type] || '<i class="fas fa-bullhorn text-sm text-gray-600"></i>';
    }

    getHideDelay(priority) {
        // High priority stays longer, low priority auto-hides faster
        const delays = {
            3: 15000, // 15 seconds for high priority
            2: 10000, // 10 seconds for medium priority
            1: 7000   // 7 seconds for low priority
        };
        return delays[priority] || 10000;
    }

    playNotificationSound() {
        try {
            // Create a simple notification sound
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.3);
        } catch (error) {
            // Fallback: no sound if AudioContext not supported
            console.log('Notification sound not supported');
        }
    }

    startPolling() {
        if (this.intervalId) return;

        this.intervalId = setInterval(() => {
            if (this.isVisible) {
                this.checkForBroadcasts();
            }
        }, this.checkInterval);
    }

    stopPolling() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
    }

    handleVisibilityChange() {
        document.addEventListener('visibilitychange', () => {
            this.isVisible = !document.hidden;
            
            if (this.isVisible) {
                // Check immediately when tab becomes visible
                this.checkForBroadcasts();
                this.startPolling();
            } else {
                this.stopPolling();
            }
        });
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    formatDate(dateString) {
        // Parse the date string and convert to Jakarta timezone
        const date = new Date(dateString);
        const now = new Date();
        
        // Convert to Jakarta timezone for display
        const jakartaDate = new Date(date.toLocaleString("en-US", {timeZone: "Asia/Jakarta"}));
        const jakartaNow = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Jakarta"}));
        
        const diffInMinutes = Math.floor((jakartaNow - jakartaDate) / (1000 * 60));

        if (diffInMinutes < 1) return 'Baru saja';
        if (diffInMinutes < 60) return `${diffInMinutes} menit lalu`;
        if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)} jam lalu`;
        
        // Format with Jakarta timezone
        return jakartaDate.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            timeZone: 'Asia/Jakarta'
        }) + ' WIB';
    }

    // Public method to manually check for broadcasts
    refresh() {
        this.checkForBroadcasts();
    }

    // Public method to clear all notifications
    clearAll() {
        const container = document.getElementById('broadcast-notifications');
        if (container) {
            container.innerHTML = '';
        }
        this.notifications = [];
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize for authenticated users (not admins)
    if (document.querySelector('meta[name="user-role"]')?.getAttribute('content') !== 'admin') {
        window.broadcastNotifications = new BroadcastNotifications();
    }
});

// Add CSS for professional notification styling
const style = document.createElement('style');
style.textContent = `
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .broadcast-notification {
        max-height: 200px;
        overflow: hidden;
        transition: all 0.3s ease;
        animation: slideInFromRight 0.4s ease-out;
    }
    
    .broadcast-notification.expanded {
        max-height: none;
    }
    
    .professional-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
    }
    
    .professional-gradient {
        background: linear-gradient(135deg, 
            rgba(255, 255, 255, 0.98) 0%, 
            rgba(248, 250, 252, 0.95) 100%);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    
    .professional-icon-bg {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border: 1px solid rgba(203, 213, 225, 0.5);
    }
    
    .professional-priority-1 {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }
    
    .professional-priority-2 {
        background: #fef3c7;
        color: #d97706;
        border: 1px solid #fbbf24;
    }
    
    .professional-priority-3 {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #f87171;
    }
    
    .professional-close-btn {
        background: rgba(248, 250, 252, 0.8);
        color: #64748b;
        border: 1px solid rgba(203, 213, 225, 0.3);
    }
    
    .professional-close-btn:hover {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }
    
    .professional-read-btn {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    
    .professional-read-btn:hover {
        background: #e2e8f0;
        color: #334155;
    }
    
    .romantic-border-promo {
        border-left: 3px solid #10b981;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.1);
    }
    
    .romantic-border-update {
        border-left: 3px solid #3b82f6;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
    }
    
    .romantic-border-maintenance {
        border-left: 3px solid #ef4444;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.1);
    }
    
    .romantic-border-announcement {
        border-left: 3px solid #f59e0b;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.1);
    }
    
    @keyframes slideInFromRight {
        0% {
            transform: translateX(100%);
            opacity: 0;
        }
        100% {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .broadcast-notification:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
`;
document.head.appendChild(style);
