<?php
session_start();
require_once 'database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $microdistrict = $_POST['microdistrict'];
    $skills = $_POST['skills'] ?? '';
    $experience = $_POST['experience'] ?? 0;
    
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    
    if ($check->rowCount() > 0) {
        $error = "Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (email, password, fullname, phone, role, microdistrict, skills, experience) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$email, $password, $fullname, $phone, $role, $microdistrict, $skills, $experience])) {
            $_SESSION['user_id'] = $conn->lastInsertId();
            $_SESSION['fullname'] = $fullname;
            $_SESSION['role'] = $role;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Registration failed!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Mangystau Jobs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#3B82F6' } } }
        }
    </script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-purple-100 min-h-screen">

<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="index.html" class="inline-flex items-center gap-2 text-2xl font-bold">
                <span>🏝️</span>
                <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Mangystau</span>
                <span class="text-gray-800">Jobs</span>
            </a>
        </div>
        
        <!-- Registration Card -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
                <h1 class="text-3xl font-bold text-white">Create Account</h1>
                <p class="text-white/80 mt-2">Join the largest job platform in Mangystau</p>
            </div>
            
            <div class="p-8">
                <?php if($error): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" onsubmit="return validateRegister()">
                    <!-- Role Selection -->
                    <div class="mb-6">
                        <label class="block font-semibold text-gray-700 mb-2">I am a:</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition <?php echo (!isset($_POST['role']) || $_POST['role'] == 'seeker') ? 'border-blue-600 bg-blue-50' : 'border-gray-300 hover:border-blue-400'; ?>">
                                <input type="radio" name="role" value="seeker" class="absolute opacity-0" onchange="toggleRoleFields()" checked>
                                <div class="text-center">
                                    <i class="fas fa-user-graduate text-2xl mb-1 block"></i>
                                    <span class="font-semibold">Job Seeker</span>
                                </div>
                            </label>
                            <label class="relative flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition <?php echo (isset($_POST['role']) && $_POST['role'] == 'employer') ? 'border-purple-600 bg-purple-50' : 'border-gray-300 hover:border-purple-400'; ?>">
                                <input type="radio" name="role" value="employer" class="absolute opacity-0" onchange="toggleRoleFields()">
                                <div class="text-center">
                                    <i class="fas fa-building text-2xl mb-1 block"></i>
                                    <span class="font-semibold">Employer</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Personal Info -->
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="fullname" required class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone" required class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:outline-none transition" placeholder="+7 777 123 4567">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block font-semibold text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" required class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:outline-none transition">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block font-semibold text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" required class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:outline-none transition">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block font-semibold text-gray-700 mb-2">Microdistrict in Aktau</label>
                        <select name="microdistrict" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:outline-none">
                            <option value="1">1st Microdistrict</option>
                            <option value="2">2nd Microdistrict</option>
                            <option value="3">3rd Microdistrict</option>
                            <option value="4">4th Microdistrict</option>
                            <option value="5">5th Microdistrict</option>
                            <option value="6">6th Microdistrict</option>
                            <option value="7">7th Microdistrict</option>
                            <option value="8">8th Microdistrict</option>
                            <option value="9">9th Microdistrict</option>
                            <option value="10">10th Microdistrict</option>
                            <option value="11">11th Microdistrict</option>
                            <option value="12">12th Microdistrict</option>
                            <option value="13">13th Microdistrict</option>
                            <option value="14">14th Microdistrict</option>
                            <option value="15">15th Microdistrict</option>
                        </select>
                    </div>
                    
                    <!-- Seeker Fields -->
                    <div id="seekerFields">
                        <div class="mb-4">
                            <label class="block font-semibold text-gray-700 mb-2">Your Skills <span class="text-sm text-gray-500">(comma separated)</span></label>
                            <input type="text" name="skills" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="e.g., bartender, cashier, sales, english">
                        </div>
                        <div class="mb-4">
                            <label class="block font-semibold text-gray-700 mb-2">Years of Experience</label>
                            <input type="number" name="experience" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:outline-none" placeholder="0">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 rounded-xl font-semibold text-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-user-plus mr-2"></i>Create Account
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-600">Already have an account? <a href="login.php" class="text-blue-600 font-semibold hover:underline">Sign In</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRoleFields() {
    const role = document.querySelector('input[name="role"]:checked').value;
    const seekerFields = document.getElementById('seekerFields');
    if(role === 'seeker') {
        seekerFields.style.display = 'block';
    } else {
        seekerFields.style.display = 'none';
    }
}
toggleRoleFields();

function validateRegister() {
    const password = document.querySelector('input[name="password"]').value;
    if(password.length < 4) {
        alert('Password must be at least 4 characters');
        return false;
    }
    return true;
}
</script>

</body>
</html>