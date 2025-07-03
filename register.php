<?php
session_start();
$conn = new mysqli("localhost", "root", "", "toyverse_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validation_errors = [];
    $error_message = "";

    // Sanitize inputs
    $fullname  = trim($_POST['fullname'] ?? '');
    $gender    = trim($_POST['gender'] ?? '');
    $dob       = trim($_POST['dob'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $street    = trim($_POST['street'] ?? '');
    $city      = trim($_POST['city'] ?? '');
    $province  = trim($_POST['province'] ?? '');
    $zip       = trim($_POST['zip'] ?? '');
    $country   = trim($_POST['country'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm'] ?? '';

    $address = "$street, $city, $province, $zip, $country";

    // Validation
    if (empty($fullname) || !preg_match("/^[A-Za-z\s]{2,50}$/", $fullname))
        $validation_errors[] = "Valid full name is required.";

    if (empty($dob)) {
        $validation_errors[] = "Date of birth is required.";
    } else {
        $birthDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        if ($age < 18) $validation_errors[] = "You must be at least 18 years old.";
    }

    if (empty($gender)) $validation_errors[] = "Gender is required.";

    if (empty($phone) || !preg_match("/^09\d{9}$/", $phone))
        $validation_errors[] = "Valid phone number is required (11 digits starting with 09).";

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $validation_errors[] = "Valid email address is required.";

    if (empty($street) || !preg_match("/^[A-Za-z0-9\s.,#-]{5,100}$/", $street))
        $validation_errors[] = "Street address must be 5-100 characters and valid format.";

    if (empty($city) || !preg_match("/^[A-Za-z\s]{2,50}$/", $city))
        $validation_errors[] = "Valid city name is required.";

    if (empty($province) || !preg_match("/^[A-Za-z\s]{2,50}$/", $province))
        $validation_errors[] = "Valid province is required.";

    if (empty($zip) || !preg_match("/^\d{4}$/", $zip))
        $validation_errors[] = "ZIP code must be 4 digits.";

    if (empty($country) || !preg_match("/^[A-Za-z\s]{2,50}$/", $country))
        $validation_errors[] = "Valid country is required.";

    if (empty($username) || !preg_match("/^[A-Za-z0-9_]{5,20}$/", $username))
        $validation_errors[] = "Username must be 5-20 characters with letters, numbers, or underscores.";

    if (empty($password) || !preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#]).{8,}$/", $password))
        $validation_errors[] = "Password must have at least 8 characters, including uppercase, lowercase, number, and special character.";

    if ($password !== $confirm)
        $validation_errors[] = "Passwords do not match.";

    // Check if username or email already exists
    $check = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $validation_errors[] = "Username or email already exists.";
    }

    $check->close();

    if (!empty($validation_errors)) {
        $error_message = "Registration failed. Please correct the following errors:";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (fullname, gender, dob, phone, email, address, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $fullname, $gender, $dob, $phone, $email, $address, $username, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['registered_user'] = [
                'fullname' => $fullname,
                'gender' => $gender,
                'dob' => $dob,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'username' => $username
            ];
            header("Location: register_complete.php");
            exit;
        } else {
            $error_message = "Database error occurred";
            $validation_errors[] = $stmt->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ToyVerse - Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=orbitron:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --red-dark:rgb(182, 48, 0);
            --red-light:rgb(255, 0, 0);
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
        }

        body {
            font-family: 'poppins', sans-serif;
            background: var(--gradient-light);
            color: var(--navy);
            line-height: 1.6;
            overflow-x: hidden;
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

        .container-custom {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 3rem;
            margin: 3rem auto;
            max-width: 1000px;
            border: 1px solid rgba(30, 144, 255, 0.3);
            box-shadow: 0 20px 60px rgba(135, 206, 235, 0.2);
        }

        .form-section {
            background: var(--gradient-card);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(30, 144, 255, 0.2);
            transition: all 0.3s ease;
        }

        .form-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(30, 144, 255, 0.1);
        }

        .section-title {
            color: var(--deep-blue);
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-transform: lowercase;
            letter-spacing: 0.05em;
        }

        .form-control {
            border: 1px solid var(--light-blue);
            border-radius: 15px;
            padding: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: var(--deep-blue);
            box-shadow: 0 0 20px rgba(30, 144, 255, 0.15);
            transform: translateY(-2px);
        }

        .btn-submit {
            background: var(--deep-blue);
            color: var(--white);
            border: none;
            padding: 1rem 2.5rem;
            border-radius: 25px;
            font-weight: 500;
            letter-spacing: 0.1em;
            transition: all 0.3s ease;
            text-transform: lowercase;
            box-shadow: 0 10px 20px rgba(30, 144, 255, 0.2);
        }

         .btn-reset {
            background: var(--red-dark);
            color: var(--white);
            border: none;
            padding: 1rem 2.5rem;
            border-radius: 25px;
            font-weight: 500;
            letter-spacing: 0.1em;
            transition: all 0.3s ease;
            text-transform: lowercase;
            box-shadow: 0 10px 20px rgba(30, 144, 255, 0.2);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(30, 144, 255, 0.3);
            color: white;
            background: var(--navy);
        }

         .btn-reset:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(30, 144, 255, 0.3);
            background: var(--red-light);
        }

        .alert {
            border-radius: 15px;
            margin-bottom: 2rem;
            padding: 1rem 1.5rem;
            border: none;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        footer {
            background: linear-gradient(135deg, var(--white) 0%, var(--light-blue) 100%);
            color: var(--navy);
            text-align: center;
            padding: 3rem 2rem;
            margin-top: 5rem;
            border-top: 2px solid var(--sky-blue);
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
            .container-custom {
                margin: 2rem 1rem;
                padding: 1.5rem;
            }

            .form-section {
                padding: 1.5rem;
            }

            .navbar-brand {
                font-size: 1.4rem;
            }

            .btn-submit {
                width: 100%;
                margin-bottom: 1rem;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeInUp 0.8s ease-out;
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
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link active" href="register.php" style="color: white;">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-custom fade-in">
        <form method="POST" action="" novalidate>
            <h2 class="text-center mb-4">Create Your ToyVerse Account</h2>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error_message); ?>
                    <?php if (!empty($validation_errors)): ?>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($validation_errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="form-section">
                <h4 class="section-title">personal information</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fullname" class="form-label">full name *</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="dob" class="form-label">date of birth *</label>
                        <input type="date" class="form-control" id="dob" name="dob" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">gender *</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="">select gender</option>
                            <option value="Male">male</option>
                            <option value="Female">female</option>
                            <option value="Other">other</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">phone number *</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">email address *</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">shipping address</h4>
                <div class="mb-3">
                    <label for="street" class="form-label">street address *</label>
                    <input type="text" class="form-control" id="street" name="street" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="city" class="form-label">city *</label>
                        <input type="text" class="form-control" id="city" name="city" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="province" class="form-label">province/state *</label>
                        <input type="text" class="form-control" id="province" name="province" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="zip" class="form-label">zip code *</label>
                        <input type="text" class="form-control" id="zip" name="zip" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="country" class="form-label">country *</label>
                        <input type="text" class="form-control" id="country" name="country" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">account information</h4>
                <div class="mb-3">
                    <label for="username" class="form-label">username *</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">password *</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="confirm" class="form-label">confirm password *</label>
                        <input type="password" class="form-control" id="confirm" name="confirm" required>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-submit">register</button>
                <button type="reset" class="btn btn-reset">reset</button>
            </div>
            
            <div class="text-center mt-3">
                <p>already have an account? <a href="login.php">login here</a></p>
            </div>
        </form>
    </div>

    <footer>
        <div>© <?php echo date("Y"); ?> ToyVerse. all rights reserved.</div>
        <div>contact: info@toyverse.com | +1 800 555 1234</div>
        <div>
            <a href="about.php">about us</a> | 
            <a href="#">privacy policy</a> | 
            <a href="#">terms of service</a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>