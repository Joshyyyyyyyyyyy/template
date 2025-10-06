<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized Access - Joshua University</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        * { font-family: 'Inter', sans-serif; }
        body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="text-center">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-red-500 rounded-full mb-6">
            <i class="fas fa-exclamation-triangle text-4xl text-white"></i>
        </div>
        <h1 class="text-4xl font-bold text-white mb-4">Unauthorized Access</h1>
        <p class="text-gray-400 mb-8 max-w-md">You don't have permission to access this page.</p>
        <a href="login.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Login
        </a>
    </div>
</body>
</html>
