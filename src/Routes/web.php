<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/larafm/docs'], function () {
    Route::get('/{slug}', 'LaraFilerController@show')->name('larafm.show');
    Route::post('/', 'LaraFilerController@store')->name('larafm.show');
    Route::delete('/{slug}', 'LaraFilerController@remove')->name('larafm.delete');
});