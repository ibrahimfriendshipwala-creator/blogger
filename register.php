<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if user is already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Database connection settings
$host = "localhost";
$username = "uzgzowzbbecqj";
$password = "pmncdpgxsstl";
$database = "dbd4ct6shmkmsu";

// Create connection with error handling
try {
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Connection error: " . $e->getMessage());
}

// Process registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // Check if email already exists
    $check_email = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Email already exists";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $_SESSION['register_success'] = "Registration successful! Please login.";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Blogger</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="container">
                <h1>Welcome to Blogger</h1>
                <nav>
                    <button id="theme-toggle" class="btn btn-theme">
                        <span class="light-icon">🌞</span>
                        <span class="dark-icon">🌙</span>
                    </button>
                    <a href="login.php" class="btn">Login</a>
                </nav>
            </div>
        </header>

        <div class="register-container">
            <div class="register-header">
                <h2>Create Account</h2>
            </div>

            <?php if(!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="register-form">
                <div class="form-group">
                    <input type="text" id="name" name="name" required 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    <label for="name">Full Name</label>
                </div>

                <div class="form-group">
                    <input type="email" id="email" name="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <label for="email">Email Address</label>
                </div>

                <div class="form-group">
                    <input type="password" id="password" name="password" required>
                    <label for="password">Password</label>
                    <div class="password-strength">
                        <div class="password-strength-bar"></div>
                    </div>
                </div>

                <div class="form-group">
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <label for="confirm_password">Confirm Password</label>
                </div>

                <button type="submit" class="register-btn">Create Account</button>
            </form>

            <p class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>

    <script>
        // Password strength indicator
        const password = document.getElementById('password');
        const strengthBar = document.querySelector('.password-strength-bar');

        password.addEventListener('input', function() {
            const strength = calculatePasswordStrength(this.value);
            updateStrengthBar(strength);
        });

        function calculatePasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/)) strength++;
            return strength;
        }

        function updateStrengthBar(strength) {
            strengthBar.className = 'password-strength-bar';
            if (strength === 1) strengthBar.classList.add('strength-weak');
            else if (strength === 2) strengthBar.classList.add('strength-medium');
            else if (strength === 3) strengthBar.classList.add('strength-strong');
        }
    </script>
</body>
</html> 
