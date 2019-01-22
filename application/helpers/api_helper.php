<?php
/**
 * @author   Luis Cortés <luizcortesj@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('cliente_consulta'))
{
    function cliente_consulta($cliente)
    {
        if ($cliente === 'api-stag') {
            return "https://api.comprobanteselectronicos.go.cr/recepcion-sandbox/v1/recepcion/";
        } elseif($cliente === 'api-prod') {
            return "https://api.comprobanteselectronicos.go.cr/recepcion/v1/recepcion/";
        }
    }
}

if ( ! function_exists('cliente_token'))
{
    function cliente_token($cliente)
    {
        if ($cliente === 'api-stag') {
            return "https://idp.comprobanteselectronicos.go.cr/auth/realms/rut-stag/protocol/openid-connect/token";
        } elseif($cliente === 'api-prod') {
            return "https://idp.comprobanteselectronicos.go.cr/auth/realms/rut/protocol/openid-connect/token";
        }
    }
}
