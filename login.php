<?php
session_start();
require_once 'database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Back | Mangystau Jobs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-purple-100 min-h-screen flex items-center justify-center">

<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="index.html" class="inline-flex items-center gap-2 text-2xl font-bold">
                <span>🏝️</span>
                <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Mangystau</span>
                <span class="text-gray-800">Jobs</span>
            </a>
        </div>
        
        <!-- Login Card -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6 text-center">
                <i class="fas fa-sign-in-alt text-4xl text-white mb-2 block"></i>
                <h1 class="text-3xl font-bold text-white">Welcome Back!</h1>
                <p class="text-white/80 mt-2">Sign in to continue</p>
            </div>
            
            <div class="p-8">
                <?php if($error): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-6">
                        <label class="block font-semibold text-gray-700 mb-2">Email Address</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="email" name="email" required class="w-full border-2 border-gray-300 rounded-xl pl-12 pr-4 py-3 focus:border-blue-500 focus:outline-none transition" placeholder="your@email.com">
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block font-semibold text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="password" name="password" required class="w-full border-2 border-gray-300 rounded-xl pl-12 pr-4 py-3 focus:border-blue-500 focus:outline-none transition" placeholder="••••••••">
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="#" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 rounded-xl font-semibold text-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-600">Don't have an account? <a href="register.php" class="text-blue-600 font-semibold hover:underline">Create one</a></p>
                </div>
                
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i> Demo: demo@test.com / 123456
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>