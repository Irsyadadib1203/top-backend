<?php

if (! function_exists('storage_url')) {
    function storage_url($path)
    {
        return rtrim(env('API_STORAGE_URL'), '/') . '/' . ltrim($path, '/');
    }
}
