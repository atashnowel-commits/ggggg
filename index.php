<?php
require 'db_connect.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// AJAX endpoint: fetch announcement details by ID
if (isset($_GET['action']) && $_GET['action'] === 'get_announcement' && isset($_GET['id'])) {
    header('Content-Type: application/json; charset=utf-8');
    $id = intval($_GET['id']);
    $stmt = mysqli_prepare($conn, "SELECT a.*, u.first_name, u.last_name
                                    FROM announcements a
                                    LEFT JOIN users u ON a.created_by = u.id
                                    WHERE a.id = ? AND a.is_active = 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $row['author'] = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
        if (empty($row['author'])) $row['author'] = 'Admin';
        $row['date'] = date('M j, Y', strtotime($row['published_at'] ?? $row['created_at']));
        echo json_encode(['success' => true, 'announcement' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Announcement not found']);
    }
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        handleLogin($conn);
    } elseif (isset($_POST['register'])) {
        handleRegister($conn);
    }
}


function handleLogin($conn) {
    $email = trim($_POST['loginEmail']);
    $password = $_POST['loginPassword'];

    // Check if user exists - use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        echo "<script>alert('Database error. Please try again.');</script>";
        return;
    }

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Check if user is active
        $is_active = true;
        if (isset($user['status'])) {
            $is_active = ($user['status'] == 'active');
        }

        if (!$is_active) {
            echo "<script>alert('Account is deactivated. Please contact administrator.');</script>";
            return;
        }

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_email'] = $user['email'];

            // Log login activity using prepared statement
            $logStmt = mysqli_prepare($conn, "INSERT INTO activity_logs (user_id, action, ip_address) VALUES (?, 'LOGIN', ?)");
            $userId = $user['id'];
            $ipAddr = $_SERVER['REMOTE_ADDR'];
            mysqli_stmt_bind_param($logStmt, "is", $userId, $ipAddr);

            if (!mysqli_stmt_execute($logStmt)) {
                // If still failing, try without ip_address too
                $logStmt2 = mysqli_prepare($conn, "INSERT INTO activity_logs (user_id, action) VALUES (?, 'LOGIN')");
                mysqli_stmt_bind_param($logStmt2, "i", $userId);
                mysqli_stmt_execute($logStmt2);
            }

            // Redirect based on user type
            switch ($user['user_type']) {
                case 'PARENT':
                    header("Location: parent-dashboard.php");
                    break;
                case 'DOCTOR':
                case 'DOCTOR_OWNER':
                    header("Location: doctor-dashboard.php");
                    break;
                case 'ADMIN':
                    header("Location: admin-dashboard.php");
                    break;
                default:
                    header("Location: parent-dashboard.php");
            }
            exit();
        } else {
            echo "<script>alert('Invalid email or password');</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password');</script>";
    }
}

