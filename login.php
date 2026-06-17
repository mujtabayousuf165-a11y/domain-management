<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Domain Registration System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f4f7ff, #eef2ff);
            min-height: 100vh;
            padding: 60px 20px;
            color: #222;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 450px;
            width: 100%;
        }

        .form-box {
            background: #fff;
            padding: 50px;
            border-radius: 24px;
            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.06),
                0 2px 8px rgba(0, 0, 0, 0.03);
            position: relative;
            overflow: hidden;
        }

        .form-box::before {
            content: "";
            position: absolute;
            top: -120px;
            right: -120px;
            width: 280px;
            height: 280px;
            background: linear-gradient(135deg, #00c853, #00e676);
            border-radius: 50%;
            opacity: 0.1;
        }

        .title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #111827;
        }

        .subtitle {
            color: #6b7280;
            margin-bottom: 40px;
            font-size: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 24px;
        }

        .form-group label {
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }

        .form-control {
            width: 100%;
            height: 58px;
            border: 1px solid #dbe1ea;
            border-radius: 14px;
            padding: 0 18px;
            font-size: 15px;
            background: #f9fbff;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: #00c853;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(0, 200, 83, 0.12);
        }

        .submit-btn {
            border: none;
            background: linear-gradient(135deg, #00c853, #00e676);
            color: #fff;
            height: 58px;
            padding: 0 45px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
            box-shadow: 0 10px 20px rgba(0, 200, 83, 0.25);
            width: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 28px rgba(0, 200, 83, 0.35);
        }

        .error-message {
            display: none;
            margin-bottom: 25px;
            padding: 18px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            border-radius: 14px;
            font-weight: 500;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #00c853;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s ease;
        }

        .back-link:hover {
            color: #00a844;
        }

        @media(max-width: 768px) {
            .form-box {
                padding: 30px 22px;
            }

            .title {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="form-box">

            <h1 class="title">Admin Login</h1>
            <p class="subtitle">
                Sign in to access domain data.
            </p>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-message" style="display: block;">
                    ❌ Invalid username or password!
                </div>
            <?php endif; ?>

            <form id="loginForm" method="POST" action="auth.php">

                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" class="form-control" name="username" placeholder="Enter Username" required>
                </div>

                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" class="form-control" name="password" placeholder="Enter Password" required>
                </div>

                <button type="submit" class="submit-btn">
                    Sign In
                </button>

            </form>

            <a href="index.php" class="back-link">← Back to Form</a>

        </div>
    </div>

</body>

</html>
