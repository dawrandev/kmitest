<?php

use App\Models\Language;

if (!function_exists('getLanguages')) {
    function getLanguages()
    {
        return \App\Models\Language::all();
    }
}
