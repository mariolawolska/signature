<?php

/**
 * Main
 */
Route::get('/', 'AuthController@index');

/**
 * Login & Registration
 */
Route::get('login', 'AuthController@index');
Route::post('post-login', 'AuthController@postLogin');
Route::get('registration', 'AuthController@registration');
Route::post('post-registration', 'AuthController@postRegistration');
Route::get('dashboard', 'AuthController@dashboard');
Route::get('logout', 'AuthController@logout');

/**
 * e-signature
 */

Route::put('saveSignature', 'ReportController@saveSignature');

/**
 * change e-signature
 */
Route::put('redefineSignature', 'ReportController@redefineSignature');

/**
 * To Pdf
 */
Route::post('toPdf', 'PdfController@toPdf')->name('toPdf');
Route::get('toPdf', 'PdfController@toPdf');



