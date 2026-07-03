<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SIMASTER</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #25252b;
            padding: 1rem;
            position: relative;
            background-image: url('https://images.unsplash.com/photo-1718220216044-006f43e3a9b1?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        @property --a {
            syntax: '<angle>';
            inherits: false;
            initial-value: 0deg;
        }

        /* Box */
        .box {
            position: relative;
            width: 450px;
            height: 200px;
            background: repeating-conic-gradient(
                from var(--a),
                #ebca56 0%,
                #ebca56 5%,
                transparent 5%,
                transparent 40%,
                #ebca56 50%
            );
            filter: drop-shadow(0 15px 50px #000);
            border-radius: 20px;
            animation: rotating 4s linear infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: 0.5s;
        }

        @keyframes rotating {
            0% { --a: 0deg; }
            100% { --a: 360deg; }
        }

        .box::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background: repeating-conic-gradient(
                from var(--a),
                #ebca56 0%,
                #ebca56 5%,
                transparent 5%,
                transparent 40%,
                #ebca56 50%
            );
            filter: drop-shadow(0 15px 50px #000);
            border-radius: 20px;
            animation: rotating 4s linear infinite;
            animation-delay: -1s;
        }

        .box::after {
            content: "";
            position: absolute;
            inset: 4px;
            background: #2d2d39;
            border-radius: 15px;
            border: 8px solid #25252b;
        }

        .box:hover {
            width: 450px;
            height: 500px;
        }

        .box:hover .login {
            inset: 40px;
        }

        .box:hover .loginBx {
            transform: translateY(0px);
        }

        /* Login area */
        .login {
            position: absolute;
            inset: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.2);
            color: #fff;
            z-index: 1000;
            box-shadow: inset 0 10px 20px rgba(0, 0, 0, 0.5);
            border-bottom: 2px solid rgba(255, 255, 255, 0.5);
            transition: 0.5s;
            overflow: hidden;
        }

        .loginBx {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            width: 100%;
            transform: translateY(126px);
            transition: 0.5s;
            text-align: center;
        }

        /* Logo */
        .logo {
            margin-bottom: 10px;
        }

        /* Title */
        h2 {
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.2em;
            font-size: 1.5rem;
        }

        h2 i {
            color: #ebca56;
            text-shadow: 0 0 5px #ebca56, 0 0 20px #ebca56;
            font-style: normal;
        }

        /* Form */
        .form-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        form {
            width: 70%;
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
        }

        input {
            width: 100%;
            padding: 10px 20px;
            outline: none;
            border: none;
            font-size: 1em;
            color: #fff;
            background: rgba(0, 0, 0, 0.1);
            border: 2px solid #fff;
            border-radius: 100px;
        }

        input::placeholder {
            color: #999;
        }

        input[type="submit"] {
            background: #ebca56;
            border: none;
            font-weight: 500;
            color: #111;
            cursor: pointer;
            transition: 0.5s;
        }

        .password-container {
            width: 100%;
            position: relative;
        }

        .password-container input {
            padding-right: 45px;
        }

        .eye-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #ebca56;
            cursor: pointer;
            font-size: 18px;
        }

        .back-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .box {
                width: 92%;
                max-width: 400px;
                /* Auto-expand tanpa hover — hover tidak works di touchscreen */
                height: auto !important;
                min-height: 460px;
            }

            .login {
                inset: 16px;
                padding: 24px 0;
            }

            .loginBx {
                transform: translateY(0) !important;
                gap: 16px;
            }

            .logo img {
                width: 180px;
            }

            h2 {
                font-size: 1.2rem;
            }

            form {
                width: 88%;
                gap: 14px;
            }

            input {
                padding: 10px 16px;
                font-size: 0.95em;
            }
        }
    </style>
</head>

<body>

    <div class="box">
        <div class="login">
            <div class="loginBx">

                <div class="logo">
                    <img src="{{ asset('storage/logo_white.png') }}" width="250">
                </div>

                <h2>LOGIN <i>SIMASTER</i></h2>

                @if (session('error'))
                    <div style="background:#ff4d4d; color:white; padding:8px 15px; border-radius:8px; margin-bottom:15px; font-size:14px;">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div style="background:#ff4d4d; color:white; padding:8px 15px; border-radius:8px; margin-bottom:15px; font-size:14px;">
                        Email atau Password salah!
                    </div>
                @endif

                <div class="form-wrapper">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                        @error('email') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror

                        <div class="password-container">
                            <input id="password" type="password" name="password" placeholder="Password" required>
                            <button type="button" class="eye-btn" onclick="togglePassword()">
                                <i id="eyeIcon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password') <span style="color:red; font-size:12px;">{{ $message }}</span> @enderror

                        <input type="submit" value="Sign In">

                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

</body>
</html>
