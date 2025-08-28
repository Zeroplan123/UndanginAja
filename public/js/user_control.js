// Global variables
let currentUserId = null;

// Modal functions
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// View user details
async function viewUser(userId) {
    try {
        const response = await fetch(`/admin/users/${userId}`);
        const user = await response.json();
        
        const content = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-xl font-medium text-gray-700">${user.name.charAt(0)}</span>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold">${user.name}</h4>
                            <p class="text-gray-600">${user.email}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Role</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'}">
                                ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(user.status)}">
                                ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Invitations Created</label>
                        <p class="text-lg font-semibold">${user.invitations_count}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Joined Date</label>
                        <p class="text-sm">${new Date(user.created_at).toLocaleDateString()}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Last Login</label>
                        <p class="text-sm">${user.last_login_at ? new Date(user.last_login_at).toLocaleDateString() : 'Never'}</p>
                    </div>
                    ${user.status === 'banned' ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Ban Reason</label>
                            <p class="text-sm">${user.ban_reason || 'No reason provided'}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Banned At</label>
                            <p class="text-sm">${user.banned_at ? new Date(user.banned_at).toLocaleDateString() : 'N/A'}</p>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        document.getElementById('userDetailContent').innerHTML = content;
        openModal('userDetailModal');
    } catch (error) {
        showNotification('Error loading user details', 'error');
    }
}

// Edit user
async function editUser(userId) {
    try {
        const response = await fetch(`/admin/users/${userId}`);
        const user = await response.json();
        
        currentUserId = userId;
        document.getElementById('editName').value = user.name;
        document.getElementById('editEmail').value = user.email;
        document.getElementById('editRole').value = user.role;
        document.getElementById('editStatus').value = user.status;
        document.getElementById('editPassword').value = '';
        
        openModal('editUserModal');
    } catch (error) {
        showNotification('Error loading user data', 'error');
    }
}

// Ban user
function banUser(userId) {
    currentUserId = userId;
    document.getElementById('banUserId').value = userId;
    openModal('banUserModal');
}

// Unban user
async function unbanUser(userId) {
    if (!confirm('Are you sure you want to unban this user?')) return;
    
    try {
        const response = await fetch('/admin/users/unban', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ user_id: userId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            location.reload();
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        showNotification('Error unbanning user', 'error');
    }
}

// Delete user
async function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;
    
    try {
        const response = await fetch(`/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            location.reload();
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        showNotification('Error deleting user', 'error');
    }
}

// Bulk actions
function bulkAction(action) {
    const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    
    if (selectedUsers.length === 0) {
        showNotification('Please select at least one user', 'warning');
        return;
    }
    
    if (!confirm(`Are you sure you want to ${action} ${selectedUsers.length} user(s)?`)) return;
    
    fetch('/admin/users/bulk-action', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            action: action,
            user_ids: selectedUsers
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showNotification(result.message, 'success');
            location.reload();
        } else {
            showNotification(result.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error performing bulk action', 'error');
    });
}

// Form submissions
document.getElementById('editUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch(`/admin/users/${currentUserId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(result.message, 'success');
            closeModal('editUserModal');
            location.reload();
        } else {
            showNotification(result.message || 'Error updating user', 'error');
        }
    } catch (error) {
        showNotification('Error updating user', 'error');
    }
});

document.getElementById('banUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Enhanced debug logging
    console.log('=== BAN REQUEST DEBUG ===');
    console.log('Form data:', data);
    console.log('User ID:', data.user_id);
    console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Validate user_id exists
    if (!data.user_id) {
        showNotification('Error: User ID tidak ditemukan', 'error');
        return;
    }
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        if (!csrfToken) {
            showNotification('Error: CSRF token tidak ditemukan', 'error');
            return;
        }
        
        console.log('Sending ban request to:', '/admin/users/ban');
        
        const response = await fetch('/admin/users/ban', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', Object.fromEntries(response.headers.entries()));
        
        const responseText = await response.text();
        console.log('Raw response:', responseText);
        
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            showNotification('Error: Invalid response from server - ' + responseText.substring(0, 100), 'error');
            return;
        }
        
        console.log('Parsed response:', result);
        
        if (response.ok && result.success) {
            showNotification(result.message, 'success');
            console.log('Ban successful:', result);
            
            if (result.user) {
                console.log('Updated user data:', result.user);
            }
            
            closeModal('banUserModal');
            
            // Reload page to show updated status
            setTimeout(() => {
                console.log('Reloading page...');
                location.reload();
            }, 1000);
        } else {
            console.error('Ban failed:', result);
            
            if (result.errors) {
                const errorMessages = Object.values(result.errors).flat().join(', ');
                showNotification('Validation Error: ' + errorMessages, 'error');
            } else {
                showNotification(result.message || 'Error banning user', 'error');
            }
        }
    } catch (error) {
        console.error('Network/Request error:', error);
        showNotification('Network Error: ' + error.message, 'error');
    }
    
    console.log('=== END BAN REQUEST DEBUG ===');
});

// Checkbox functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateBulkActions();
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('user-checkbox')) {
        updateBulkActions();
    }
});

function updateBulkActions() {
    const selectedCount = document.querySelectorAll('.user-checkbox:checked').length;
    const bulkActions = document.getElementById('bulkActions');
    const selectedCountSpan = document.getElementById('selectedCount');
    
    selectedCountSpan.textContent = selectedCount;
    
    if (selectedCount > 0) {
        bulkActions.style.display = 'block';
    } else {
        bulkActions.style.display = 'none';
    }
}

// Utility functions
function getStatusColor(status) {
    const colors = {
        'active': 'bg-green-100 text-green-800',
        'banned': 'bg-red-100 text-red-800',
        'suspended': 'bg-yellow-100 text-yellow-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${getNotificationColor(type)}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

function getNotificationColor(type) {
    const colors = {
        'success': 'bg-green-500 text-white',
        'error': 'bg-red-500 text-white',
        'warning': 'bg-yellow-500 text-white',
        'info': 'bg-blue-500 text-white'
    };
    return colors[type] || colors.info;
}