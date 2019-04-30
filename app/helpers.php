<?php

function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}


function storage_url ($path = '',$disk = 'public')
{
    if(!$path) return;
    if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
        return $path;
    }
    return \Storage::disk($disk)->url($path);
}
