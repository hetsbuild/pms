<?php

if (!function_exists('setMessage')) {

    function setMessage($message)
    {
        $_SESSION['message'] = $message;
    }
}

if (!function_exists('showMessages')) {

    function showMessages()
    {
        if (isset($_SESSION['message'])) {

            echo '<p style="color:green;">'
                . htmlspecialchars($_SESSION['message'])
                . '</p>';

            unset($_SESSION['message']);
        }
    }
}