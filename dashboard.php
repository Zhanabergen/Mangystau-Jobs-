<?php
session_start();
require_once 'database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$fullname = $_SESSION['fullname'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Mangystau Jobs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#3B82F6' } } }
        }
    </script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gray-100">

<!-- Header -->
<header class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <a href="index.html" class="flex items-center gap-2">
                <span class="text-2xl">🏝️</span>
                <span class="font-bold text-xl hidden md:block">MangystauJobs</span>
            </a>
            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full"><?php echo ucfirst($role); ?></span>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="relative group">
                <button class="flex items-center gap-2 bg-gray-100 rounded-full px-4 py-2 hover:bg-gray-200 transition">
                    <i class="fas fa-bell text-gray-600"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                </button>
            </div>
            
            <div class="relative group">
                <button class="flex items-center gap-2 bg-gray-100 rounded-full px-4 py-2 hover:bg-gray-200 transition">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                        <?php echo strtoupper(substr($fullname, 0, 1)); ?>
                    </div>
                    <span class="hidden md:inline"><?php echo $fullname; ?></span>
                    <i class="fas fa-chevron-down text-sm"></i>
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl hidden group-hover:block">
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100"><i class="fas fa-user mr-2"></i>Profile</a>
                    <a href="#" class="block px-4 py-2 hover:bg-gray-100"><i class="fas fa-cog mr-2"></i>Settings</a>
                    <hr class="my-1">
                    <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-4 py-8">

<?php if($role == 'seeker'): ?>
    <!-- SEEKER DASHBOARD -->
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Left Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Profile Card -->
            <div class="bg-white rounded-2xl shadow-md p-6 text-center">
                <div class="w-24 h-24 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                    <?php echo strtoupper(substr($fullname, 0, 2)); ?>
                </div>
                <h2 class="text-xl font-bold"><?php echo $fullname; ?></h2>
                <p class="text-gray-500 mb-3"><i class="fas fa-map-marker-alt mr-1"></i><?php echo $user['microdistrict']; ?>th Microdistrict</p>
                <div class="flex justify-center gap-2 mb-4">
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm"><i class="fas fa-phone mr-1"></i><?php echo $user['phone']; ?></span>
                </div>
                <button onclick="shareProfile()" class="w-full bg-gray-100 text-gray-700 py-2 rounded-xl hover:bg-gray-200 transition">
                    <i class="fas fa-share-alt mr-2"></i>Share Profile
                </button>
            </div>
            
            <!-- Skills Card -->
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h3 class="font-bold text-lg mb-3"><i class="fas fa-code mr-2 text-blue-600"></i>Your Skills</h3>
                <div class="flex flex-wrap gap-2">
                    <?php 
                    $skills = explode(',', $user['skills'] ?? '');
                    foreach($skills as $skill):
                        if(trim($skill)):
                    ?>
                        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm"><?php echo trim($skill); ?></span>
                    <?php endif; endforeach; ?>
                </div>
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm text-gray-600"><i class="fas fa-briefcase mr-1"></i>Experience: <?php echo $user['experience']; ?> years</p>
                </div>
            </div>
            
            <!-- Telegram Bot Card -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl shadow-md p-6 text-white text-center">
                <i class="fab fa-telegram text-4xl mb-3"></i>
                <h3 class="font-bold text-xl mb-2">Connect Telegram Bot</h3>
                <p class="text-sm mb-4">Get instant job notifications</p>
                <button onclick="connectTelegram()" class="bg-white text-purple-600 px-6 py-2 rounded-xl font-semibold hover:shadow-lg transition">
                    <i class="fab fa-telegram mr-2"></i>Connect Now
                </button>
            </div>
        </div>
        
        <!-- Right Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- AI Recommendations -->
            <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-blue-600 rounded-2xl shadow-xl p-6 text-white">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold"><i class="fas fa-robot mr-2"></i>AI Recommendations</h2>
                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm">Smart Match</span>
                </div>
                <div id="aiRecommendations" class="grid md:grid-cols-2 gap-4">
                    <div class="animate-pulse">Loading AI matches...</div>
                </div>
            </div>
            
            <!-- Search Filters -->
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h3 class="font-bold text-lg mb-4"><i class="fas fa-search mr-2 text-blue-600"></i>Find Jobs</h3>
                <div class="grid md:grid-cols-3 gap-4">
                    <input type="text" id="searchTitle" placeholder="Job title, keywords" class="border-2 border-gray-300 rounded-xl px-4 py-2 focus:border-blue-500 focus:outline-none">
                    <select id="filterDistrict" class="border-2 border-gray-300 rounded-xl px-4 py-2 focus:border-blue-500 focus:outline-none">
                        <option value="">All Microdistricts</option>
                        <option value="1">1st</option><option value="2">2nd</option><option value="3">3rd</option>
                        <option value="4">4th</option><option value="5">5th</option><option value="6">6th</option>
                        <option value="7">7th</option><option value="8">8th</option><option value="9">9th</option>
                        <option value="10">10th</option><option value="11">11th</option><option value="12">12th</option>
                    </select>
                    <select id="filterType" class="border-2 border-gray-300 rounded-xl px-4 py-2 focus:border-blue-500 focus:outline-none">
                        <option value="">All Types</option>
                        <option value="full">Full Time</option>
                        <option value="part">Part Time</option>
                        <option value="freelance">Freelance</option>
                    </select>
                </div>
            </div>
            
            <!-- Jobs List -->
            <div id="jobsList" class="space-y-4"></div>
        </div>
    </div>

