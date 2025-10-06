<?php
require_once 'config/session.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /Students/paymentportal.php');
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
        
        .input-field, .select-field {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(59, 130, 246, 0.2);
            transition: all 0.3s ease;
        }
        
        .input-field:focus, .select-field:focus {
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
        
        .profile-upload-area {
            background: rgba(15, 23, 42, 0.5);
            border: 2px dashed rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .profile-upload-area:hover {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
        }
        
        .profile-upload-area.dragover {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.15);
        }
        
        .profile-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #3b82f6;
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
        
        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="py-8 px-4">
    <div class="max-w-4xl mx-auto"> 
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">University</h1>
            <p class="text-blue-300 text-lg">Student Registration</p>
        </div>

        <div class="glass-effect rounded-2xl p-8 shadow-2xl">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-500 rounded-full mb-4">
                    <i class="fas fa-user-graduate text-2xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2">Create Your Account</h2>
                <p class="text-gray-400">Payment Management System</p>
            </div>

            <div id="alertContainer"></div>

            <form id="registerForm" enctype="multipart/form-data" class="space-y-6">
                <div class="text-center">
                    <label class="block text-sm font-medium text-gray-300 mb-4">
                        <i class="fas fa-camera mr-2"></i>Profile Picture (Optional)
                    </label>
                    
                    <div id="profileUploadArea" class="profile-upload-area rounded-xl p-6 mx-auto max-w-md">
                        <div id="uploadPlaceholder" class="text-center">
                            <i class="fas fa-cloud-upload-alt text-5xl text-blue-400 mb-3"></i>
                            <p class="text-white font-medium mb-1">Click to upload or drag and drop</p>
                            <p class="text-gray-400 text-sm">PNG, JPG, GIF up to 5MB</p>
                        </div>
                        <div id="previewContainer" class="hidden text-center">
                            <img id="profilePreview" class="profile-preview mx-auto mb-3" alt="Profile preview">
                            <button type="button" id="removeImage" class="text-red-400 hover:text-red-300 text-sm">
                                <i class="fas fa-times-circle mr-1"></i>Remove
                            </button>
                        </div>
                    </div>
                    <input type="file" id="profilePicture" name="profile_picture" accept="image/*" class="hidden">
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-user mr-2"></i>Full Name *
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            required
                            class="input-field w-full px-4 py-3 rounded-lg text-white placeholder-gray-500"
                            placeholder="Enter your full name"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-envelope mr-2"></i>Email Address *
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            class="input-field w-full px-4 py-3 rounded-lg text-white placeholder-gray-500"
                            placeholder="your.email@joshuauniversity.edu"
                        >
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="program" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-graduation-cap mr-2"></i>Program *
                        </label>
                        <select 
                            id="program" 
                            name="program" 
                            required
                            class="select-field w-full px-4 py-3 rounded-lg text-white"
                        >
                            <option value="">Select Program</option>
                            <option value="BS Information Technology">BS Information Technology</option>
                            <option value="BS Computer Engineering">BS Computer Engineering</option>
                            <option value="BS Business Administration">BS Business Administration</option>
                            <option value="BS Accountancy">BS Accountancy</option>
                            <option value="BS Psychology">BS Psychology</option>
                            <option value="BS Education">BS Education</option>
                        </select>
                    </div>

                    <div>
                        <label for="yearLevel" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-layer-group mr-2"></i>Year Level *
                        </label>
                        <select 
                            id="yearLevel" 
                            name="year_level" 
                            required
                            class="select-field w-full px-4 py-3 rounded-lg text-white"
                        >
                            <option value="">Select Year Level</option>
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
                        </select>
                    </div>

                    <div>
                        <label for="college" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-building mr-2"></i>College *
                        </label>
                        <select 
                            id="college" 
                            name="college" 
                            required
                            class="select-field w-full px-4 py-3 rounded-lg text-white"
                        >
                            <option value="">Select College</option>
                            <option value="College of Information Technology">College of Information Technology</option>
                            <option value="College of Business Administration">College of Business Administration</option>
                            <option value="College of Engineering">College of Engineering</option>
                            <option value="College of Education">College of Education</option>
                            <option value="College of Psychology">College of Psychology</option>
                        </select>
                    </div>

                    <div>
                        <label for="campus" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-map-marker-alt mr-2"></i>Campus *
                        </label>
                        <select 
                            id="campus" 
                            name="campus" 
                            required
                            class="select-field w-full px-4 py-3 rounded-lg text-white"
                        >
                            <option value="">Select Campus</option>
                            <option value="Quezon City Campus">Quezon City Campus</option>
                            <option value="Bulacan Campus">Bulacan Campus</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="studentStatus" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-user-check mr-2"></i>Student Status *
                        </label>
                        <select 
                            id="studentStatus" 
                            name="student_status" 
                            required
                            class="select-field w-full px-4 py-3 rounded-lg text-white"
                        >
                            <option value="regular">Regular</option>
                            <option value="irregular">Irregular</option>
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-lock mr-2"></i>Password *
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                minlength="8"
                                class="input-field w-full px-4 py-3 rounded-lg text-white placeholder-gray-500 pr-12"
                                placeholder="Minimum 8 characters"
                            >
                            <button 
                                type="button" 
                                id="togglePassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition-colors"
                            >
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <div class="password-strength bg-gray-700" id="passwordStrength"></div>
                            <p class="text-xs text-gray-400 mt-1" id="strengthText">Password strength</p>
                        </div>
                    </div>

                    <div>
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-lock mr-2"></i>Confirm Password *
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="confirmPassword" 
                                name="confirm_password" 
                                required
                                class="input-field w-full px-4 py-3 rounded-lg text-white placeholder-gray-500 pr-12"
                                placeholder="Re-enter password"
                            >
                            <button 
                                type="button" 
                                id="toggleConfirmPassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition-colors"
                            >
                                <i class="fas fa-eye" id="eyeIcon2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-start">
                    <input 
                        type="checkbox" 
                        id="terms" 
                        required
                        class="w-5 h-5 mt-1 rounded border-gray-600 text-blue-500 focus:ring-blue-500 focus:ring-offset-gray-800"
                    >
                    <label for="terms" class="ml-3 text-sm text-gray-400">
                        I agree to the <a href="#" class="text-blue-400 hover:text-blue-300">Terms and Conditions</a> and 
                        <a href="#" class="text-blue-400 hover:text-blue-300">Privacy Policy</a> of University
                    </label>
                </div>

                <button 
                    type="submit" 
                    id="registerBtn"
                    class="btn-primary w-full py-4 rounded-lg text-white font-semibold text-lg shadow-lg"
                >
                    <i class="fas fa-user-plus mr-2"></i>Create Account
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-400">
                    Already have an account? 
                    <a href="login.php" class="text-blue-400 hover:text-blue-300 font-semibold transition-colors">
                        Sign In
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Profile picture upload handling
        const profileUploadArea = document.getElementById('profileUploadArea');
        const profilePictureInput = document.getElementById('profilePicture');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        const previewContainer = document.getElementById('previewContainer');
        const profilePreview = document.getElementById('profilePreview');
        const removeImageBtn = document.getElementById('removeImage');

        profileUploadArea.addEventListener('click', () => {
            profilePictureInput.click();
        });

        profilePictureInput.addEventListener('change', handleFileSelect);

        // Drag and drop
        profileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            profileUploadArea.classList.add('dragover');
        });

        profileUploadArea.addEventListener('dragleave', () => {
            profileUploadArea.classList.remove('dragover');
        });

        profileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            profileUploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                profilePictureInput.files = files;
                handleFileSelect();
            }
        });

        function handleFileSelect() {
            const file = profilePictureInput.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    showAlert('Please select an image file');
                    return;
                }
                
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    showAlert('File size must be less than 5MB');
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    profilePreview.src = e.target.result;
                    uploadPlaceholder.classList.add('hidden');
                    previewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        removeImageBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profilePictureInput.value = '';
            uploadPlaceholder.classList.remove('hidden');
            previewContainer.classList.add('hidden');
        });

        // Password visibility toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });

        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const eyeIcon2 = document.getElementById('eyeIcon2');

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
            confirmPasswordInput.type = type;
            eyeIcon2.classList.toggle('fa-eye');
            eyeIcon2.classList.toggle('fa-eye-slash');
        });

        // Password strength indicator
        const passwordStrength = document.getElementById('passwordStrength');
        const strengthText = document.getElementById('strengthText');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            const colors = ['#ef4444', '#f59e0b', '#eab308', '#22c55e'];
            const texts = ['Weak', 'Fair', 'Good', 'Strong'];
            const widths = ['25%', '50%', '75%', '100%'];

            if (password.length > 0) {
                passwordStrength.style.width = widths[strength - 1] || '25%';
                passwordStrength.style.backgroundColor = colors[strength - 1] || '#ef4444';
                strengthText.textContent = texts[strength - 1] || 'Weak';
                strengthText.style.color = colors[strength - 1] || '#ef4444';
            } else {
                passwordStrength.style.width = '0%';
                strengthText.textContent = 'Password strength';
                strengthText.style.color = '#9ca3af';
            }
        });

        // Form submission
        const registerForm = document.getElementById('registerForm');
        const registerBtn = document.getElementById('registerBtn');
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
            
            // Scroll to top to show alert
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            if (type === 'error') {
                setTimeout(() => {
                    alertContainer.innerHTML = '';
                }, 5000);
            }
        }

        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validate password match
            if (passwordInput.value !== confirmPasswordInput.value) {
                showAlert('Passwords do not match');
                return;
            }

            // Disable button and show loading
            registerBtn.disabled = true;
            registerBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating Account...';

            const formData = new FormData(registerForm);

            try {
                const response = await fetch('auth/register_process.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    showAlert(data.message);
                    registerBtn.disabled = false;
                    registerBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Create Account';
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.');
                registerBtn.disabled = false;
                registerBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Create Account';
            }
        });
    </script>
</body>
</html>
