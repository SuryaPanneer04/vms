<?php
include("includes/config.php");

$error = "";

if (isset($_POST['login'])) {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($email) && !empty($password)) {
        $stmt = $con->prepare("SELECT * FROM users WHERE email = ? AND status = 'Active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['emp_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['department'] = $user['department'];
            $_SESSION['designation'] = $user['designation'];
            
            if ($_SESSION['role'] === 'admin') {
                header("Location: index.php");
            } elseif ($_SESSION['role'] === 'timeoffice') {
                header("Location: add_visitor_form.php");
            } else {
                header("Location: employee_portal.php");
            }
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Please enter both email and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Visitor Management System</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4cc9f0;
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-gradient);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
            color: white;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
            position: relative;
        }

        .login-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand-logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto 24px;
            box-shadow: 0 10px 20px -5px rgba(67, 97, 238, 0.5);
        }

        .login-header h2 {
            font-weight: 700;
            margin-bottom: 8px;
            text-align: center;
        }

        .login-header p {
            color: #94a3b8;
            text-align: center;
            margin-bottom: 32px;
        }

        .form-label {
            color: #cbd5e1;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .input-group {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #94a3b8;
            padding-left: 16px;
        }

        .form-control {
            background: transparent;
            border: none;
            color: white;
            padding: 12px 16px;
            font-weight: 400;
        }

        .form-control:focus {
            background: transparent;
            box-shadow: none;
            color: white;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-top: 10px;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(67, 97, 238, 0.4);
            color: white;
        }

        .alert-custom {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .bg-blobs {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            filter: blur(100px);
        }

        .blob-1 {
            position: absolute;
            width: 300px;
            height: 300px;
            background: var(--primary);
            top: -150px;
            left: -150px;
            opacity: 0.3;
            border-radius: 50%;
        }

        .blob-2 {
            position: absolute;
            width: 300px;
            height: 300px;
            background: var(--accent);
            bottom: -150px;
            right: -150px;
            opacity: 0.2;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="bg-blobs">
        <div class="blob-1"></div>
        <div class="blob-2"></div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="brand-logo">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Sign in to your VMS account</p>
            </div>

            <?php if ($error): ?>
                <div class="alert-custom">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter email" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" name="login" class="btn btn-login">
                        Sign In <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <small style="color: #64748b">VMS v2.0 &copy; <?= date('Y') ?></small>
            </div>
        </div>
    </div>
</body>
</html>
