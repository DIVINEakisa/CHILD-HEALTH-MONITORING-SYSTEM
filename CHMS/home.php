<?php
/**
 * Landing Page - Child Health Monitoring System
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Health Monitoring System - Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .hero-pattern {
            background-color: #667eea;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <svg class="h-10 w-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="ml-2 text-2xl font-bold text-gray-900">CHMS</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="login.php" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium transition">Login</a>
                    <a href="register.php" class="btn-primary text-white px-6 py-2 rounded-md text-sm font-medium">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-pattern py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                    Child Health Monitoring System
                </h1>
                <p class="text-xl md:text-2xl text-indigo-100 mb-8 max-w-3xl mx-auto">
                    Track your child's growth, health records, and immunizations all in one place. 
                    Empowering parents and healthcare professionals for better child health outcomes.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="register.php" class="bg-white text-indigo-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-50 transition transform hover:-translate-y-1 shadow-lg">
                        Register as Mother
                    </a>
                    <a href="login.php" class="bg-indigo-800 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-indigo-900 transition transform hover:-translate-y-1 shadow-lg">
                        Healthcare Professional Login
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Why Choose CHMS?</h2>
                <p class="text-xl text-gray-600">Comprehensive child health monitoring at your fingertips</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md border border-gray-100">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Growth Tracking</h3>
                    <p class="text-gray-600">Monitor your child's weight, height, and development milestones with interactive charts and analytics.</p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md border border-gray-100">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Immunization Schedule</h3>
                    <p class="text-gray-600">Never miss a vaccination with automated reminders and comprehensive immunization tracking.</p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md border border-gray-100">
                    <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Health Alerts</h3>
                    <p class="text-gray-600">Get instant notifications for health concerns, growth deviations, and important health updates.</p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md border border-gray-100">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Health Records</h3>
                    <p class="text-gray-600">Maintain complete digital health records with doctor's notes, diagnoses, and treatment history.</p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md border border-gray-100">
                    <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Multiple Profiles</h3>
                    <p class="text-gray-600">Manage health records for multiple children under one account with ease.</p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md border border-gray-100">
                    <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Doctor Integration</h3>
                    <p class="text-gray-600">Healthcare professionals can access and update records, providing seamless care coordination.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600">Get started in three simple steps</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="text-center">
                    <div class="bg-indigo-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-3xl font-bold text-white">1</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Register</h3>
                    <p class="text-gray-600">Create your free account as a mother or healthcare professional in minutes.</p>
                </div>

                <div class="text-center">
                    <div class="bg-indigo-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-3xl font-bold text-white">2</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Add Child Profile</h3>
                    <p class="text-gray-600">Enter your child's information and begin tracking their health journey.</p>
                </div>

                <div class="text-center">
                    <div class="bg-indigo-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-3xl font-bold text-white">3</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Monitor & Track</h3>
                    <p class="text-gray-600">Access comprehensive dashboards, charts, and health insights anytime, anywhere.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="gradient-bg py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">Ready to Start Monitoring Your Child's Health?</h2>
            <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
                Join thousands of parents and healthcare professionals using CHMS to ensure better health outcomes for children.
            </p>
            <a href="register.php" class="inline-block bg-white text-indigo-600 px-10 py-4 rounded-lg text-lg font-semibold hover:bg-gray-50 transition transform hover:-translate-y-1 shadow-xl">
                Get Started Free
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <svg class="h-8 w-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="ml-2 text-xl font-bold">CHMS</span>
                    </div>
                    <p class="text-gray-400">
                        Comprehensive child health monitoring for better healthcare outcomes.
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="login.php" class="text-gray-400 hover:text-white transition">Login</a></li>
                        <li><a href="register.php" class="text-gray-400 hover:text-white transition">Register</a></li>
                        <li><a href="#features" class="text-gray-400 hover:text-white transition">Features</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <p class="text-gray-400">
                        For support and inquiries, please contact your healthcare provider.
                    </p>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> Child Health Monitoring System. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
