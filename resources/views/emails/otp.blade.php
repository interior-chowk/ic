<!DOCTYPE html>
<html>

<head>
    <title>OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .otp-box {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            letter-spacing: 5px;
        }

        .note {
            font-size: 14px;
            color: #6c757d;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>OTP Verification Code</h2>
        <p>Hello,</p>
        <p>Your OTP verification code is:</p>

        <div class="otp-box">
            <div class="otp-code">{{ $otp }}</div>
        </div>

        <p>This code will expire in 10 minutes.</p>

        <p class="note">If you didn't request this code, please ignore this email.</p>

        <p>Best regards,<br>Your Application Team</p>
    </div>
</body>

</html>
