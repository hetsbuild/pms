<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container">

        <div class="row justify-content-center align-items-center vh-100">

            <div class="col-md-5">

                <div class="card shadow">

                    <div class="card-header text-center">
                        <h3 class="mb-0">Registration Form</h3>
                    </div>

                    <div class="card-body">

                        <form action="register_action.php" method="POST">

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

                            <button
                                type="submit"
                                class="btn btn-success w-100">
                                Register
                            </button>

                        </form>

                    </div>

                    <div class="card-footer text-center">

                        Already have an account?

                        <a href="login.php" class="text-decoration-none">
                            Login
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>