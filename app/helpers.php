<?php

use App\Models\Language;

if (!function_exists('getLanguages')) {
    function getLanguages()
    {
        return \App\Models\Language::all();
    }
}

if (!function_exists('currentLanguageId')) {
    function currentLanguageId(): ?int
    {
        $locale = app()->getLocale();

        $language = Language::where('code', strtoupper($locale))->first();

        return $language?->id;
    }
}

if (!function_exists('getFaculties')) {
    function getFaculties()
    {
        return \App\Models\Faculty::with('translations')->get();
    }
}
