<?php
// Navbar Component
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="index.php" class="flex items-center">
                        <div class="h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <span class="ml-2 text-xl font-bold text-gray-900">CHMS</span>
                    </a>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="index.php" class="<?php echo $currentPage === 'index.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Home
                    </a>
                    <?php if ($userRole === 'mother'): ?>
                        <a href="add_child.php" class="<?php echo $currentPage === 'add_child.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Add Child
                        </a>
                        <a href="mother_profile.php?id=<?php echo $userId; ?>" class="<?php echo $currentPage === 'mother_profile.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            My Health
                        </a>
                    <?php elseif ($userRole === 'doctor'): ?>
                        <a href="doctor_dashboard.php" class="<?php echo $currentPage === 'doctor_dashboard.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="all_children.php" class="<?php echo $currentPage === 'all_children.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            All Children
                        </a>
                        <a href="all_mothers.php" class="<?php echo $currentPage === 'all_mothers.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            All Mothers
                        </a>
                        <a href="reports.php" class="<?php echo $currentPage === 'reports.php' ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Reports
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                <div class="ml-3 relative flex items-center space-x-4">
                    <span class="text-sm text-gray-700">
                        <span class="font-medium"><?php echo htmlspecialchars($userName); ?></span>
                        <span class="text-gray-500"> (<?php echo ucfirst($userRole); ?>)</span>
                    </span>
                    <a href="src/controllers/auth_controller.php?action=logout" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                        Logout
                    </a>
                </div>
            </div>
            <!-- Mobile menu button -->
            <div class="flex items-center sm:hidden">
                <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="mobile-menu hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="index.php" class="<?php echo $currentPage === 'index.php' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Home
            </a>
            <?php if ($userRole === 'mother'): ?>
                <a href="add_child.php" class="<?php echo $currentPage === 'add_child.php' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Add Child
                </a>
                <a href="mother_profile.php?id=<?php echo $userId; ?>" class="<?php echo $currentPage === 'mother_profile.php' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    My Health
                </a>
            <?php elseif ($userRole === 'doctor'): ?>
                <a href="doctor_dashboard.php" class="<?php echo $currentPage === 'doctor_dashboard.php' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    Dashboard
                </a>
                <a href="all_children.php" class="<?php echo $currentPage === 'all_children.php' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    All Children
                </a>
                <a href="all_mothers.php" class="<?php echo $currentPage === 'all_mothers.php' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                    All Mothers
                </a>
            <?php endif; ?>
            <a href="src/controllers/auth_controller.php?action=logout" class="border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                Logout
            </a>
        </div>
    </div>
</nav>

<script>
    // Mobile menu toggle
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }
</script>
