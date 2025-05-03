<?php
function redirect($path) {
    header("Location: $path");
    exit();
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}