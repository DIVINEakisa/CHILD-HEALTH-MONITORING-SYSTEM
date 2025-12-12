<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Child Health Monitoring System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-green-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-blue-500 rounded-full flex items-center justify-center">
                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <h2 class="mt-6 text-3xl font-bold text-gray-900">Child Health Monitoring</h2>
            <p class="mt-2 text-sm text-gray-600">Sign in to your account</p>
        </div>

        <div class="bg-white rounded-lg shadow-xl p-8">
            <?php
            session_start();
            
            // Display flash messages
            if (isset($_SESSION['flash_message'])): ?>
                <div class="mb-4 p-4 rounded-lg <?php echo $_SESSION['flash_type'] === 'error' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'; ?>">
                    <?php 
                    echo htmlspecialchars($_SESSION['flash_message']);
                    unset($_SESSION['flash_message']);
                    unset($_SESSION['flash_type']);
                    ?>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="src/controllers/auth_controller.php" method="POST">
                <input type="hidden" name="action" value="login">
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 rounded-lg placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter your email">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 rounded-lg placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter your password">
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                        Sign in
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Don't have an account?</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="register.php" 
                       class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                        Create new account
                    </a>
                </div>
            </div>

            <div class="mt-6 text-center text-sm text-gray-600">
                <p>Demo Credentials:</p>
                <p class="mt-1"><strong>Doctor:</strong> sarah.johnson@chms.com / password123</p>
                <p><strong>Mother:</strong> mary.williams@email.com / password123</p>
            </div>
            
            <div class="mt-6 text-center">
                <a href="home.php" class="text-sm text-gray-600 hover:text-gray-900 transition">
                    ← Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
