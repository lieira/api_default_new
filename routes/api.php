<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'Welcome to API - '.env('PROJECT_NAME'),
    ]);
});


//LARAVEL SOCIALITE
Route::group(['middleware' => ['web']], function () {
    Route::get('auth/{provider}','SocialiteController@redirectToProvider');
    Route::get('auth/{provider}/callback','SocialiteController@handleProviderCallback');
});


//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// USUARIO
Route::prefix('user')->group(function(){ 

    Route::post('register', 'UserController@register')->middleware('assign.guard:users'); //METODO DE REGISTRO DE CLIENTE

    Route::post('login', 'UserController@login')->middleware('assign.guard:users'); // METODO DE LOGIN DO CLIENTE

    Route::post('/changelostPassword','UserController@changelostPassword'); // ALTERAR SENHA ESQUECIDA

    Route::post('/forgotPassword','UserController@forgotPassword'); // ESQUECI MINHA SENHA

    Route::post('/confirmEmail','UserController@confirmEmail');// CONFIRMAR EMAIL
    
    Route::post('/sendCodeEmail','UserController@sendCodeEmail'); //ENVIAR CODIGO NO EMAIL

    Route::get('/cep/{id}','UserController@getCep'); //OBTER CEP
        
    Route::get('/verifyCpf/{cpf}','UserController@verifyCpf'); //VALIDAR CPF

    Route::get('/verifyCnpj/{cnpj}','UserController@verifyCnpj'); //VALIDAR CNPJ

    Route::group(['middleware' => ['auth.jwt', 'assign.guard:users']], function () {
        Route::get('/','UserController@show'); //SELECIONAR USUARIO
        Route::get('/firstTime','UserController@firstTime'); //LOGANDO PELA PRIMEIRA VEZ
        Route::post('/logout','UserController@logout'); //DESLOGAR USUARIO
        Route::post('/changePassword','UserController@changePassword'); // ALTERAR SENHA
        Route::post('/updateAddress','UserController@updateAddress'); //ATUALIZAR ENDEREÇO
        Route::post('/updateProfile','UserController@updateProfile'); //ATUALIZAR DADOS PESSOAIS
        Route::post('/updateImageProfile','UserController@updateImageProfile'); //ATUALIZAR FOTO DE PERFIL
        Route::post('/updateBannerImage','UserController@updateBannerImage'); //ATUALIZAR BANNER DE USUARIO
        Route::post('/updateDocumentFront','UserController@updateDocumentFront'); //ATUALIZAR DOCUMENTO - FRENTE
        Route::post('/updateDocumentBack','UserController@updateDocumentBack'); //ATUALIZAR DOCUMENTO - VERSO
        Route::post('/updateDocumentAddress','UserController@updateDocumentAddress'); //ATUALIZAR DOCUMENTO - ENDERECO
        Route::post('/fillRegister','UserController@fillRegister'); //COMPLETAR CADASTRO
    });
});

//--------------------------------------------------------------------------------------------------------------------------------------------------------------
// ADMIN
Route::prefix('admin')->group(function(){

    Route::post('login', 'AdminController@login')->middleware('assign.guard:admins'); // METODO DE LOGIN ADMINISTRADOR

    Route::group(['middleware' => ['auth.jwt', 'assign.guard:admins']], function () {
        //ADMIN
        Route::post('/logout','AdminController@logout'); //LOGOUT ADMIN
        Route::get('/','AdminController@show'); // MOSTRAR DADOS ADMIN
        Route::get('/home','AdminController@home'); // MOSTRAR DADOS GERAIS
        Route::get('/unique/{id}','AdminController@unique'); //MOSTRAR UNICO ADMIN
        Route::get('/admins','AdminController@admins'); //MOSTRAR TODOS ADMINS
        Route::post('/addAdmin','AdminController@addAdmin'); //ADICIONAR ADMIN
        Route::post('/removeAdmin','AdminController@removeAdmin'); //REMOVER ADMIN
        Route::post('/editAdmin','AdminController@editAdmin'); //EDITAR ADMIN 

        //USUARIO
        Route::get('/user/{id}','AdminController@listUser'); //MOSTRAR DADOS DE USUARIO
        Route::get('/users','AdminController@listUsers'); //MOSTRAR TODOS USUARIOS
        Route::post('/blockUser','AdminController@blockUser'); //BLOQUEAR USUARIO
        Route::post('/activeUser','AdminController@activeUser'); //ATIVAR USUARIO
        Route::post('/aproveUser','AdminController@aproveUser'); //APROVAR USUARIO
        Route::post('/refuseUser','AdminController@refuseUser'); //RECUSAR USUARIO
    });

});
