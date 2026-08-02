<?php
session_start();

// Handle dummy login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    // Basic dummy auth
    $_SESSION['logged_in'] = true;
    
        // Extract name from email (e.g., sarthak@fraudeye.com -> Sarthak) or default to Sarthak Salunke
    if ($email === 'sarthak@fraudeye.com' || $email === 'google_user@gmail.com' || $email === 'admin@fraudeye.com') {
        $_SESSION['username'] = 'Sarthak Salunke';
        $_SESSION['role'] = 'Admin';
    } else {
        $parts = explode('@', $email);
        $_SESSION['username'] = ucfirst($parts[0]);
        $_SESSION['role'] = 'User';
    }
    
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FraudEye</title>
    <!-- Tailwind CSS Browser Compiler -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <!-- Using Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen m-0 p-0">

    <div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-xl shadow-indigo-500/5 border border-gray-100">
        <!-- Logo -->
        <div class="flex flex-col items-center mb-8 text-center">
            <div class="w-16 h-16 rounded-xl bg-[#0f152a] flex items-center justify-center shadow-lg mb-4">
                <i class="ph-fill ph-shield-check text-3xl text-white"></i>
            </div>
            <h1 class="text-2xl font-black text-gray-800 tracking-tight m-0">Welcome Back</h1>
            <p class="text-sm text-gray-500 font-medium mt-1">Sign in to FraudEye Dashboard</p>
        </div>

        <!-- Form -->
        <form method="POST" action="login.php" class="space-y-5">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-2">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="ph-fill ph-envelope"></i>
                    </div>
                    <input type="email" name="email" value="sarthak@fraudeye.com" required 
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-gray-700 transition-colors" />
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-gray-700">Password</label>
                    <a href="#" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 no-underline">Forgot Password?</a>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="ph-fill ph-lock-key"></i>
                    </div>
                    <input type="password" id="password-input" name="password" value="S@rthak#2026!Secure" required 
                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}"
                        title="Must contain at least one number, one uppercase, one lowercase, one special character, and at least 8 characters"
                        class="w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-gray-700 transition-colors" />
                    <button type="button" onclick="togglePassword()" tabindex="-1" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer border-none bg-transparent">
                        <i id="password-icon" class="ph-fill ph-eye text-lg"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="remember" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                <label for="remember" class="ml-2 block text-xs font-medium text-gray-600 cursor-pointer">
                    Remember me for 30 days
                </label>
            </div>

            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors cursor-pointer mt-2">
                Sign In
            </button>
        </form>

        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-2 bg-white text-gray-500 font-medium">Or continue with</span>
                </div>
            </div>

            <div class="mt-6">
                <button type="button" onclick="document.getElementById('google-modal').classList.remove('hidden')" class="w-full flex items-center justify-center gap-3 py-2.5 px-4 border border-gray-200 rounded-lg shadow-sm bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5">
                    Sign in with Google
                </button>
            </div>
        </div>

        <div class="mt-8 text-center border-t border-gray-50 pt-6">
            <p class="text-xs text-gray-500 font-medium m-0">
                Don't have an account? <a href="#" class="font-bold text-indigo-600 hover:text-indigo-800 no-underline">Request Access</a>
            </p>
        </div>
    </div>

    <!-- Google Account Chooser Modal -->
    <div id="google-modal" class="hidden fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all">
            <div class="p-6 text-center border-b border-gray-100">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-10 h-10 mx-auto mb-4">
                <h3 class="text-xl font-medium text-gray-900 m-0 tracking-tight">Choose an account</h3>
                <p class="text-sm text-gray-500 mt-1">to continue to FraudEye</p>
            </div>
            <div class="p-2">
                <form id="google-login-form" method="POST" action="login.php">
                    <input type="hidden" name="email" id="google-email-input" value="">
                    
                    <button type="button" onclick="submitGoogle('sarthak@fraudeye.com')" class="w-full flex items-center gap-4 p-3 hover:bg-gray-50 rounded-xl transition-colors cursor-pointer text-left border-none bg-transparent">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-lg border border-indigo-200">S</div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">Sarthak Salunke</div>
                            <div class="text-[11px] text-gray-500">sarthak@fraudeye.com</div>
                        </div>
                    </button>
                    
                    <div class="h-px bg-gray-100 my-1 mx-3"></div>
                    
                    <button type="button" onclick="submitGoogle('google_user@gmail.com')" class="w-full flex items-center gap-4 p-3 hover:bg-gray-50 rounded-xl transition-colors cursor-pointer text-left border-none bg-transparent">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 font-bold text-lg border border-gray-200">G</div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">Google User</div>
                            <div class="text-[11px] text-gray-500">google_user@gmail.com</div>
                        </div>
                    </button>
                </form>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button type="button" onclick="document.getElementById('google-modal').classList.add('hidden')" class="px-5 py-2 text-sm font-bold text-gray-600 hover:text-gray-900 hover:bg-gray-200 rounded-lg cursor-pointer border-none bg-transparent transition-colors">Cancel</button>
            </div>
        </div>
    </div>
    
    <script>
        function submitGoogle(email) {
            document.getElementById('google-email-input').value = email;
            document.getElementById('google-login-form').submit();
        }

        function togglePassword() {
            const input = document.getElementById('password-input');
            const icon = document.getElementById('password-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ph-eye', 'ph-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('ph-eye-slash', 'ph-eye');
            }
        }
    </script>

</body>
</html>
