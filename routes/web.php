<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::name('files.')->prefix('files')->group(function () {
    Route::post('save_file', 'FileController@store')->name('save_file');
    Route::get('download_file/{id}', 'FileController@download' )->name('download_file');
});

Route::name('folios.')->prefix('folios')->group(function () {
    Route::get('by_month', 'FolioController@by_month')->name('by_month');

    Route::get('search_folios', 'FolioController@search_folios')->name('search_folios');
    Route::post('get_folio', 'FolioController@get_folio')->name('get_folio');
    Route::post('load_folios', 'FolioController@store')->name('load_folios');
    Route::get('show_folio', 'FolioController@show' )->name('show_folio');
    Route::get('by_date', 'FolioController@by_date' )->name('by_date');
    Route::get('by_date_abono', 'FolioController@by_date_abono' )->name('by_date_abono');
    Route::get('by_referencia', 'FolioController@by_referencia' )->name('by_referencia');
    Route::get('by_bank', 'FolioController@by_bank' )->name('by_bank');
    Route::put('marcar_ocupado', 'FolioController@marcar_ocupado')->name('marcar_ocupado');
});

Route::name('student.')->prefix('student')->group(function () {
    Route::get('/show_registers', 'StudentController@show_registers' )->name('show_registers');
    Route::get('books_to_email', 'StudentController@books_to_email' )->name('books_to_email');

    Route::get('/register', 'StudentController@register' )->name('register');
    // Route::post('save_student', 'StudentController@store')->name('save_student');
    Route::post('preregister', 'StudentController@store')->name('preregister');
    Route::get('/consult_data/{date}/{id}', 'StudentController@consult_data' )->name('consult_data');
    Route::get('/download_emails/{school}/{book}', 'StudentController@download_emails' )->name('download_emails');
    Route::get('/download_all/{school}', 'StudentController@download_all' )->name('download_all');
    Route::get('/down_by_book/{book}', 'StudentController@down_by_book' )->name('down_by_book');
    
    Route::post('send_codes', 'StudentController@send_codes')->name('send_codes');

    Route::delete('delete', 'StudentController@delete')->name('delete');
    Route::delete('debug_accepted', 'StudentController@debug_accepted')->name('debug_accepted');
    Route::get('/download_tutorial', 'StudentController@download_tutorial' )->name('download_tutorial');

    Route::get('/show_students', 'StudentController@show_students' )->name('show_students');
    Route::get('/by_school', 'StudentController@by_school' )->name('by_school');

    Route::put('update_status', 'StudentController@update_status')->name('update_status');
    Route::put('update_delivery', 'StudentController@update_delivery')->name('update_delivery');

    Route::put('mark_delivery', 'StudentController@mark_delivery')->name('mark_delivery');
    Route::get('/download_delivery/{status}/{school}/{book}', 'StudentController@download_delivery' )->name('download_delivery');

    Route::put('update_preregister', 'StudentController@update_preregister')->name('update_preregister');
    
    // VISUALIZAR CODIGOS ENVIADOS POR FECHA
    Route::get('/codes_dates', 'StudentController@codes_dates' )->name('codes_dates');
    // VISUALIZAR LIBROS ENTREGADOS POR FECHA
    Route::get('/delivery_dates', 'StudentController@delivery_dates' )->name('delivery_dates');
    // DESCARGAR CODIGOS ENVIADOS O LIBROS ENTREGADOS
    Route::get('/download_dates/{type}/{inicio}/{final}', 'StudentController@download_dates' )->name('download_dates');
    
    // BUSQUEDA POR ESCUELA ENTREGADO Y NO ENTREGADO
    Route::get('/by_school_ne', 'StudentController@by_school_ne' )->name('by_school_ne');
});

Route::name('schools.')->prefix('schools')->group(function () {
    Route::get('index', 'SchoolController@index' )->name('index');
    Route::get('schools_to_email', 'SchoolController@schools_to_email' )->name('schools_to_email');
    Route::get('show_schools', 'SchoolController@show_schools' )->name('show_schools');
    Route::get('show_school', 'SchoolController@show' )->name('show_school');
    Route::get('get_schools', 'SchoolController@get_schools' )->name('get_schools');
    Route::post('new_school', 'SchoolController@store')->name('new_school');
    Route::put('update_school', 'SchoolController@update')->name('update_school');
    Route::get('get_books', 'SchoolController@get_books' )->name('get_books');
    Route::delete('remove_book', 'SchoolController@remove_book')->name('remove_book');
    Route::delete('delete', 'SchoolController@delete')->name('delete');

    // DESCARGAR RELACIÓN DE LIBROS
    Route::get('download_relation', 'SchoolController@download_relation' )->name('download_relation');
    
});

