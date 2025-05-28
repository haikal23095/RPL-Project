<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .verification-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .verification-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
        .verification-text {
            text-align: center;
            margin-bottom: 30px;
            color: #6c757d;
        }
        .btn-verify {
            width: 100%;
            padding: 10px;
            background-color: orange;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }
        .resend-code {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            cursor: pointer;
        }
        .resend-code:hover {
            color: #0d6efd;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-title">Verification Code</div>
        <div class="verification-text">We sent it to email@gmail.com</div>
        
        <div class="d-flex justify-content-center mb-4">
            <input type="text" class="form-control verification-input mx-1 text-center" maxlength="1" style="width: 50px; height: 50px; font-size: 20px;">
            <input type="text" class="form-control verification-input mx-1 text-center" maxlength="1" style="width: 50px; height: 50px; font-size: 20px;">
            <input type="text" class="form-control verification-input mx-1 text-center" maxlength="1" style="width: 50px; height: 50px; font-size: 20px;">
            <input type="text" class="form-control verification-input mx-1 text-center" maxlength="1" style="width: 50px; height: 50px; font-size: 20px;">
            <input type="text" class="form-control verification-input mx-1 text-center" maxlength="1" style="width: 50px; height: 50px; font-size: 20px;">
            <input type="text" class="form-control verification-input mx-1 text-center" maxlength="1" style="width: 50px; height: 50px; font-size: 20px;">
        </div>
        
        <button class="btn btn-verify mb-3" id="verifyBtn">SEND CODE</button>
        <div class="resend-code" id="resendCode">Resend code</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus the first input
            document.querySelector('.verification-input').focus();
            
            // Handle input navigation between boxes
            const inputs = document.querySelectorAll('.verification-input');
            inputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (this.value.length === 1) {
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    }
                });
                
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value.length === 0) {
                        if (index > 0) {
                            inputs[index - 1].focus();
                        }
                    }
                });
            });
            
            // Verify button click handler
            document.getElementById('verifyBtn').addEventListener('click', function() {
                let code = '';
                inputs.forEach(input => {
                    code += input.value;
                });
                
                if (code.length === 6) {
                    alert('Verification code submitted: ' + code);
                    // Here you would typically send the code to your backend for verification
                } else {
                    alert('Please enter the complete 6-digit code');
                }
            });
            
            // Resend code handler
            document.getElementById('resendCode').addEventListener('click', function() {
                alert('New verification code has been sent to email@gmail.com');
                // Here you would typically trigger your backend to resend the code
            });
        });
    </script>
</body>
</html>