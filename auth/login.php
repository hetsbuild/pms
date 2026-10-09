<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/pms/assets/img/logo.jpg">
</head>

<body class="bg-light">

    <div class="container">

        <div class="row justify-content-center align-items-center vh-100">

            <div class="col-md-5">

                <div class="card shadow">

                    <div class="card-header text-center">
                        <h3 class="mb-0">Login Form</h3>
                    </div>

                    <div class="card-body">

                        <?php
                        session_start();

                        if (isset($_SESSION['error'])) {
                        ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?= $_SESSION['error']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php
                            unset($_SESSION['error']);
                        }
                        ?>

                        <form action="login_action.php" method="POST">

                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    Username
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    placeholder="Enter Username"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    Password
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Enter Password"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">
                                    Role
                                </label>

                                <select name="role" class="form-select">
                                    <option value="staff">Staff</option>
                                    <option value="admin">Admin</option>
                                    <option value="customer">customer</option>

                                </select>
                            </div>

                            <div class="form-check mb-3">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="remember"
                                    id="remember">

                                <label class="form-check-label" for="remember">
                                    Remember Me
                                </label>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary w-100">
                                Login
                            </button>

                        </form>

                    </div>

                    <div class="card-footer text-center">
                        New User?
                        <a href="register.php" class="text-decoration-none">
                            Register
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>