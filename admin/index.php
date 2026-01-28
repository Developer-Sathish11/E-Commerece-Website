<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopNow Admin - User Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #2c3e50, #1a252f);
            color: white;
            padding: 20px 0;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            height: 100%;
            overflow-y: auto;
        }

        .logo {
            text-align: center;
            padding: 20px 15px;
            border-bottom: 1px solid #34495e;
            margin-bottom: 20px;
        }

        .logo h2 {
            font-size: 1.8rem;
            color: #3498db;
        }

        .logo span {
            color: #e74c3c;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }

        .nav-link:hover, .nav-link.active {
            background-color: #34495e;
            color: white;
            border-left: 4px solid #3498db;
        }

        .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid #e0e0e0;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 1.8rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.5rem;
        }

        .card-info h3 {
            font-size: 2rem;
            color: #2c3e50;
        }

        .card-info p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .card-users .card-icon {
            background-color: #e3f2fd;
            color: #2196f3;
        }

        .card-active .card-icon {
            background-color: #e8f5e9;
            color: #4caf50;
        }

        .card-new .card-icon {
            background-color: #fff3e0;
            color: #ff9800;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-header h3 {
            color: #2c3e50;
        }

        .search-box {
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 8px 15px;
            width: 300px;
        }

        .search-box i {
            color: #7f8c8d;
            margin-right: 10px;
        }

        .search-box input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: 0.95rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid #f0f0f0;
            color: #2c3e50;
            font-weight: 600;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .user-avatar-small {
            width: 35px;
            height: 35px;
            background-color: #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }

        .user-cell {
            display: flex;
            align-items: center;
        }

        .status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-edit {
            background-color: #e3f2fd;
            color: #2196f3;
        }

        .btn-edit:hover {
            background-color: #2196f3;
            color: white;
        }

        .btn-delete {
            background-color: #fde8e8;
            color: #e74c3c;
        }

        .btn-delete:hover {
            background-color: #e74c3c;
            color: white;
        }

        .btn-add {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-add:hover {
            background-color: #3498db;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }

        .pagination button {
            padding: 8px 15px;
            border: 1px solid #ddd;
            background-color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .pagination button.active {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            color: #2c3e50;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #7f8c8d;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3498db;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
        }

        .btn-cancel {
            background-color: #f8f9fa;
            color: #333;
        }

        .btn-cancel:hover {
            background-color: #e9ecef;
        }

        .btn-save {
            background-color: #3498db;
            color: white;
        }

        .btn-save:hover {
            background-color: #2980b9;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                padding: 20px 5px;
            }
            
            .logo h2 {
                font-size: 1.2rem;
            }
            
            .nav-link span {
                display: none;
            }
            
            .nav-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }
            
            .main-content {
                margin-left: 70px;
            }
        }

        @media (max-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .search-box {
                width: 100%;
            }
            
            .actions {
                flex-direction: column;
                gap: 5px;
            }
            
            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
       <?php include"sidebar.php"; ?> 

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <h1>User Management</h1>
                <div class="user-info">
                    <div class="user-avatar">A</div>
                    <div>
                        <p>Admin User</p>
                        <small>Administrator</small>
                    </div>
                </div>
            </div>

            <!-- Dashboard Cards -->
            <div class="dashboard-cards">
                <div class="card card-users">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-info">
                        <h3 id="total-users">3</h3>
                        <p>Total Users</p>
                    </div>
                </div>
                
                <div class="card card-active">
                    <div class="card-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="card-info">
                        <h3>3</h3>
                        <p>Active Users</p>
                    </div>
                </div>
                
                <div class="card card-new">
                    <div class="card-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="card-info">
                        <h3>0</h3>
                        <p>New Today</p>
                    </div>
                </div>
            </div>

            <!-- User Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3>All Users</h3>
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-input" placeholder="Search users...">
                    </div>
                </div>
                
                <button class="btn btn-add" id="add-user-btn">
                    <i class="fas fa-plus"></i> Add New User
                </button>
                
                <table id="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joined Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body">
                        <!-- User data will be populated here by JavaScript -->
                    </tbody>
                </table>
                
                <div class="pagination" id="pagination">
                    <!-- Pagination will be generated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div class="modal" id="user-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Add New User</h3>
                <button class="close-btn" id="close-modal">&times;</button>
            </div>
            
            <form id="user-form">
                <input type="hidden" id="user-id">
                
                <div class="form-group">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" required>
                </div>
                
                <div class="form-group">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password">
                    <small>Leave blank to keep current password</small>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-cancel" id="cancel-btn">Cancel</button>
                    <button type="submit" class="btn btn-save">Save User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Sample user data from the database dump
        const usersData = [
            {
                id: 1,
                first_name: "pramekumar",
                last_name: "R",
                email: "Prame123123@gmail.com",
                phone: "1234567890",
                created_at: "2026-01-25 10:24:46",
                status: "active"
            },
            {
                id: 2,
                first_name: "kavin",
                last_name: "A",
                email: "Kavinn1212@gmail.com",
                phone: "1122667733",
                created_at: "2026-01-25 10:26:38",
                status: "active"
            },
            {
                id: 3,
                first_name: "Thangaraj",
                last_name: "C",
                email: "thangaraj01@gmail.com",
                phone: "4563728910",
                created_at: "2026-01-25 10:36:10",
                status: "active"
            }
        ];

        // DOM Elements
        const usersTableBody = document.getElementById('users-table-body');
        const searchInput = document.getElementById('search-input');
        const addUserBtn = document.getElementById('add-user-btn');
        const userModal = document.getElementById('user-modal');
        const closeModal = document.getElementById('close-modal');
        const cancelBtn = document.getElementById('cancel-btn');
        const userForm = document.getElementById('user-form');
        const modalTitle = document.getElementById('modal-title');
        const userIdInput = document.getElementById('user-id');
        const firstNameInput = document.getElementById('first-name');
        const lastNameInput = document.getElementById('last-name');
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const passwordInput = document.getElementById('password');
        const totalUsersElement = document.getElementById('total-users');

        // Initialize
        let currentUsers = [...usersData];
        let currentEditId = null;
        
        // Format date for display
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Get initials for avatar
        function getInitials(firstName, lastName) {
            return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
        }

        // Render users table
        function renderUsersTable(users) {
            usersTableBody.innerHTML = '';
            
            if (users.length === 0) {
                usersTableBody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            <i class="fas fa-users" style="font-size: 3rem; color: #ddd; margin-bottom: 10px;"></i>
                            <p>No users found</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            users.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.id}</td>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-small">${getInitials(user.first_name, user.last_name)}</div>
                            <div>
                                <strong>${user.first_name} ${user.last_name}</strong>
                            </div>
                        </div>
                    </td>
                    <td>${user.email}</td>
                    <td>${user.phone || 'N/A'}</td>
                    <td>${formatDate(user.created_at)}</td>
                    <td><span class="status status-active">Active</span></td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-edit" onclick="editUser(${user.id})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-delete" onclick="deleteUser(${user.id})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                `;
                usersTableBody.appendChild(row);
            });
            
            // Update total users count
            totalUsersElement.textContent = users.length;
        }

        // Search users
        function searchUsers(query) {
            if (!query.trim()) {
                renderUsersTable(currentUsers);
                return;
            }
            
            const filteredUsers = currentUsers.filter(user => 
                user.first_name.toLowerCase().includes(query.toLowerCase()) ||
                user.last_name.toLowerCase().includes(query.toLowerCase()) ||
                user.email.toLowerCase().includes(query.toLowerCase()) ||
                user.phone.includes(query)
            );
            
            renderUsersTable(filteredUsers);
        }

        // Open modal for adding/editing user
        function openModal(isEdit = false, user = null) {
            if (isEdit && user) {
                modalTitle.textContent = 'Edit User';
                userIdInput.value = user.id;
                firstNameInput.value = user.first_name;
                lastNameInput.value = user.last_name;
                emailInput.value = user.email;
                phoneInput.value = user.phone;
                passwordInput.required = false;
                currentEditId = user.id;
            } else {
                modalTitle.textContent = 'Add New User';
                userIdInput.value = '';
                firstNameInput.value = '';
                lastNameInput.value = '';
                emailInput.value = '';
                phoneInput.value = '';
                passwordInput.value = '';
                passwordInput.required = true;
                currentEditId = null;
            }
            
            userModal.style.display = 'flex';
        }

        // Close modal
        function closeUserModal() {
            userModal.style.display = 'none';
            userForm.reset();
            currentEditId = null;
        }

        // Edit user
        function editUser(id) {
            const user = currentUsers.find(u => u.id === id);
            if (user) {
                openModal(true, user);
            }
        }

        // Delete user
        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                currentUsers = currentUsers.filter(user => user.id !== id);
                renderUsersTable(currentUsers);
                alert('User deleted successfully!');
            }
        }

        // Handle form submission
        userForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const userData = {
                first_name: firstNameInput.value,
                last_name: lastNameInput.value,
                email: emailInput.value,
                phone: phoneInput.value,
                created_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
                status: 'active'
            };
            
            if (currentEditId) {
                // Update existing user
                userData.id = currentEditId;
                const index = currentUsers.findIndex(u => u.id === currentEditId);
                currentUsers[index] = userData;
            } else {
                // Add new user
                userData.id = currentUsers.length > 0 ? Math.max(...currentUsers.map(u => u.id)) + 1 : 1;
                currentUsers.push(userData);
            }
            
            renderUsersTable(currentUsers);
            closeUserModal();
            alert(`User ${currentEditId ? 'updated' : 'added'} successfully!`);
        });

        // Event Listeners
        searchInput.addEventListener('input', (e) => {
            searchUsers(e.target.value);
        });
        
        addUserBtn.addEventListener('click', () => {
            openModal(false);
        });
        
        closeModal.addEventListener('click', closeUserModal);
        cancelBtn.addEventListener('click', closeUserModal);
        
        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === userModal) {
                closeUserModal();
            }
        });

        // Initialize the page
        document.addEventListener('DOMContentLoaded', () => {
            renderUsersTable(currentUsers);
        });
    </script>
</body>
</html>