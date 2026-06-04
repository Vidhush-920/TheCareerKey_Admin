<?php
$title = 'Login - Admin Panel - The Career Key';
$extra_css = '<link rel="stylesheet" href="./styles/login.css">';
$skip_session = true;
$skip_scripts = true;
include 'layouts/header.php';
?>
        <div class="login-container">
            <div class="login-box">
                <div class="login-head">
                    <img src="./assets/images/vcotlogo.png" alt="The Career Key Logo" class="login-logo">
                    <h3 id="loginTitle"><b>The Career Key</b><br><span>Admin Panel</span></h3>
                </div>
            
                <div class="login-body">
                    <form class="form login-form" onsubmit="event.preventDefault(); getLogin();">
                        <div class="form-field login-field">
                            <label for="username">Username or E-Mail:</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-field login-field">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password">
                        </div>
                        <button class="btn btn-sm btn-primary login-btn mt-2" type="submit">Login</button>
                        <span class="login-message"></span>
                    </form>
                </div>
            </div>
        </div>

        <script>
            
            //get the username/email and password values and send them to the server for authentication (login_auth.php), then display the response message below the form
            function getLogin() {
                const userName = document.getElementById('username').value.trim();
                const password = document.getElementById('password').value;
                const loginMessage = document.querySelector('.login-message');

                if (userName === '' || password === '') {
                    loginMessage.textContent = 'Please enter both username and password.';
                    loginMessage.style.color = 'red';
                    return;
                }
                const formData = new FormData();
                formData.append('username', userName);
                formData.append('password', password);

                fetch('./actions/login_auth.php', {
                    method: 'POST',
                    body: formData
                })                
                .then(response => response.json())
                .then(data => {
                    loginMessage.textContent = data.message;
                    loginMessage.style.color = data.success ? 'green' : 'red';
                    if (data.success) {
                        setTimeout(() => {
                            window.location.href = './index.php';
                        }, 1000);
                    }
                })                
                .catch(error => {
                    console.error('Error:', error);
                    loginMessage.textContent = 'An error occurred. Please try again.';
                    loginMessage.style.color = 'red';
                });
            };
        </script>

<?php
// $skip_scripts = true;
include 'layouts/footer.php';
?>