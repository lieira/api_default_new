<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    //----------------------------------------------------------------------------------------------------------------------
    // VERIFICACAO DE CEP
    public function boot()
    {
        Validator::extend('cep', function ($attribute, $value, $parameters, $validator) {
            $cep = preg_replace('/[^0-9]/', '', (string) $value);
            $url = "http://viacep.com.br/ws/" . $cep . "/json/";
            // CURL
            $ch = curl_init();
            // Disable SSL verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // Will return the response, if false it print the response
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Set the url
            curl_setopt($ch, CURLOPT_URL, $url);
            // Execute
            $result = curl_exec($ch);
            // Closing
            curl_close($ch);

            $json = json_decode($result);
            //var_dump($json);
            if (!isset($json->erro)) {
                $array['uf'] = $json->uf;
                $array['cidade'] = $json->localidade;
                $array['bairro'] = $json->bairro;
                $array['logradouro'] = $json->logradouro;

                return true;
            } else {
                return false;
            }

        });

        Validator::extend('CPForCNPJ', function ($attribute, $value, $parameters, $validator) {
            $CPForCNPJ = preg_replace("/[^0-9]/", "", $value);

            //Caso seja CNPJ
            if (strlen($CPForCNPJ) == 14) {
                return $this->valida_cnpj($CPForCNPJ);
            }
            //Caso seja CPF
            if (strlen($CPForCNPJ) == 11) {
                return $this->valida_cpf($CPForCNPJ);
            }
            return false;
        });

    }
    //---------------------------------------------------------------------------------------------------------------------------



    //----------------------------------------------------------------------------------------------------------------------
    // VALIDACAO DE CPF (CORRIGIDO)
    public function valida_cpf($cpf = null)
    {

        // Verifica se um número foi informado
        if (empty($cpf)) {
            return false;
        }

        // Elimina possivel mascara
        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        // Verifica se o numero de digitos informados é igual a 11
        if (strlen($cpf) != 11) {
            return false;
        }
        // Verifica se nenhuma das sequências invalidas abaixo
        // foi digitada. Caso afirmativo, retorna falso
        else if ($cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999') {
            return false;
            // Calcula os digitos verificadores para verificar se o
            // CPF é válido
        } else {

            for ($t = 9; $t < 11; $t++) {

                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[$c] * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf[$c] != $d) {
                    return false;
                }
            }

            return true;
        }
    }
    //---------------------------------------------------------------------------------------------------------------------------

    
    //----------------------------------------------------------------------------------------------------------------------
    //VALIDACAO DE CNPJ
    public function valida_cnpj($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        $cnpj = (string) $cnpj;

        $cnpj_original = $cnpj;

        $primeiros_numeros_cnpj = substr($cnpj, 0, 12);
        if (!function_exists('multiplica_cnpj')) {

            function multiplica_cnpj($cnpj, $posicao = 5)
            {
                $calculo = 0;

                for ($i = 0; $i < strlen($cnpj); $i++) {
                    $calculo = $calculo + ($cnpj[$i] * $posicao);

                    $posicao--;

                    if ($posicao < 2) {
                        $posicao = 9;
                    }
                }
                return $calculo;
            }

        }

        $primeiro_calculo = multiplica_cnpj($primeiros_numeros_cnpj);

        $primeiro_digito = ($primeiro_calculo % 11) < 2 ? 0 : 11 - ($primeiro_calculo % 11);
        $primeiros_numeros_cnpj .= $primeiro_digito;

        $segundo_calculo = multiplica_cnpj($primeiros_numeros_cnpj, 6);
        $segundo_digito = ($segundo_calculo % 11) < 2 ? 0 : 11 - ($segundo_calculo % 11);
        $cnpj = $primeiros_numeros_cnpj . $segundo_digito;

        if ($cnpj === $cnpj_original) {
            return true;
        }
        return false;
    }
    //----------------------------------------------------------------------------------------------------------------------
}
