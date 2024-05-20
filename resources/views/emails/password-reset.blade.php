<!DOCTYPE html>
<html>

<head>
    <title>OTP Code Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            margin: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }

        .otp-code {
            text-align: center;
            font-size: 24px;
            margin-bottom: 30px;
            color: #333;
        }

        .instructions {
            text-align: center;
            margin-bottom: 30px;
            color: #555;
        }

        .disclaimer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="otp-code">
            Your One-Time Password (OTP) Code: <strong>{{ $otp }}</strong>
        </div>
        <div class="instructions">
            Please use the provided OTP code to complete your verification process.
        </div>
        <div class="disclaimer">
            This email was sent automatically. Please do not reply to this email.
        </div>
    </div>
</body>

</html>
