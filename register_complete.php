<?php
session_start();

// This page assumes the registration process has already stored these in the session
if (!isset($_SESSION['registered_user'])) {
    header("Location: register.php");
    exit();
}

$user = $_SESSION['registered_user'];
// Optional: clear it after showing once
unset($_SESSION['registered_user']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful - ToyVerse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=orbitron:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sky-blue: #87CEEB;
            --light-blue: #ADD8E6;
            --deep-blue: #1E90FF;
            --navy: #000080;
            --white: #FFFFFF;
            --gradient-light: linear-gradient(135deg, #FFFFFF 0%, #E6F3FF 50%, #CCE6FF 100%);
            --gradient-card: linear-gradient(145deg, #FFFFFF 0%, #F0F8FF 100%);
        }

        body {
            font-family: 'poppins', sans-serif;
            background: var(--gradient-light);
            color: var(--navy);
            line-height: 1.6;
            padding: 2rem;
        }

        .success-box {
            max-width: 600px;
            margin: auto;
            background: var(--gradient-card);
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid rgba(30, 144, 255, 0.3);
            box-shadow: 0 20px 60px rgba(135, 206, 235, 0.2);
            backdrop-filter: blur(10px);
        }

        h2 {
            color: var(--deep-blue);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, var(--navy), var(--deep-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .info-label {
            font-weight: 600;
            color: var(--deep-blue);
        }

        .list-group-item {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(30, 144, 255, 0.2);
            margin-bottom: 0.5rem;
            border-radius: 10px !important;
            transition: all 0.3s ease;
        }

        .list-group-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(30, 144, 255, 0.1);
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--sky-blue), var(--deep-blue));
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            letter-spacing: 0.05em;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(30, 144, 255, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(30, 144, 255, 0.4);
        }

        @media (max-width: 768px) {
            .success-box {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
<div class="success-box">
    <h2 class="text-center mb-4">Registration Successful!</h2>
    <p class="text-center">Welcome to ToyVerse! Here's your information:</p>
    <ul class="list-group mb-4">
        <li class="list-group-item"><span class="info-label">Full Name:</span> <?= htmlspecialchars($user['fullname']) ?></li>
        <li class="list-group-item"><span class="info-label">Username:</span> <?= htmlspecialchars($user['username']) ?></li>
        <li class="list-group-item"><span class="info-label">Email:</span> <?= htmlspecialchars($user['email']) ?></li>
        <li class="list-group-item"><span class="info-label">Date of Birth:</span> <?= htmlspecialchars($user['dob']) ?></li>
        <li class="list-group-item"><span class="info-label">Gender:</span> <?= htmlspecialchars($user['gender']) ?></li>
        <li class="list-group-item"><span class="info-label">Phone:</span> <?= htmlspecialchars($user['phone']) ?></li>
        <li class="list-group-item"><span class="info-label">Address:</span> <?= htmlspecialchars($user['address']) ?></li>
    </ul>
    <div class="text-center">
        <a href="login.php" class="btn btn-primary">Go to Login</a>
    </div>
</div>
</body>
</html>