function handleRegister($conn) {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['registerEmail']);
    $phone = trim($_POST['phoneNumber']);
    $password = $_POST['registerPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    $dateOfBirth = isset($_POST['dateOfBirth']) ? trim($_POST['dateOfBirth']) : null;
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : null;
    $address = isset($_POST['address']) ? trim($_POST['address']) : null;
    $emergencyContactName = isset($_POST['emergencyContactName']) ? trim($_POST['emergencyContactName']) : null;
    $emergencyContactPhone = isset($_POST['emergencyContactPhone']) ? trim($_POST['emergencyContactPhone']) : null;
    $userType = 'PARENT';

    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password)) {
        echo "<script>alert('Please fill in all required fields.');</script>";
        return;
    }

    // Validate passwords match
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match');</script>";
        return;
    }

    // Validate password strength
    if (strlen($password) < 6 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password)
        || !preg_match('/[0-9]/', $password) || !preg_match('/[!@#$%^&*]/', $password)) {
        echo "<script>alert('Password must be at least 6 characters with uppercase, lowercase, number, and special character.');</script>";
        return;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address.');</script>";
        return;
    }

    // Check if email already exists
    $checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($checkStmt, "s", $email);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);

    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Email already registered. Please use a different email or login.');</script>";
        mysqli_stmt_close($checkStmt);
        return;
    }
    mysqli_stmt_close($checkStmt);

    // Validate phone number - Philippine format (9XXXXXXXXX)
    $cleanPhone = preg_replace('/\s+/', '', $phone);
    if (!preg_match('/^9\d{9}$/', $cleanPhone)) {
        echo "<script>alert('Please enter a valid Philippine mobile number (10 digits starting with 9)');</script>";
        return;
    }

    // Validate date of birth if provided
    if ($dateOfBirth && !strtotime($dateOfBirth)) {
        $dateOfBirth = null;
    }

    // Validate gender if provided
    $validGenders = ['MALE', 'FEMALE', 'OTHER'];
    if ($gender && !in_array($gender, $validGenders)) {
        $gender = null;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user with all collected fields
    $insertStmt = mysqli_prepare($conn, "INSERT INTO users (first_name, last_name, email, phone, password, user_type, status, date_of_birth, gender, address, emergency_contact_name, emergency_contact_phone, created_at)
                   VALUES (?, ?, ?, ?, ?, ?, 'active', ?, ?, ?, ?, ?, NOW())");
    mysqli_stmt_bind_param($insertStmt, "sssssssssss", $firstName, $lastName, $email, $cleanPhone, $hashedPassword, $userType, $dateOfBirth, $gender, $address, $emergencyContactName, $emergencyContactPhone);

    if (mysqli_stmt_execute($insertStmt)) {
        $newUserId = mysqli_insert_id($conn);
        mysqli_stmt_close($insertStmt);

        // Log registration activity
        $logStmt = mysqli_prepare($conn, "INSERT INTO activity_logs (user_id, action, timestamp) VALUES (?, 'User registered', NOW())");
        mysqli_stmt_bind_param($logStmt, "i", $newUserId);
        mysqli_stmt_execute($logStmt);
        mysqli_stmt_close($logStmt);

        // Auto-login after registration
        session_regenerate_id(true);
        $_SESSION['user_id'] = $newUserId;
        $_SESSION['user_type'] = $userType;
        $_SESSION['user_name'] = $firstName . ' ' . $lastName;
        $_SESSION['user_email'] = $email;
        $_SESSION['first_name'] = $firstName;
        $_SESSION['last_name'] = $lastName;

        header("Location: parent-dashboard.php");
        exit();
    } else {
        mysqli_stmt_close($insertStmt);
        error_log("Registration failed: " . mysqli_error($conn));
        echo "<script>alert('Registration failed. Please try again later.');</script>";
    }
}

// Display success/error messages from session
if (isset($_SESSION['message'])) {
    $safeMessage = htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8');
    echo "<script>alert(" . json_encode($safeMessage) . ");</script>";
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlagApp Clinic - Pediatric Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pixi.js/7.3.2/pixi.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Source+Sans+Pro:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="css/shared.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
    <style>
        :root {
            --primary-pink: #d03664;
            --light-pink: #FFBCD9;
            --dark-text: #333333;
            --light-gray: #f8f0f4;
        }
        
        body {
            font-family: 'Source Sans Pro', sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #fef5f8 100%);
        }
        
        .font-inter { font-family: 'Inter', sans-serif; }
        
        .text-primary { color: var(--primary-pink); }
        .bg-primary { background-color: var(--primary-pink); }
        .bg-light-pink { background-color: var(--light-pink); }
        
        .hero-bg {
            background: linear-gradient(rgba(255, 107, 154, 0.1), rgba(255, 188, 217, 0.1)), 
                        url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1953&q=80') center/cover;
            min-height: 100vh;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(255, 107, 154, 0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-pink), var(--light-pink));
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 154, 0.3);
        }
        
        .floating-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--light-pink);
            border-radius: 50%;
            opacity: 0.6;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .modal-backdrop {
            backdrop-filter: blur(8px);
            background: rgba(0, 0, 0, 0.4);
        }
        
        .typewriter {
            overflow: hidden;
            border-right: 3px solid var(--primary-pink);
            white-space: nowrap;
            animation: typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite;
        }
        
        @keyframes typing {
            from { width: 0; }
            to { width: 100%; }
        }
        
        @keyframes blink-caret {
            from, to { border-color: transparent; }
            50% { border-color: var(--primary-pink); }
        }
        
        .captcha-container {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-pink), var(--light-pink));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            transition: all 0.3s ease;
        }
        
        .service-icon:hover {
            transform: scale(1.1) rotate(5deg);
        }
        
        .service-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
        }
        
        /* Custom Splide Styles for Services Carousel */
        #services-carousel .splide__slide {
            padding: 0.5rem;
        }
        
        #services-carousel .splide__arrow {
            position: static;
            transform: none;
            opacity: 1;
            background: var(--primary-pink);
        }
        
        #services-carousel .splide__arrow:disabled {
            opacity: 0.5;
        }
        
        #services-carousel .splide__arrow svg {
            fill: none;
        }
        
        #services-carousel .splide__progress {
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }

        /* Announcement Carousel Styles */
        .announcement-card {
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-pink);
            background: linear-gradient(to bottom right, white, #fdf2f8);
        }

        .announcement-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(255, 107, 154, 0.15);
            background: linear-gradient(to bottom right, white, #fce7f3);
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Pink category badges */
        .category-badge {
            background: var(--light-pink);
            color: var(--primary-pink);
            border: 1px solid rgba(255, 107, 154, 0.3);
        }

        /* Splide custom styles for announcements with pink theme */
        #announcements-carousel .splide__slide {
            padding: 0.5rem;
        }

        #announcements-carousel .splide__arrow {
            position: static;
            transform: none;
            opacity: 1;
            background: linear-gradient(135deg, var(--primary-pink), #ff8fab);
            box-shadow: 0 4px 15px rgba(255, 107, 154, 0.3);
        }

        #announcements-carousel .splide__arrow:hover {
            background: linear-gradient(135deg, #ff5a8c, #ff7aa3);
            transform: scale(1.1);
        }

        #announcements-carousel .splide__arrow:disabled {
            opacity: 0.5;
        }

        #announcements-carousel .splide__arrow svg {
            fill: none;
        }

        #announcements-carousel .splide__progress {
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }

        /* Pink progress bar */
        #announcements-carousel .splide__progress__bar {
            background: #fce7f3;
        }

        #announcements-carousel .splide__progress__bar__fill {
            background: linear-gradient(90deg, var(--primary-pink), #ff8fab);
        }

        /* Modal pink enhancements */
        .modal-header-pink {
            background: linear-gradient(135deg, var(--primary-pink), #ff8fab);
        }

        @media (max-width: 640px) {
        #registerModal {
            padding: 1rem;
            align-items: flex-start;
            padding-top: 2rem;
        }
        
        #registerModal > div {
            max-height: calc(100vh - 2rem);
            margin-top: 2rem;
        }
        
        .grid-cols-2 {
            grid-template-columns: 1fr;
        }
        
        .modal-header-pink h2 {
            font-size: 1.5rem;
        }
        
        .modal-header-piny p {
            font-size: 0.875rem;
        }
    }

    /* Ensure form elements are readable on mobile */
    input, select, textarea {
        font-size: 16px; /* Prevents zoom on iOS */
    }

    /* Improve focus states for accessibility */
    input:focus, select:focus, textarea:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(208, 54, 100, 0.1);
    }

    /* Style for error states */
    .border-red-500 {
        border-color: #ef4444;
    }

    .ring-red-200 {
        --tw-ring-color: rgba(254, 202, 202, 0.5);
    }

    /* Border colors for validation states */
    .border-green-500 {
        border-color: #10B981 !important;
    }
    
    .border-yellow-500 {
        border-color: #F59E0B !important;
    }
    
    .border-red-500 {
        border-color: #EF4444 !important;
    }
    
    /* Ring colors for validation states */
    .ring-green-200 {
        --tw-ring-color: rgba(187, 247, 208, 0.5);
    }
    
    .ring-yellow-200 {
        --tw-ring-color: rgba(253, 230, 138, 0.5);
    }
    
    .ring-red-200 {
        --tw-ring-color: rgba(254, 202, 202, 0.5);
    }
    
    /* Progress bar colors */
    .bg-green-500 {
        background-color: #10B981;
    }
    
    .bg-yellow-500 {
        background-color: #F59E0B;
    }
    
    .bg-red-500 {
        background-color: #EF4444;
    }
    
    /* Background colors for strength indicators */
    .bg-green-50 {
        background-color: #ECFDF5;
    }
    
    .bg-yellow-50 {
        background-color: #FFFBEB;
    }
    
    .bg-red-50 {
        background-color: #FEF2F2;
    }

    @keyframes pulse-once {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .animate-pulse-once {
        animation: pulse-once 0.3s ease-in-out;
    }
    
    .requirement-met {
        background-color: rgba(34, 197, 94, 0.1);
        padding: 2px 4px;
        border-radius: 4px;
        margin: 1px 0;
    }
    
    .requirement-not-met {
        padding: 2px 4px;
        border-radius: 4px;
        margin: 1px 0;
    }
    
    /* Smooth transitions for input states */
    input, select, textarea {
        transition: all 0.3s ease;
    }
    
    input:focus, select:focus, textarea:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(208, 54, 100, 0.1);
    }
    
    /* Custom scrollbar for modal */
    #registerModal > div::-webkit-scrollbar {
        width: 6px;
    }
    
    #registerModal > div::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    #registerModal > div::-webkit-scrollbar-thumb {
        background: var(--primary-pink);
        border-radius: 4px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 640px) {
        #registerModal {
            padding: 0.5rem;
            align-items: flex-start;
            padding-top: 1rem;
        }
        
        #registerModal > div {
            max-height: calc(100vh - 1rem);
            margin-top: 1rem;
            border-radius: 0.75rem;
        }
        
        .modal-header-pink {
            padding: 1rem;
        }
        
        .modal-header-pink h2 {
            font-size: 1.25rem;
        }
        
        .modal-header-pink p {
            font-size: 0.875rem;
        }
        
        .p-8 {
            padding: 1rem;
        }
        
        .gap-6 {
            gap: 1rem;
        }
        
        .space-y-6 > * + * {
            margin-top: 1rem;
        }
        
        .mb-8 {
            margin-bottom: 1.5rem;
        }
        
        input, select, textarea {
            font-size: 16px !important; /* Prevents zoom on iOS */
        }
    }
    
    /* Progress bar animation */
    #passwordStrengthBar {
        transition: width 0.5s ease-in-out, background-color 0.5s ease;
    }
    
    /* Requirement indicator animation */
    #passwordRequirements div {
        transition: all 0.3s ease;
    }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white/95 backdrop-blur-sm shadow-sm z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-inter font-bold text-primary">AlagApp</h1>
                    </div>
                </div>
                
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="#home" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium transition-colors">Home</a>
                        <a href="#services" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium transition-colors">Services</a>
                        <a href="#doctors" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium transition-colors">Doctors</a>
                        <a href="#about" class="text-gray-700 hover:text-primary px-3 py-2 text-sm font-medium transition-colors">About</a>
                        <button onclick="openLoginModal()" class="btn-primary text-white px-6 py-2 rounded-lg font-medium">Login</button>
                    </div>
                </div>
                
                <div class="md:hidden">
                    <button onclick="toggleMobileMenu()" class="text-gray-700 hover:text-primary">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobileMenu" class="md:hidden hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="#home" class="block px-3 py-2 text-gray-700 hover:text-primary">Home</a>
                <a href="#services" class="block px-3 py-2 text-gray-700 hover:text-primary">Services</a>
                <a href="#doctors" class="block px-3 py-2 text-gray-700 hover:text-primary">Doctors</a>
                <a href="#about" class="block px-3 py-2 text-gray-700 hover:text-primary">About</a>
                <button onclick="openLoginModal()" class="w-full text-left px-3 py-2 text-primary font-medium">Login</button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-bg relative flex items-center justify-center">
        <div class="floating-particles">
            <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
            <div class="particle" style="left: 20%; animation-delay: 1s;"></div>
            <div class="particle" style="left: 30%; animation-delay: 2s;"></div>
            <div class="particle" style="left: 40%; animation-delay: 3s;"></div>
            <div class="particle" style="left: 50%; animation-delay: 4s;"></div>
            <div class="particle" style="left: 60%; animation-delay: 5s;"></div>
            <div class="particle" style="left: 70%; animation-delay: 1.5s;"></div>
            <div class="particle" style="left: 80%; animation-delay: 2.5s;"></div>
            <div class="particle" style="left: 90%; animation-delay: 3.5s;"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-5xl md:text-7xl font-inter font-bold text-gray-800 mb-6">
                    <span class="typewriter">Caring for Little Ones</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-600 mb-8 leading-relaxed">
                    Comprehensive pediatric clinic management system designed for modern healthcare. 
                    Streamline appointments, track vaccinations, and manage patient records with ease.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <button onclick="openRegisterModal()" class="btn-primary text-white px-8 py-4 rounded-lg text-lg font-semibold">
                        Get Started Today
                    </button>
                    <button onclick="scrollToServices()" class="border-2 border-primary text-primary px-8 py-4 rounded-lg text-lg font-semibold hover:bg-primary hover:text-white transition-all">
                        Explore Services
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-inter font-bold text-gray-800 mb-4">Our Services</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Comprehensive pediatric healthcare services designed to meet the unique needs of children and families.
                </p>
            </div>
            
            <!-- Services Carousel -->
            <div class="splide" id="services-carousel">
                <div class="splide__track">
                    <ul class="splide__list">
                        <!-- Consultation -->
                        <li class="splide__slide">
                            <div class="card-hover bg-white rounded-xl shadow-lg p-8 text-center border border-gray-100 h-full">
                                <div class="service-icon">
                                    <svg viewBox="0 0 24 24" fill="white">
                                        <path d="M20 6h-4V4c0-1.1-.9-2-2-2h-4c-1.1 0-2 .9-2 2v2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM10 4h4v2h-4V4zm6 11h-3v3h-2v-3H8v-2h3v-3h2v3h3v2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-inter font-semibold text-gray-800 mb-4">Consultation</h3>
                                <p class="text-gray-600 mb-6">
                                    Comprehensive health assessments and medical consultations for children of all ages.
                                </p>
                                <button onclick="openRegisterModal()" class="text-primary font-semibold hover:underline">Book Consultation →</button>
                            </div>
                        </li>
                        
                        <!-- Vaccination -->
                        <li class="splide__slide">
                            <div class="card-hover bg-white rounded-xl shadow-lg p-8 text-center border border-gray-100 h-full">
                                <div class="service-icon">
                                    <svg viewBox="0 0 24 24" fill="white">
                                        <path d="M11 1v4H8l4 4 4-4h-3V1h-2zm-4.83 7L2 12.17l1.41 1.41L5 12l1.41 1.41L5 14.83 6.41 16.24 8 14.66l1.41 1.41-1.41 1.42 1.41 1.41L11 17.32V21h2v-3.68l1.59 1.58 1.41-1.41-1.41-1.42L16 15.07l1.59 1.59 1.41-1.42L17.59 13.83 19 12.41 17.59 11 16 12.59 14.59 11 16 9.59 14.59 8.17 13 9.76 11.41 8.17z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-inter font-semibold text-gray-800 mb-4">Vaccination</h3>
                                <p class="text-gray-600 mb-6">
                                    Complete immunization services with tracking and reminder systems for optimal health.
                                </p>
                                <button onclick="openRegisterModal()" class="text-primary font-semibold hover:underline">Schedule Vaccine →</button>
                            </div>
                        </li>
                        
                        <!-- Well Baby Checkup -->
                        <li class="splide__slide">
                            <div class="card-hover bg-white rounded-xl shadow-lg p-8 text-center border border-gray-100 h-full">
                                <div class="service-icon">
                                    <svg viewBox="0 0 24 24" fill="white">
                                        <path d="M12 12.75c1.63 0 3.07.39 4.24.9 1.08.48 1.76 1.56 1.76 2.73V18H6v-1.61c0-1.18.68-2.26 1.76-2.73 1.17-.52 2.61-.91 4.24-.91zM4 13c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm1.13 1.1c-.37-.06-.74-.1-1.13-.1-.99 0-1.93.21-2.78.58C.48 14.9 0 15.62 0 16.43V18h4.5v-1.61c0-.83.23-1.61.63-2.29zM20 13c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm4 3.43c0-.81-.48-1.53-1.22-1.85-.85-.37-1.79-.58-2.78-.58-.39 0-.76.04-1.13.1.4.68.63 1.46.63 2.29V18H24v-1.57zM12 6c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-inter font-semibold text-gray-800 mb-4">Well Baby Checkup</h3>
                                <p class="text-gray-600 mb-6">
                                    Regular developmental assessments and health monitoring for growing children.
                                </p>
                                <button onclick="openRegisterModal()" class="text-primary font-semibold hover:underline">Learn More →</button>
                            </div>
                        </li>
                        
                        <!-- Pediatric Clearance -->
                        <li class="splide__slide">
                            <div class="card-hover bg-white rounded-xl shadow-lg p-8 text-center border border-gray-100 h-full">
                                <div class="service-icon">
                                    <svg viewBox="0 0 24 24" fill="white">
                                        <path d="M23 12l-2.44-2.79.34-3.69-3.61-.82-1.89-3.2L12 2.96 8.6 1.5 6.71 4.69 3.1 5.5l.34 3.7L1 12l2.44 2.79-.34 3.7 3.61.82L8.6 22.5l3.4-1.47 3.4 1.46 1.89-3.19 3.61-.82-.34-3.69L23 12zm-12.91 4.72l-3.8-3.81 1.48-1.48 2.32 2.33 5.85-5.87 1.48 1.48-7.33 7.35z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-inter font-semibold text-gray-800 mb-4">Pediatric Clearance</h3>
                                <p class="text-gray-600 mb-6">
                                    Medical clearance certificates for school, sports, and other activities.
                                </p>
                                <button onclick="openRegisterModal()" class="text-primary font-semibold hover:underline">Get Clearance →</button>
                            </div>
                        </li>
                        
                        <!-- Referral Services -->
                        <li class="splide__slide">
                            <div class="card-hover bg-white rounded-xl shadow-lg p-8 text-center border border-gray-100 h-full">
                                <div class="service-icon">
                                    <svg viewBox="0 0 24 24" fill="white">
                                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-inter font-semibold text-gray-800 mb-4">Referral Services</h3>
                                <p class="text-gray-600 mb-6">
                                    Coordinated care with specialists and other healthcare providers.
                                </p>
                                <button onclick="openRegisterModal()" class="text-primary font-semibold hover:underline">Request Referral →</button>
                            </div>
                        </li>
                        
                        <!-- Ear Piercing -->
                        <li class="splide__slide">
                            <div class="card-hover bg-white rounded-xl shadow-lg p-8 text-center border border-gray-100 h-full">
                                <div class="service-icon">
                                    <svg viewBox="0 0 24 24" fill="white">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-inter font-semibold text-gray-800 mb-4">Ear Piercing</h3>
                                <p class="text-gray-600 mb-6">
                                    Safe and professional ear piercing services for children with proper aftercare.
                                </p>
                                <button onclick="openRegisterModal()" class="text-primary font-semibold hover:underline">Book Service →</button>
                            </div>
                        </li>
                        
                        <!-- Medical Certification (NEW) -->
                        <li class="splide__slide">
                            <div class="card-hover bg-white rounded-xl shadow-lg p-8 text-center border border-gray-100 h-full">
                                <div class="service-icon">
                                    <svg viewBox="0 0 24 24" fill="white">
                                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v5h5v11H6zm5-5.5l-1.5 1.5L8 16.5 11.5 20l7-7-1.5-1.5L11 17z"/>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-inter font-semibold text-gray-800 mb-4">Medical Certification</h3>
                                <p class="text-gray-600 mb-6">
                                    Official medical certificates for school enrollment, travel, and other official requirements.
                                </p>
                                <button onclick="openRegisterModal()" class="text-primary font-semibold hover:underline">Get Certificate →</button>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <!-- Carousel Progress Bar -->
                <div class="splide__progress mt-8">
                    <div class="splide__progress__bar bg-gray-200 h-1 rounded-full">
                        <div class="splide__progress__bar__fill bg-primary h-full rounded-full"></div>
                    </div>
                </div>
                
                <!-- Custom Navigation -->
                <div class="splide__arrows flex justify-center mt-6 space-x-4">
                    <button class="splide__arrow splide__arrow--prev bg-primary text-white p-3 rounded-full hover:bg-opacity-90 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button class="splide__arrow splide__arrow--next bg-primary text-white p-3 rounded-full hover:bg-opacity-90 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Announcements Carousel Section -->
    <section id="announcements" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-inter font-bold text-gray-800 mb-4">Latest Announcements</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Stay updated with the latest news and important updates from AlagApp Clinic.
                </p>
            </div>
            
            <!-- Announcements Carousel -->
            <div class="splide" id="announcements-carousel">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                        // Fetch active announcements from database with author name
                        $announcementQuery = "SELECT a.*, u.first_name, u.last_name
                                              FROM announcements a
                                              LEFT JOIN users u ON a.created_by = u.id
                                              WHERE a.is_active = 1
                                              AND (a.expires_at IS NULL OR a.expires_at > NOW())
                                              ORDER BY a.created_at DESC";
                        $announcementResult = mysqli_query($conn, $announcementQuery);

                        if ($announcementResult && mysqli_num_rows($announcementResult) > 0) {
                            while ($announcement = mysqli_fetch_assoc($announcementResult)) {
                                $formattedDate = date('M j, Y', strtotime($announcement['published_at'] ?? $announcement['created_at']));
                                $authorName = trim(($announcement['first_name'] ?? '') . ' ' . ($announcement['last_name'] ?? ''));
                                if (empty($authorName)) $authorName = 'Admin';
                                $categoryClass = getCategoryClass($announcement['category']);
                        ?>
                        <li class="splide__slide">
                            <div class="announcement-card bg-white rounded-xl shadow-lg border border-gray-100 p-6 h-full">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="<?php echo $categoryClass; ?> px-3 py-1 rounded-full text-sm font-medium">
                                        <?php echo htmlspecialchars($announcement['category']); ?>
                                    </span>
                                    <span class="text-sm text-gray-500"><?php echo $formattedDate; ?></span>
                                </div>

                                <h3 class="text-xl font-inter font-semibold text-gray-800 mb-3">
                                    <?php echo htmlspecialchars($announcement['title']); ?>
                                </h3>

                                <p class="text-gray-600 mb-4 line-clamp-3">
                                    <?php echo htmlspecialchars(substr(strip_tags($announcement['content']), 0, 200)); ?>
                                </p>

                                <div class="flex items-center justify-between mt-auto">
                                    <span class="text-sm text-gray-500">By: <?php echo htmlspecialchars($authorName); ?></span>
                                    <button onclick="openAnnouncementModal(<?php echo $announcement['id']; ?>)"
                                            class="text-primary hover:text-primary-dark text-sm font-medium transition-colors">
                                        Read More &rarr;
                                    </button>
                                </div>
                            </div>
                        </li>
                        <?php
                            }
                        } else {
                        ?>
                        <li class="splide__slide">
                            <div class="announcement-card bg-white rounded-xl shadow-lg border border-gray-100 p-6 h-full text-center">
                                <div class="service-icon mx-auto mb-4">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H5.2L4 17.2V4H20V16Z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-inter font-semibold text-gray-800 mb-3">No Announcements</h3>
                                <p class="text-gray-600">Check back later for updates and important information.</p>
                            </div>
                        </li>
                        <?php
                        }

                        // Helper function for category styling
                        function getCategoryClass($category) {
                            switch (strtoupper($category)) {
                                case 'MAINTENANCE':
                                    return 'bg-yellow-100 text-yellow-800';
                                case 'HEALTH_ADVISORY':
                                    return 'bg-red-100 text-red-800';
                                case 'EVENT':
                                    return 'bg-green-100 text-green-800';
                                case 'PROMOTION':
                                    return 'bg-purple-100 text-purple-800';
                                default:
                                    return 'bg-blue-100 text-blue-800';
                            }
                        }
                        ?>
                    </ul>
                </div>
                
                <!-- Carousel Progress Bar -->
                <div class="splide__progress mt-8">
                    <div class="splide__progress__bar bg-gray-200 h-1 rounded-full">
                        <div class="splide__progress__bar__fill bg-primary h-full rounded-full"></div>
                    </div>
                </div>
                
                <!-- Custom Navigation -->
                <div class="splide__arrows flex justify-center mt-6 space-x-4">
                    <button class="splide__arrow splide__arrow--prev bg-primary text-white p-3 rounded-full hover:bg-opacity-90 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button class="splide__arrow splide__arrow--next bg-primary text-white p-3 rounded-full hover:bg-opacity-90 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Announcement Detail Modal -->
    <div id="announcementModal" class="fixed inset-0 modal-backdrop hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border-2 border-primary">
            <div class="p-8">
                <!-- Modal Header with Pink Background -->
                <div class="bg-gradient-to-r from-primary to-pink-400 -m-8 mb-6 p-6 rounded-t-xl">
                    <div class="flex items-center justify-between text-white">
                        <div>
                            <span id="modalCategory" class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium backdrop-blur-sm"></span>
                            <span id="modalDate" class="text-pink-100 text-sm ml-3"></span>
                        </div>
                        <button onclick="closeAnnouncementModal()" class="text-white hover:text-pink-100 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <h2 id="modalTitle" class="text-2xl font-inter font-bold text-white mt-4"></h2>
                    <p id="modalAuthor" class="text-pink-100 text-sm mt-2"></p>
                </div>
                
                <!-- Modal Content -->
                <div id="announcementContent" class="prose prose-lg max-w-none text-gray-600">
                    <!-- Content will be loaded here via JavaScript -->
                </div>
                
                <div class="mt-8 text-center">
                    <button onclick="closeAnnouncementModal()" class="btn-primary text-white px-8 py-3 rounded-lg font-medium hover:shadow-lg transition-all">
                        Close Announcement
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Doctors Section -->
    <section id="doctors" class="py-20 bg-light-pink/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-inter font-bold text-gray-800 mb-4">Our Medical Team</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Meet our experienced pediatric specialists dedicated to providing exceptional care for your children.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                // Fetch all active doctors from the database
                $doctorQuery = "SELECT id, first_name, last_name, specialization, years_of_experience FROM users WHERE user_type IN ('DOCTOR', 'DOCTOR_OWNER') AND status = 'active' ORDER BY years_of_experience DESC LIMIT 6";
                $doctorResult = mysqli_query($conn, $doctorQuery);

                if ($doctorResult && mysqli_num_rows($doctorResult) > 0) {
                    $doctorColors = ['from-pink-400 to-rose-400', 'from-blue-400 to-indigo-400', 'from-green-400 to-teal-400', 'from-purple-400 to-violet-400', 'from-orange-400 to-amber-400', 'from-cyan-400 to-sky-400'];
                    $colorIndex = 0;

                    while ($doctor = mysqli_fetch_assoc($doctorResult)) {
                        $gradient = $doctorColors[$colorIndex % count($doctorColors)];
                        $colorIndex++;
                        $initials = strtoupper(substr($doctor['first_name'], 0, 1) . substr($doctor['last_name'], 0, 1));
                        $experience = $doctor['years_of_experience'] ? $doctor['years_of_experience'] . '+ years' : '';
                ?>
                <div class="card-hover bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r <?php echo $gradient; ?> p-6 text-center">
                        <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-white text-2xl font-bold"><?php echo htmlspecialchars($initials); ?></span>
                        </div>
                        <h3 class="text-xl font-inter font-bold text-white">
                            Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?>
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <svg class="w-5 h-5 text-primary mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($doctor['specialization'] ?? 'Pediatrician'); ?></span>
                        </div>
                        <?php if ($experience): ?>
                        <div class="flex items-center mb-3">
                            <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-600"><?php echo htmlspecialchars($experience); ?> experience</span>
                        </div>
                        <?php endif; ?>
                        <button onclick="openRegisterModal()" class="w-full mt-3 btn-primary text-white py-2.5 rounded-lg font-medium text-sm">
                            Book Appointment
                        </button>
                    </div>
                </div>
                <?php
                    }
                } else {
                ?>
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">Our medical team information will be available soon.</p>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-inter font-bold text-gray-800 mb-6">Why Choose AlagApp?</h2>
                    <p class="text-xl text-gray-600 mb-8">
                        Our comprehensive clinic management system streamlines healthcare delivery, making it easier for parents, doctors, and administrators to focus on what matters most - your child's health.
                    </p>
                    
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Easy Appointment Booking</h3>
                                <p class="text-gray-600">Schedule appointments online with real-time availability and instant confirmation.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Vaccination Tracking</h3>
                                <p class="text-gray-600">Keep track of your child's immunization schedule with automated reminders.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Secure Medical Records</h3>
                                <p class="text-gray-600">Access and manage your child's health information securely from anywhere.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">24/7 Access</h3>
                                <p class="text-gray-600">Manage appointments and view records anytime, anywhere with our secure platform.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80" alt="Happy children in clinic" class="rounded-xl shadow-lg w-full">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-xl"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h3 class="text-2xl font-inter font-bold text-primary mb-4">AlagApp Clinic</h3>
                <p class="text-gray-300 mb-6">Comprehensive pediatric healthcare management system</p>
                <div class="flex justify-center space-x-6 mb-8">
                    <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046.867-.22c1.489-.107 3.045.815 3.045 3.846v6.245zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                </div>
                <p class="text-gray-400 text-sm">
                    © <?php echo date('Y'); ?> AlagApp Clinic. All rights reserved. | Privacy Policy | Terms of Service
                </p>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 modal-backdrop hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
            <div class="p-8">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-inter font-bold text-gray-800 mb-2">Welcome Back</h2>
                    <p class="text-gray-600">Sign in to your AlagApp account</p>
                </div>
                
                <form id="loginForm" method="POST" onsubmit="return validateLoginForm()">
                    <input type="hidden" name="login" value="1">
                    
                    <div class="space-y-6">
                        <div>
                            <label for="loginEmail" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="loginEmail" name="loginEmail" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                placeholder="Enter your email"
                                value="<?php echo isset($_POST['loginEmail']) ? htmlspecialchars($_POST['loginEmail']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="loginPassword" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input type="password" id="loginPassword" name="loginPassword" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                placeholder="Enter your password">
                            <div class="mt-1 flex justify-end">
                                <button type="button" onclick="togglePasswordVisibility('loginPassword')" class="text-sm text-primary hover:underline">
                                    Show Password
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" name="rememberMe" class="rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="ml-2 text-sm text-gray-600">Remember me</span>
                            </label>
                            <button type="button" onclick="showForgotPassword()" class="text-sm text-primary hover:underline">Forgot password?</button>
                        </div>
                        
                        <button type="submit" class="w-full btn-primary text-white py-3 rounded-lg font-semibold transition-all duration-200 hover:scale-105">
                            <span id="loginButtonText">Sign In</span>
                            <div id="loginSpinner" class="hidden inline-block ml-2">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                            </div>
                        </button>
                    </div>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account? 
                        <button onclick="switchToRegister()" class="text-primary font-semibold hover:underline">Sign up</button>
                    </p>
                </div>
                
                <button onclick="closeLoginModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Register Modal - Complete Redesign -->
    <div id="registerModal" class="fixed inset-0 modal-backdrop hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[95vh] overflow-y-auto border-2 border-primary">
            <!-- Modal Header -->
            <div class="modal-header-pink p-6 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <h2 class="text-3xl font-inter font-bold">Join AlagApp Clinic</h2>
                        <p class="text-pink-100 mt-2">Create your parent account to get started</p>
                    </div>
                    <button onclick="closeRegisterModal()" class="text-white hover:text-pink-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Progress Steps -->
            <div class="px-8 pt-4">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-semibold">1</div>
                        <span class="text-sm font-medium text-gray-700">Personal Info</span>
                    </div>
                    <div class="flex-1 h-1 mx-4 bg-gray-200"></div>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-semibold">2</div>
                        <span class="text-sm text-gray-500">Contact & Security</span>
                    </div>
                </div>
            </div>
            
            <!-- Registration Form -->
            <div class="p-8">
                <form id="registerForm" method="POST" onsubmit="return validateRegisterForm()">
                    <input type="hidden" name="register" value="1">
                    
                    <!-- Section 1: Personal Information -->
                    <div class="mb-8">
                        <h3 class="text-xl font-inter font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            Personal Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div>
                                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="firstName" name="firstName" required 
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                        placeholder="Enter first name"
                                        value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>">
                                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Last Name -->
                            <div>
                                <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="lastName" name="lastName" required 
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                        placeholder="Enter last name"
                                        value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>">
                                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Date of Birth -->
                            <div>
                                <label for="dateOfBirth" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date of Birth <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="date" id="dateOfBirth" name="dateOfBirth" required 
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                        max="<?php echo date('Y-m-d'); ?>">
                                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Gender -->
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                                    Gender <span class="text-red-500">*</span>
                                </label>
                                <select id="gender" name="gender" required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 appearance-none">
                                    <option value="">Select gender</option>
                                    <option value="MALE">Male</option>
                                    <option value="FEMALE">Female</option>
                                    <option value="OTHER">Other / Prefer not to say</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 2: Contact & Address -->
                    <div class="mb-8">
                        <h3 class="text-xl font-inter font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            Contact Information
                        </h3>
                        
                        <div class="space-y-6">
                            <!-- Email Address -->
                            <div>
                                <label for="registerEmail" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="email" id="registerEmail" name="registerEmail" required 
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                        placeholder="your.email@example.com"
                                        value="<?php echo isset($_POST['registerEmail']) ? htmlspecialchars($_POST['registerEmail']) : ''; ?>"
                                        onblur="validateEmail()">
                                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div id="emailError" class="mt-1 text-sm text-red-600 hidden"></div>
                            </div>
                            
                            <!-- Phone Number -->
                            <div>
                                <label for="phoneNumber" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">+63</span>
                                    </div>
                                    <input type="tel" id="phoneNumber" name="phoneNumber" required
                                        class="w-full pl-16 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                        placeholder="9XX XXX XXXX"
                                        maxlength="12"
                                        title="Philippine mobile number format: 9XXXXXXXXX (10 digits starting with 9)"
                                        value="<?php echo isset($_POST['phoneNumber']) ? htmlspecialchars($_POST['phoneNumber']) : ''; ?>"
                                        oninput="formatPhoneNumber(this)">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Philippine mobile number format: 9XXXXXXXXX</p>
                            </div>
                            
                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Address
                                </label>
                                <textarea id="address" name="address" rows="2"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 resize-none"
                                    placeholder="Enter your complete address (optional)"></textarea>
                            </div>
                            
                            <!-- Emergency Contact Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="emergencyContactName" class="block text-sm font-medium text-gray-700 mb-2">
                                        Emergency Contact Name
                                    </label>
                                    <input type="text" id="emergencyContactName" name="emergencyContactName"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                        placeholder="Full name of emergency contact">
                                </div>
                                <div>
                                    <label for="emergencyContactPhone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Emergency Contact Phone
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500">+63</span>
                                        </div>
                                        <input type="tel" id="emergencyContactPhone" name="emergencyContactPhone"
                                            class="w-full pl-16 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                            placeholder="9XX XXX XXXX"
                                            pattern="[9]\d{9}"
                                            maxlength="10"
                                            title="Philippine mobile number format: 9XXXXXXXXX">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 3: Account Security -->
                    <div class="mb-8">
                        <h3 class="text-xl font-inter font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            Account Security
                        </h3>
                        
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Password -->
                                <div>
                                    <label for="registerPassword" class="block text-sm font-medium text-gray-700 mb-2">
                                        Password <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="registerPassword" name="registerPassword" required 
                                            class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 password-strength-input"
                                            placeholder="Create secure password"
                                            oninput="checkPasswordStrength(this.value)"
                                            onfocus="showPasswordRequirements()"
                                            onblur="hidePasswordRequirements()">
                                        <button type="button" onclick="togglePasswordVisibility('registerPassword')" 
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Password Strength Indicator -->
                                    <div class="mt-2 flex justify-between items-center">
                                        <div id="passwordStrength" class="text-sm font-medium"></div>
                                    </div>
                                </div>
                                
                                <!-- Confirm Password -->
                                <div>
                                    <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">
                                        Confirm Password <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="confirmPassword" name="confirmPassword" required 
                                            class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 password-match-input"
                                            placeholder="Confirm your password"
                                            oninput="checkPasswordMatch()">
                                        <button type="button" onclick="togglePasswordVisibility('confirmPassword')" 
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div id="passwordMatch" class="mt-2 text-sm hidden"></div>
                                </div>
                            </div>
                            
                            <!-- Password Requirements Panel -->
                            <div id="passwordRequirements" class="mt-4 hidden bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <p class="text-sm font-medium text-gray-700 mb-3">Password Requirements:</p>
                                <div class="space-y-2">
                                    <div id="reqLength" class="flex items-center transition-all duration-300">
                                        <span class="mr-2 text-gray-400">○</span>
                                        <span class="text-xs text-gray-600">At least 6 characters</span>
                                    </div>
                                    <div id="reqUppercase" class="flex items-center transition-all duration-300">
                                        <span class="mr-2 text-gray-400">○</span>
                                        <span class="text-xs text-gray-600">One uppercase letter (A-Z)</span>
                                    </div>
                                    <div id="reqLowercase" class="flex items-center transition-all duration-300">
                                        <span class="mr-2 text-gray-400">○</span>
                                        <span class="text-xs text-gray-600">One lowercase letter (a-z)</span>
                                    </div>
                                    <div id="reqNumber" class="flex items-center transition-all duration-300">
                                        <span class="mr-2 text-gray-400">○</span>
                                        <span class="text-xs text-gray-600">One number (0-9)</span>
                                    </div>
                                    <div id="reqSpecial" class="flex items-center transition-all duration-300">
                                        <span class="mr-2 text-gray-400">○</span>
                                        <span class="text-xs text-gray-600">One special character (!@#$%^&*)</span>
                                    </div>
                                </div>
                                
                                <!-- Visual Strength Meter -->
                                <div class="mt-3">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span>Weak</span>
                                        <span>Medium</span>
                                        <span>Strong</span>
                                    </div>
                                    <div id="passwordStrengthVisual" class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div id="passwordStrengthBar" class="h-full bg-gray-400 transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Terms & Conditions -->
                    <div class="mb-8">
                        <div class="flex items-start p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <input type="checkbox" id="terms" name="terms" required 
                                class="rounded border-gray-300 text-primary focus:ring-primary mt-1 flex-shrink-0">
                            <label for="terms" class="ml-3 text-sm text-gray-700">
                                <span class="font-medium">I agree to the Terms of Service and Privacy Policy</span>
                                <span class="text-red-500 ml-1">*</span>
                                <p class="mt-1 text-gray-600">
                                    By creating an account, you agree to our terms and acknowledge our privacy practices. 
                                    You can manage your preferences at any time in your account settings.
                                </p>
                            </label>
                        </div>
                        <div id="termsError" class="mt-2 text-sm text-red-600 hidden">
                            You must agree to the Terms of Service and Privacy Policy to continue.
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="button" onclick="closeRegisterModal()" 
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-all duration-200">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 btn-primary text-white py-3 rounded-lg font-semibold transition-all duration-200 hover:scale-[1.02] flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span id="registerButtonText">Create Account</span>
                            <div id="registerSpinner" class="hidden ml-2">
                                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                            </div>
                        </button>
                    </div>
                </form>
                
                <!-- Login Link -->
                <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <button onclick="switchToLogin()" class="text-primary font-semibold hover:underline ml-1">
                            Sign in to your account
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div id="forgotPasswordModal" class="fixed inset-0 modal-backdrop hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
            <div class="p-8">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-inter font-bold text-gray-800 mb-2">Reset Password</h2>
                    <p class="text-gray-600">Enter your email to receive reset instructions</p>
                </div>
                
                <form id="forgotPasswordForm" onsubmit="return handleForgotPassword(event)">
                    <div class="space-y-6">
                        <div>
                            <label for="forgotEmail" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="forgotEmail" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Enter your registered email">
                        </div>
                        <div id="forgotPasswordMessage" class="hidden"></div>

                        <button type="submit" id="forgotPasswordBtn" class="w-full btn-primary text-white py-3 rounded-lg font-semibold">
                            Send Reset Instructions
                        </button>
                    </div>
                </form>
                
                <div class="mt-6 text-center">
                    <button onclick="closeForgotPassword()" class="text-primary font-semibold hover:underline">Back to login</button>
                </div>
                
                <button onclick="closeForgotPassword()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script src="js/index.js"></script>
</body>
</html>