Route::name('registros.')->prefix('registros')->group(function () {
    Route::get('by_date', 'RegistroController@by_date' )->name('by_date');
    Route::get('by_type', 'RegistroController@by_type' )->name('by_type');
    Route::get('by_folio', 'RegistroController@by_folio' )->name('by_folio');
    Route::get('by_total', 'RegistroController@by_total' )->name('by_total');
    Route::get('by_book', 'RegistroController@by_book' )->name('by_book');
    Route::get('by_status', 'RegistroController@by_status' )->name('by_status');
    Route::get('by_student', 'RegistroController@by_student' )->name('by_student');
    Route::get('by_bank', 'RegistroController@by_bank' )->name('by_bank');
    Route::get('download/{temporal1}', 'RegistroController@download' )->name('download');
    Route::get('download_status/{status}', 'RegistroController@download_status' )->name('download_status');
    Route::put('update_status', 'RegistroController@update_status')->name('update_status');
    Route::put('update_rejected', 'RegistroController@update_rejected')->name('update_rejected');
    Route::put('resend_mail', 'RegistroController@resend_mail')->name('resend_mail');

    Route::get('down_banxico/{inicio}/{final}', 'RegistroController@down_banxico' )->name('down_banxico');

    // DESCARGAS BIEN
    Route::get('by_day/{fecha1}/{fecha2}', 'RegistroController@by_day' )->name('by_day');
});

Route::name('administrator.')->prefix('administrator')
->middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/movimientos', 'AdministratorController@movimientos' )->name('movimientos');
    Route::get('/folios', 'AdministratorController@folios' )->name('folios');
    Route::get('/home', 'AdministratorController@home' )->name('home');
});

Route::name('manager.')->prefix('manager')
->middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/folios', 'ManagerController@folios' )->name('folios');
    Route::get('/files', 'ManagerController@files' )->name('files');
    Route::get('/home', 'ManagerController@home' )->name('home');
    Route::get('/movimientos', 'ManagerController@movimientos' )->name('movimientos');
    Route::get('/books', 'ManagerController@books' )->name('books');
    Route::get('/codes', 'ManagerController@codes' )->name('codes');
    Route::get('/schools', 'ManagerController@schools' )->name('schools');
    Route::name('categories.')->prefix('categories')->group(function () {
        Route::get('/revisions', 'ManagerController@revisions' )->name('revisions');
        Route::get('/lista', 'ManagerController@categories' )->name('lista');
        Route::get('/pagos', 'ManagerController@pagos' )->name('pagos');
    });
});

Route::name('reviewer.')->prefix('reviewer')
->middleware(['auth', 'role:reviewer'])->group(function () {
    Route::get('/home', 'ReviewerController@home' )->name('home');
    Route::get('/codes', 'ReviewerController@codes' )->name('codes');
    Route::get('/schools', 'ReviewerController@schools' )->name('schools');
    Route::get('/books', 'ReviewerController@books' )->name('books');
    Route::get('/folios', 'ReviewerController@folios' )->name('folios');
    Route::get('/revisions', 'ReviewerController@revisions' )->name('revisions');
    Route::get('/preregister', 'ReviewerController@preregister' )->name('preregister');
    Route::get('/categories', 'ReviewerController@categories' )->name('categories');
    Route::get('/pagos', 'ReviewerController@pagos' )->name('pagos');
});

Route::name('books.')->prefix('books')->group(function () {
    Route::post('new_book', 'BookController@store')->name('new_book');
    Route::put('update_book', 'BookController@update')->name('update_book');
    Route::post('assign_book', 'BookController@assign_book')->name('assign_book');
    Route::get('get_schools', 'BookController@get_schools' )->name('get_schools');
    Route::get('show_books', 'BookController@show_books' )->name('show_books');
    Route::put('update_price', 'BookController@update_price')->name('update_price');
    Route::get('get_editoriales', 'BookController@get_editoriales' )->name('get_editoriales');
    Route::delete('delete', 'BookController@delete')->name('delete');
});

Route::name('capturist.')->prefix('capturist')
->middleware(['auth', 'role:capturist'])->group(function () {
    Route::get('home', 'CapturistController@home' )->name('home');
});

Route::name('movimientos.')->prefix('movimientos')->group(function () {
    Route::get('by_month', 'MovimientoController@by_month' )->name('by_month');
    Route::get('down_by_month/{month}/{status}', 'MovimientoController@down_by_month' )->name('down_by_month');
});

Route::name('revisions.')->prefix('revisions')->group(function () {
    Route::get('index', 'RevisionController@index' )->name('index');
    Route::get('show', 'RevisionController@show' )->name('show');
    Route::put('save', 'RevisionController@save' )->name('save');

    Route::post('save_categorie', 'RevisionController@save_categorie' )->name('save_categorie');
    Route::put('update_categorie', 'RevisionController@update_categorie' )->name('update_categorie');
    Route::delete('delete_categorie', 'RevisionController@delete_categorie' )->name('delete_categorie');

    Route::get('show_categories', 'RevisionController@show_categories' )->name('show_categories');
    Route::get('by_categorie', 'RevisionController@by_categorie' )->name('by_categorie');

    Route::get('by_student', 'RevisionController@by_student' )->name('by_student');
    Route::get('show_student', 'RevisionController@show_student' )->name('show_student');

    Route::get('/download_categorie/{id}', 'RevisionController@download_categorie' )->name('download_categorie');

    Route::put('archive_categorie', 'RevisionController@archive_categorie' )->name('archive_categorie');

    Route::get('/categories_byschool', 'RevisionController@categories_byschool' )->name('categories_byschool');
    Route::put('/calculate_libros', 'RevisionController@calculate_libros' )->name('calculate_libros');
    Route::post('/save_pago', 'RevisionController@save_pago' )->name('save_pago');

    Route::get('/get_pagos', 'RevisionController@get_pagos' )->name('get_pagos');
    
});

Route::name('information.')->prefix('information')->group(function () {
    Route::put('/send_error', 'StudentController@send_error' )->name('send_error');
});