<?php else: ?>
    <!-- EMPLOYER DASHBOARD -->
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Left Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-md p-6 text-center">
                <div class="w-24 h-24 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                    <i class="fas fa-building text-4xl"></i>
                </div>
                <h2 class="text-xl font-bold"><?php echo $fullname; ?></h2>
                <p class="text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i><?php echo $user['microdistrict']; ?>th District</p>
                <button onclick="showCreateJobModal()" class="mt-4 w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition">
                    <i class="fas fa-plus-circle mr-2"></i>Post New Job
                </button>
            </div>
            
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h3 class="font-bold text-lg mb-3"><i class="fas fa-chart-line mr-2 text-green-600"></i>Statistics</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span>Active Jobs</span>
                        <span class="font-bold" id="activeJobsCount">0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Applications</span>
                        <span class="font-bold" id="totalAppsCount">0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Hired Candidates</span>
                        <span class="font-bold" id="hiredCount">0</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Content -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h2 class="text-xl font-bold mb-4"><i class="fas fa-briefcase mr-2 text-blue-600"></i>My Job Posts</h2>
                <div id="myJobs" class="space-y-4"></div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h2 class="text-xl font-bold mb-4"><i class="fas fa-users mr-2 text-green-600"></i>Recent Applications</h2>
                <div id="responsesList" class="space-y-4"></div>
            </div>
        </div>
    </div>
    
    <!-- Create Job Modal -->
    <div id="jobModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl max-w-lg w-full mx-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Post New Job</h2>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-2xl"></i></button>
            </div>
            <form id="createJobForm">
                <input type="text" id="jobTitle" placeholder="Job Title" class="w-full border-2 rounded-xl px-4 py-2 mb-3" required>
                <textarea id="jobDesc" placeholder="Job Description" rows="4" class="w-full border-2 rounded-xl px-4 py-2 mb-3" required></textarea>
                <input type="text" id="jobSkills" placeholder="Required Skills (comma separated)" class="w-full border-2 rounded-xl px-4 py-2 mb-3">
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <input type="number" id="jobSalaryMin" placeholder="Min Salary" class="border-2 rounded-xl px-4 py-2">
                    <input type="number" id="jobSalaryMax" placeholder="Max Salary" class="border-2 rounded-xl px-4 py-2">
                </div>
                <select id="jobDistrict" class="w-full border-2 rounded-xl px-4 py-2 mb-3">
                    <option value="">Select Microdistrict</option>
                    <option value="1">1st</option><option value="2">2nd</option><option value="3">3rd</option>
                    <option value="4">4th</option><option value="5">5th</option><option value="6">6th</option>
                    <option value="7">7th</option><option value="8">8th</option><option value="9">9th</option>
                    <option value="10">10th</option><option value="11">11th</option><option value="12">12th</option>
                </select>
                <select id="jobType" class="w-full border-2 rounded-xl px-4 py-2 mb-4">
                    <option value="full">Full Time</option>
                    <option value="part">Part Time</option>
                    <option value="freelance">Freelance</option>
                </select>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-xl font-semibold">Post Job</button>
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-200 py-3 rounded-xl font-semibold">Cancel</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

</main>

<script src="script.js"></script>
<script>
<?php if($role == 'seeker'): ?>
    loadAIMatches();
    loadJobs();
    document.getElementById('searchTitle').addEventListener('input', loadJobs);
    document.getElementById('filterDistrict').addEventListener('change', loadJobs);
    document.getElementById('filterType').addEventListener('change', loadJobs);
<?php else: ?>
    loadMyJobs();
    loadResponses();
<?php endif; ?>

function shareProfile() {
    navigator.clipboard.writeText(window.location.href);
    showToast('Profile link copied!', 'success');
}

function connectTelegram() {
    showToast('Coming soon! Join our Telegram @MangystauJobsBot', 'info');
}
</script>

</body>
</html>