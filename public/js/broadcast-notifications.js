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
        notification.className = `broadcast-notification romantic-card transition-all duration-500 cursor-pointer transform hover:scale-105 ${this.getBorderColor(broadcast.type)}`;
        notification.dataset.broadcastId = broadcast.id;

        const priorityIcon = this.getPriorityIcon(broadcast.priority);
        const typeIcon = this.getTypeIcon(broadcast.type);

        notification.innerHTML = `
            <div class="romantic-gradient p-5 rounded-xl shadow-xl backdrop-blur-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center romantic-icon-bg shadow-lg">
                            ${typeIcon}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-bold text-rose-800 truncate romantic-title">${this.escapeHtml(broadcast.title)}</h4>
                            <div class="flex items-center space-x-2 ml-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold romantic-priority-${broadcast.priority} shadow-sm">
                                    ${priorityIcon} ${broadcast.priority_text}
                                </span>
                                <button onclick="broadcastNotifications.hideNotification(this.closest('.broadcast-notification'), ${broadcast.id})" 
                                        class="romantic-close-btn p-1 rounded-full transition-all duration-200">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-rose-700 mb-3 line-clamp-3 romantic-message leading-relaxed">${this.escapeHtml(broadcast.message)}</p>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-rose-500 font-medium romantic-date">${this.formatDate(broadcast.sent_at)}</span>
                            <button onclick="broadcastNotifications.markAsRead(${broadcast.id})" 
                                    class="romantic-read-btn px-3 py-1 rounded-full text-xs font-semibold transition-all duration-200">
                                <i class="fas fa-heart mr-1"></i>Mark as read
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
            3: '<i class="fas fa-heart text-xs"></i>',
            2: '<i class="fas fa-star text-xs"></i>',
            1: '<i class="fas fa-circle text-xs"></i>'
        };
        return icons[priority] || '';
    }

    getTypeIcon(type) {
        const icons = {
            'promo': '<i class="fas fa-gift text-sm text-rose-600"></i>',
            'update': '<i class="fas fa-sparkles text-sm text-rose-600"></i>',
            'maintenance': '<i class="fas fa-heart-broken text-sm text-rose-600"></i>',
            'announcement': '<i class="fas fa-heart text-sm text-rose-600"></i>'
        };
        return icons[type] || '<i class="fas fa-heart text-sm text-rose-600"></i>';
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
        const date = new Date(dateString);
        const now = new Date();
        const diffInMinutes = Math.floor((now - date) / (1000 * 60));

        if (diffInMinutes < 1) return 'Just now';
        if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
        if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
        
        return date.toLocaleDateString();
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

// Add CSS for romantic notification styling
const style = document.createElement('style');
style.textContent = `
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .broadcast-notification {
        max-height: 250px;
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        animation: slideInFromRight 0.6s ease-out;
    }
    
    .broadcast-notification.expanded {
        max-height: none;
    }
    
    .romantic-card {
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 182, 193, 0.3);
    }
    
    .romantic-gradient {
        background: linear-gradient(135deg, 
            rgba(255, 255, 255, 0.95) 0%, 
            rgba(255, 182, 193, 0.1) 50%, 
            rgba(255, 192, 203, 0.15) 100%);
        border: 1px solid rgba(255, 182, 193, 0.2);
        position: relative;
        overflow: hidden;
    }
    
    .romantic-gradient::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at top right, rgba(255, 182, 193, 0.1) 0%, transparent 50%);
        pointer-events: none;
    }
    
    .romantic-icon-bg {
        background: linear-gradient(135deg, #fff 0%, #ffb6c1 100%);
        border: 2px solid rgba(255, 182, 193, 0.3);
    }
    
    .romantic-title {
        text-shadow: 0 1px 2px rgba(255, 182, 193, 0.3);
        font-family: 'Georgia', serif;
    }
    
    .romantic-message {
        font-family: 'Georgia', serif;
        line-height: 1.6;
    }
    
    .romantic-date {
        font-style: italic;
        font-family: 'Georgia', serif;
    }
    
    .romantic-priority-1 {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        color: #6b7280;
        border: 1px solid #e5e7eb;
    }
    
    .romantic-priority-2 {
        background: linear-gradient(135deg, #fff5f5 0%, #fecaca 100%);
        color: #dc2626;
        border: 1px solid #fca5a5;
    }
    
    .romantic-priority-3 {
        background: linear-gradient(135deg, #fff1f2 0%, #fda4af 100%);
        color: #be185d;
        border: 1px solid #fb7185;
        animation: pulse 2s infinite;
    }
    
    .romantic-close-btn {
        background: rgba(255, 255, 255, 0.8);
        color: #9ca3af;
        border: 1px solid rgba(255, 182, 193, 0.2);
    }
    
    .romantic-close-btn:hover {
        background: rgba(255, 182, 193, 0.2);
        color: #ef4444;
        transform: scale(1.1);
    }
    
    .romantic-read-btn {
        background: linear-gradient(135deg, #fff 0%, #ffb6c1 100%);
        color: #be185d;
        border: 1px solid rgba(255, 182, 193, 0.3);
    }
    
    .romantic-read-btn:hover {
        background: linear-gradient(135deg, #ffb6c1 0%, #ff91a4 100%);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(255, 182, 193, 0.4);
    }
    
    .romantic-border-promo {
        border-left: 4px solid #10b981;
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.1);
    }
    
    .romantic-border-update {
        border-left: 4px solid #3b82f6;
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.1);
    }
    
    .romantic-border-maintenance {
        border-left: 4px solid #ef4444;
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.1);
    }
    
    .romantic-border-announcement {
        border-left: 4px solid #f59e0b;
        box-shadow: 0 0 20px rgba(245, 158, 11, 0.1);
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
    
    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(190, 24, 93, 0.4);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(190, 24, 93, 0);
        }
    }
    
    .broadcast-notification:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 20px 40px rgba(255, 182, 193, 0.2), 0 15px 25px rgba(0, 0, 0, 0.1);
    }
`;
document.head.appendChild(style);
