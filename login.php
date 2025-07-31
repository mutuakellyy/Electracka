<!DOCTYPE html>
<html lang="en">

<head>
    <title>login Form</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <!--Stylesheet-->
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <form method="post" action="loginphp.php" id="loginForm">
        <h3>Login Here</h3>

        <label for="email">Your Email</label>
        <input type="email" placeholder="example@gmail.com" title="Please enter you email address" required="" value=""
            name="email" id="email" required="required" class="form-control">

        <label for="password">Password</label>
        <input type="password" title="Please enter your password" required="required" value="" name="password"
            id="password" class="form-control">

        <input type="submit" id="login" name="login" value="login" class="btn btn-success btn-block loginbtn" />
        <p>Have no account?<a class="btn btn-default btn-block" href="signup.php">Register</a></p>

    </form>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!email || !password) {
                alert('Please fill in all fields.');
                return;
            }

            fetch('loginphp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ email, password })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);           // show success
                        setTimeout(() => {             // wait 3 seconds…
                            window.location.href = data.redirect_url;  // …then redirect
                        }, 500);
                    } else {
                        alert(data.message);           // show error
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Server error. Try again.');
                });
        });
    </script>
</body>

</html>