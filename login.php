<?php
require_once 'config/session.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $redirectUrls = [
        'student' => '/Students/paymentportal.php',
        'scholarship_coordinator' => '/coordinator/scholarship_coordinator.php',
        'financial_controller' => '/financial/dashboard.php',
        'admin' => '/admin/dashboard.php'
    ];
    header('Location: ' . ($redirectUrls[$_SESSION['user_type']] ?? '/dashboard.php'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
        }
        
        .glass-effect {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.1);
        }
        
        .input-field {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(59, 130, 246, 0.2);
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }
        
        .user-type-card {
            background: rgba(15, 23, 42, 0.5);
            border: 2px solid rgba(59, 130, 246, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .user-type-card:hover {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
            transform: translateY(-4px);
        }
        
        .user-type-card.selected {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.15);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }
        
        .alert {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-5xl">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">University</h1>
            <p class="text-blue-300 text-lg">Payment Management System Only</p>
        </div>

        <div class="glass-effect rounded-2xl p-8 shadow-2xl">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-white mb-2">Welcome Back</h2>
                <p class="text-gray-400">Sign in to access your account</p>
            </div>

            <div id="alertContainer"></div>

            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-300 mb-4">Select User Type</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="user-type-card rounded-xl p-4 text-center" data-type="student">
                        <i class="fas fa-user-graduate text-3xl text-blue-400 mb-2"></i>
                        <p class="text-white font-medium text-sm">Student</p>
                    </div>
                    <div class="user-type-card rounded-xl p-4 text-center" data-type="scholarship_coordinator">
                        <i class="fas fa-user-tie text-3xl text-blue-400 mb-2"></i>
                        <p class="text-white font-medium text-sm">Scholarship Coordinator</p>
                    </div>
                    <div class="user-type-card rounded-xl p-4 text-center" data-type="financial_controller">
                        <i class="fas fa-calculator text-3xl text-blue-400 mb-2"></i>
                        <p class="text-white font-medium text-sm">Financial Controller</p>
                    </div>
                    <div class="user-type-card rounded-xl p-4 text-center" data-type="admin">
                        <i class="fas fa-user-shield text-3xl text-blue-400 mb-2"></i>
                        <p class="text-white font-medium text-sm">Admin</p>
                    </div>
                </div>
                <input type="hidden" id="userType" name="user_type" required>
            </div>

            <form id="loginForm" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Email Address
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                        class="input-field w-full px-4 py-3 rounded-lg text-white placeholder-gray-500"
                        placeholder="Enter your email"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="input-field w-full px-4 py-3 rounded-lg text-white placeholder-gray-500 pr-12"
                            placeholder="Enter your password"
                        >
                        <button 
                            type="button" 
                            id="togglePassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition-colors"
                        >
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" class="w-4 h-4 rounded border-gray-600 text-blue-500 focus:ring-blue-500 focus:ring-offset-gray-800">
                        <span class="ml-2 text-sm text-gray-400">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">Forgot password?</a>
                </div>

                <button 
                    type="submit" 
                    id="loginBtn"
                    class="btn-primary w-full py-3 rounded-lg text-white font-semibold text-lg shadow-lg"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-400">
                    Don't have an account? 
                    <a href="register.php" class="text-blue-400 hover:text-blue-300 font-semibold transition-colors">
                        Register as Student
                    </a>
                </p>
            </div>
        </div>

    </div>

    <script>
        // User type selection
        const userTypeCards = document.querySelectorAll('.user-type-card');
        const userTypeInput = document.getElementById('userType');
        
        userTypeCards.forEach(card => {
            card.addEventListener('click', function() {
                userTypeCards.forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                userTypeInput.value = this.dataset.type;
            });
        });

        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });

        // Form submission
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const alertContainer = document.getElementById('alertContainer');

        function showAlert(message, type = 'error') {
            const alertClass = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            alertContainer.innerHTML = `
                <div class="alert ${alertClass} text-white px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas ${iconClass} mr-3"></i>
                    <span>${message}</span>
                </div>
            `;
            
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }

        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validate user type selection
            if (!userTypeInput.value) {
                showAlert('Please select a user type');
                return;
            }

            // Disable button and show loading
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Signing in...';

            const formData = new FormData(loginForm);
            formData.append('user_type', userTypeInput.value);

            try {
                const response = await fetch('auth/login_process.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    showAlert(data.message);
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Sign In';
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.');
                loginBtn.disabled = false;
                loginBtn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Sign In';
            }
        });
    </script>
</body>
</html>
