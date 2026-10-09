<?php
require '../../includes/auth.php';
require '../../includes/security.php';
require_once '../../config/db.php';
require "../../includes/flash_message.php";
require '../../includes/permission.php';
include '../../includes/header.php';

?>


<!DOCTYPE html>
<html>

<head>
    <title>Users</title>
    <link rel="stylesheet" href="/pms/assets/css/style.css">
</head>

<body>

    <?php include '../../includes/sidebar.php'; ?>

    <div class="main-content">

        <h2>Users</h2>

        <?php showMessages(); ?>

        <?php if (isAdmin()) { ?>

            <a href="add.php">Add User</a>

        <?php } ?>

        <br><br>

        <input
            type="text"
            id="search"
            placeholder="Search User Name">

        <select id="sort">
            <option value="newest">Newest First</option>
            <option value="az">Name (A-Z)</option>
            <option value="za">Name (Z-A)</option>
        </select>

        <br><br>
        <div id="message"></div>
        <div id="user-data"></div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function loadUsers(page = 1) {

            let search =
                document.getElementById("search").value;

            let sort =
                document.getElementById("sort").value;

            let xhr = new XMLHttpRequest();

            xhr.open(
                "GET",
                "ajax.php?search=" +
                encodeURIComponent(search) +
                "&sort=" +
                encodeURIComponent(sort) +
                "&page=" +
                page,
                true
            );

            xhr.onload = function() {

                if (xhr.status == 200) {

                    document.getElementById("user-data")
                        .innerHTML = xhr.responseText;

                }

            };

            xhr.send();
        }

        document.addEventListener("DOMContentLoaded", function() {

            loadUsers();

            document.getElementById("search")
                .addEventListener("keyup", function() {

                    loadUsers();

                });

            document.getElementById("sort")
                .addEventListener("change", function() {

                    loadUsers();

                });

            document.addEventListener("click", function(e) {

                if (e.target.classList.contains("pagination-link")) {

                    e.preventDefault();

                    let page =
                        e.target.dataset.page;

                    loadUsers(page);

                }

            });

        });

        function toggleStatus(id) {

            fetch("../../ajax/toggle_status.php", {

                    method: "POST",

                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },

                    body: "id=" + id + "&table=users"

                })

                .then(response => response.text())

                .then(data => {

                    if (data.trim() === "success") {

                        loadUsers();

                    } else {

                        alert(data);

                    }

                })

                .catch(error => {

                    console.error(error);

                });

        }

        function deleteUser(id) {

    Swal.fire({
        title: "Are you sure?",
        text: "This user will be permanently deleted!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, Delete!",
        cancelButtonText: "Cancel"
    }).then((result) => {

        if (result.isConfirmed) {

            fetch('delete.php?id=' + id)

                .then(response => response.text())

                .then(data => {

                    if (data.trim() === "success") {

                        Swal.fire({
                            title: "Deleted!",
                            text: "User deleted successfully.",
                            icon: "success",
                            confirmButtonColor: "#3085d6"
                        }).then(() => {

                            loadUsers();

                        });

                    } else {

                        Swal.fire({
                            title: "Error!",
                            text: data,
                            icon: "error",
                            confirmButtonColor: "#3085d6"
                        });

                    }

                })

                .catch(error => {

                    console.error(error);

                    Swal.fire({
                        title: "Error!",
                        text: "Something went wrong while deleting the user.",
                        icon: "error",
                        confirmButtonColor: "#3085d6"
                    });

                });

        }

    });

}
    </script>

</body>

</html>