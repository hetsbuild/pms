<?php

function sanitizeInput($data)
{
    return trim(strip_tags($data));
}

function escapeOutput($data)
{
    return htmlspecialchars(
        $data,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>