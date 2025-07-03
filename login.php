<?php
session_start();
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputusername = $_POST['username'];
    $inputpassword = $_POST['password'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'toyverse_db');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to prevent SQL injection
   $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $inputusername);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Use password_verify to check raw input against hashed DB password
    if (password_verify($inputpassword, $user['password'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['dob'] = $user['dob'];
        $_SESSION['gender'] = $user['gender'];
        $_SESSION['address'] = $user['address'];
        $_SESSION['phone'] = $user['phone'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        header("Location: homepage.php");
        exit;
    } else {
        $error_message = "Incorrect password.";
    }
} else {
    $error_message = "Invalid username.";
}

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToyVerse - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sky-blue: #87CEEB;
            --light-blue: #ADD8E6;
            --deep-blue: #1E90FF;
            --navy: #000080;
            --white: #FFFFFF;
            --off-white: #F8F9FA;
            --gradient-light: linear-gradient(135deg, #FFFFFF 0%, #E6F3FF 50%, #CCE6FF 100%);
            --gradient-card: linear-gradient(145deg, #FFFFFF 0%, #F0F8FF 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--gradient-light);
            color: var(--navy);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

       .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 2px solid var(--light-blue);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 8px 32px rgba(135, 206, 235, 0.2);
        }

        .navbar-brand {
            font-family: 'poppins', sans-serif;
            display: flex;
            align-items: center;
            font-weight: 700;
            font-size: 1.8rem;
            background: var(--deep-blue);
            color: white;
            text-decoration: none;
            letter-spacing: 0.1em;
            text-shadow: 0 2px 15px rgba(135, 206, 235, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .navbar-brand:hover {
            color: white;
        }

        .navbar-logo {
            height: 80px;
            margin-right: 1rem;
            filter: drop-shadow(0 0 10px rgba(30, 144, 255, 0.3));
        }

        .navbar-nav .nav-link {
            color: var(--navy);
            font-weight: 500;
            margin: 0 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            letter-spacing: 0.05em;
            font-size: 0.9rem;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus {
            color: var(--white);
            background: var(--deep-blue);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 144, 255, 0.3);
        }

         .nav-link.active {
            color: var(--white);
            background: var(--deep-blue);
            transform: translateY(-2px);
        }
        

        .login-container {
            flex: 1;
            padding: 4rem 0;
        }

        .login-card {
            background: var(--gradient-card);
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(135, 206, 235, 0.2);
            padding: 3rem;
            max-width: 900px;
            margin: 0 auto;
            border: 1px solid rgba(30, 144, 255, 0.3);
            backdrop-filter: blur(20px);
        }

        .welcome-section h3 {
            color: var(--deep-blue);
            font-weight: 700;
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
        }

        .welcome-section p {
            color: var(--navy);
            font-size: 1.1rem;
            line-height: 1.8;
        }

        .login-section h2 {
            color: var(--navy);
            font-weight: 700;
            margin-bottom: 2rem;
        }

        .form-label {
            color: var(--navy);
            font-weight: 600;
        }

        .form-control {
            border: 2px solid var(--light-blue);
            border-radius: 10px;
            padding: 0.8rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--deep-blue);
            box-shadow: 0 0 0 0.2rem rgba(30, 144, 255, 0.25);
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--sky-blue), var(--deep-blue));
            border: none;
            padding: 0.8rem 2rem;
            font-weight: 600;
            border-radius: 30px;
            transition: all 0.3s ease;
            text-transform: lowercase;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(30, 144, 255, 0.4);
        }

        .alert {
            border-radius: 10px;
            font-weight: 500;
        }

        footer {
            background: linear-gradient(135deg, var(--white) 0%, var(--light-blue) 100%);
            padding: 3rem 2rem;
            margin-top: auto;
            border-top: 2px solid var(--sky-blue);
            text-align: center;
        }

        footer h4 {
            color: var(--deep-blue);
            font-weight: 700;
        }

        footer a {
            color: var(--deep-blue);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: var(--navy);
        }

        @media (max-width: 768px) {
            .login-card {
                margin: 1rem;
                padding: 2rem;
            }
            
            .welcome-section h3 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                ToyVerse
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                     <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about_logged_out.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="login.php" style="color: white;">Login</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="login-container">
        <div class="login-card">
            <form method="POST" action="">
                <div class="row">
                    <div class="col-lg-6 welcome-section">
                        <h3>Welcome to ToyVerse!</h3>
                        <p>Sign in to explore our magical world of toys! Access your wishlist, track orders, and discover new arrivals in our toy wonderland.</p>
                        <p>Where imagination comes to life and every child's dream becomes reality.</p>
                    </div>
                    <div class="col-lg-6 login-section">
                        <h2>Login</h2>
                        
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo htmlspecialchars($success_message); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" style="border-radius: 8px;">login</button>

                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                Don't have an account? 
                                <a href="register.php" style="color: var(--deep-blue);">Sign up here</a>
                            </small>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h4>ToyVerse's Hirana</h4>
                    <p class="mb-3">Collect the cutest designer figurines.</p>
                    <div class="mb-3">
                        <p>© <?php echo date("Y"); ?> ToyVerse Hirana. all rights reserved.</p>
                        <p>contact: <a href="mailto:info@toyversehirana.com">info@toyversehirana.com</a> | <a href="tel:+18005551234">+1 800 555 1234</a></p>
                    </div>
                    <div>
                        <a href="about.php">about us</a> | 
                        <a href="#">privacy policy</a> | 
                        <a href="#">terms of service</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>