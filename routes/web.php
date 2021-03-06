<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'HomeController@index')->name('home');

Auth::routes();

Route::group(['prefix' => 'alimentos'], function () {
    Route::get('', 'Alimento\AlimentoController@index')->name('alimentos');
    Route::get('/create', 'Alimento\AlimentoController@create')->name('alimentos.create')->middleware('auth');
    Route::post('/store', 'Alimento\AlimentoController@store')->name('alimentos.store')->middleware('auth');
    Route::get('/create_medida_caseira/{id}', 'Alimento\AlimentoController@createMedidaCaseira')
        ->name('alimentos.createMedida')->where('id', '[0-9]+')->middleware('auth');
    Route::post('/store_medida_caseira/{id}', 'Alimento\AlimentoController@storeMedidaCaseira')
        ->name('alimentos.storeMedida')->middleware('auth');
    Route::get('/show/{id}', 'Alimento\AlimentoController@show')->name('alimentos.show')->where('id', '[0-9]+');
    Route::get('/edit/{id}', 'Alimento\AlimentoController@edit')->name('alimentos.edit')->where('id', '[0-9]+')
        ->middleware('auth');
    Route::get('/enable/{id}', 'Alimento\AlimentoController@enable')->name('alimentos.enable')->where('id', '[0-9]+')
        ->middleware('auth');
    Route::get('/disable/{id}', 'Alimento\AlimentoController@disable')->name('alimentos.disable')->where('id', '[0-9]+')
        ->middleware('auth');
    Route::post('/update/{id}', 'Alimento\AlimentoController@update')->name('alimentos.update')->where('id', '[0-9]+')
        ->middleware('auth');
    Route::get('/destroy/{id}', 'Alimento\AlimentoController@destroy')->name('alimentos.destroy')->where('id', '[0-9]+')
        ->middleware('auth');
});

Route::group(['prefix' => 'receitas'], function () {
    Route::get('', 'Receitas\ReceitasController@index')->name('receitas');
    Route::get('/create', 'Receitas\ReceitasController@create')->name('receitas.create')->middleware('auth');
    Route::post('/store', 'Receitas\ReceitasController@store')->name('receitas.store')->middleware('auth');
    Route::get('/show/{id}', 'Receitas\ReceitasController@show')->name('receitas.show')->where('id', '[0-9]+');
    Route::get('/edit/{id}', 'Receitas\ReceitasController@edit')->name('receitas.edit')->where('id', '[0-9]+')->middleware('auth');
    Route::post('/update/{id}', 'Receitas\ReceitasController@update')->name('receitas.update')->where('id', '[0-9]+')->middleware('auth');
    Route::get('/destroy/{id}', 'Receitas\ReceitasController@destroy')->name('receitas.destroy')->where('id', '[0-9]+')->middleware('auth');
    Route::get('/disable/{id}', 'Receitas\ReceitasController@disable')->name('receitas.disable')->where('id', '[0-9]+')->middleware('auth');
    Route::get('/enable/{id}', 'Receitas\ReceitasController@enable')->name('receitas.enable')->where('id', '[0-9]+')->middleware('auth');
});

Route::group(['prefix' => 'refeicao'], function () {
    Route::get('', 'Refeicao\RefeicaoController@index')->name('refeicao');
    Route::get('/create', 'Refeicao\RefeicaoController@create')->name('refeicao.create')->middleware('auth');
    Route::post('/store', 'Refeicao\RefeicaoController@store')->name('refeicao.store')->middleware('auth');
    Route::get('/show/{id}', 'Refeicao\RefeicaoController@show')->name('refeicao.show')->where('id', '[0-9]+');
    Route::get('/edit/{id}', 'Refeicao\RefeicaoController@edit')->name('refeicao.edit')->where('id', '[0-9]+')->middleware('auth');
    Route::post('/update/{id}', 'Refeicao\RefeicaoController@update')->name('refeicao.update')->where('id', '[0-9]+')->middleware('auth');
    Route::get('/destroy/{id}', 'Refeicao\RefeicaoController@destroy')->name('refeicao.destroy')->where('id', '[0-9]+')->middleware('auth');
    Route::get('/disable/{id}', 'Refeicao\RefeicaoController@disable')->name('refeicao.disable')->where('id', '[0-9]+')->middleware('auth');
    Route::get('/enable/{id}', 'Refeicao\RefeicaoController@enable')->name('refeicao.enable')->where('id', '[0-9]+')->middleware('auth');
});

Route::group(['prefix' => 'cardapio'], function () {
    Route::get('', 'Cardapio\CardapioController@index')->name('cardapio');
    Route::get('/all', 'Cardapio\CardapioController@all')->name('cardapio.all');
    Route::get('/relatorio', 'Cardapio\CardapioController@FaixaEtariaMensal')->name('cardapio.FEtaria');
    Route::get('/relatorio_semanal', 'Cardapio\CardapioController@FaixaEtariaSemanal')->name('cardapio.FEtariaSemanal');
    Route::post('/total', 'Cardapio\CardapioController@total')->name('cardapio.total');
    Route::post('/total_semanal', 'Cardapio\CardapioController@totalSemanal')->name('cardapio.totalSemanal');
    Route::get('/create', 'Cardapio\CardapioController@create')->name('cardapio.create')->middleware('auth');
    Route::post('/store', 'Cardapio\CardapioController@store')->name('cardapio.store')->middleware('auth');
    Route::get('/show/{id}', 'Cardapio\CardapioController@show')->name('cardapio.show')->where('id', '[0-9]+');
    Route::get('/edit/{id}', 'Cardapio\CardapioController@edit')->name('cardapio.edit')->where('id', '[0-9]+')->middleware('auth');
    Route::post('/update/{id}', 'Cardapio\CardapioController@update')->name('cardapio.update')->where('id', '[0-9]+')->middleware('auth');
    Route::get('/destroy/{id}', 'Cardapio\CardapioController@destroy')->name('cardapio.destroy')->where('id', '[0-9]+')->middleware('auth');
    Route::get('/disable/{id}', 'Cardapio\CardapioController@disable')->name('cardapio.disable')->where('id', '[0-9]+')->middleware('auth');
    Route::get('/enable/{id}', 'Cardapio\CardapioController@enable')->name('cardapio.enable')->where('id', '[0-9]+')->middleware('auth');
});

