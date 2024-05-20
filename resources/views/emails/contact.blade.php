<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        p {
            color: #666;
            line-height: 1.6;
        }

        .contact-details {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .footer {
            margin-top: 20px;
            color: #888;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Contact Us</h1>

        <div class="contact-details">
            <p><strong>Name:</strong> {{ $data['name'] }}</p>
            <p><strong>Email:</strong> {{ $data['email'] }}</p>
            <p><strong>Subject:</strong> {{ $data['subject'] }}</p>
            <p><strong>Message:</strong></p>
            <p>{{ $data['message'] }}</p>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply.</p>
    </div>
</body>

</html>
