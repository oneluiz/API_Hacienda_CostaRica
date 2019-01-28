<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Hacienda extends REST_Controller
{
    
    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        
        $this->load->helper('api_helper');
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        //$this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        //$this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        //$this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }
    
    public function index_get()
    {
        $this->set_response(array(
            "ApiRest" => "ApiRest Hacienda Costa Rica"
        ), REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    /**
     * Consulta Estado Factura Hacienda
     */
    public function consulta_post()
    {
        $client_id = $this->post("client_id");
        $token     = $this->post('token');
        $clave     = $this->post("clave");
        
        if ($clave == "" || strlen($clave) == 0) {
            $this->response(array(
                "error" => "La clave no puede estar en blanco"
            ), REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
        
        if ($token == "" || strlen($token) == 0) {
            $this->response(array(
                "error" => "El token no puede estar en blanco"
            ), REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
        
        $url = cliente_consulta($client_id);
        
        if ($url == null) {
            $this->response(array(
                "error" => "Ha ocurrido un error en el client_id."
            ), REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url . $clave,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $token,
                "Cache-Control: no-cache",
                "Content-Type: application/x-www-form-urlencoded"
            )
        ));
        
        $response = curl_exec($curl);
        $status   = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error    = curl_error($curl);
        curl_close($curl);
        
        if ($error) {
            $respuesta = array(
                "Status" => $status,
                "to" => $apiTo,
                "text" => $error
            );
            
            $this->set_response($respuesta, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->set_response(json_decode($response), REST_Controller::HTTP_OK);
        }
    }
    
    /**
     * Genera XML Factura Electronica
     */
    public function fe_post()
    {
        // Datos contribuyente
        $clave        = $this->post("clave");
        $consecutivo  = $this->post("consecutivo");
        $fechaEmision = $this->post("fecha_emision");
        
        // Datos emisor
        $emisorNombre      = $this->post("emisor_nombre");
        $emisorTipoIdentif = $this->post("emisor_tipo_indetif");
        $emisorNumIdentif  = $this->post("emisor_num_identif");
        $nombreComercial   = $this->post("nombre_comercial");
        $emisorProv        = $this->post("emisor_provincia");
        $emisorCanton      = $this->post("emisor_canton");
        $emisorDistrito    = $this->post("emisor_distrito");
        $emisorBarrio      = $this->post("emisor_barrio");
        $emisorOtrasSenas  = $this->post("emisor_otras_senas");
        $emisorCodPaisTel  = $this->post("emisor_cod_pais_tel");
        $emisorTel         = $this->post("emisor_tel");
        $emisorCodPaisFax  = $this->post("emisor_cod_pais_fax");
        $emisorFax         = $this->post("emisor_fax");
        $emisorEmail       = $this->post("emisor_email");
        
        // Datos receptor
        $omitir_receptor     = $this->post("omitir_receptor");
        $receptorNombre      = $this->post("receptor_nombre");
        $receptorTipoIdentif = $this->post("receptor_tipo_identif");
        $receptorNumIdentif  = $this->post("receptor_num_identif");
        $receptorProvincia   = $this->post("receptor_provincia");
        $receptorCanton      = $this->post("receptor_canton");
        $receptorDistrito    = $this->post("receptor_distrito");
        $receptorBarrio      = $this->post("receptor_barrio");
        $receptorOtrasSenas  = $this->post("receptor_otras_senas");
        $receptorCodPaisTel  = $this->post("receptor_cod_pais_tel");
        $receptorTel         = $this->post("receptor_tel");
        $receptorCodPaisFax  = $this->post("receptor_cod_pais_fax");
        $receptorFax         = $this->post("receptor_fax");
        $receptorEmail       = $this->post("receptor_email");
        
        // Detalles de tiquete / Factura
        $condVenta         = $this->post("condicion_venta");
        $plazoCredito      = $this->post("plazo_credito");
        $medioPago         = $this->post("medio_pago");
        $codMoneda         = $this->post("cod_moneda");
        $tipoCambio        = $this->post("tipo_cambio");
        $totalServGravados = $this->post("total_serv_gravados");
        $totalServExentos  = $this->post("total_serv_exentos");
        $totalMercGravadas = $this->post("total_merc_gravada");
        $totalMercExentas  = $this->post("total_merc_exenta");
        $totalGravados     = $this->post("total_gravados");
        $totalExentos      = $this->post("total_exentos");
        $totalVentas       = $this->post("total_ventas");
        $totalDescuentos   = $this->post("total_descuentos");
        $totalVentasNeta   = $this->post("total_ventas_neta");
        $totalImp          = $this->post("total_impuestos");
        $totalComprobante  = $this->post("total_comprobante");
        $otros             = $this->post("otros");
        $otrosType         = $this->post("otrosType");
        
        
        $detalles = json_decode($this->post("detalles"));
        
        // Carga la librería XML
        $this->load->library('xml');
        
        $this->xml->setRootName('FacturaElectronica');
        $this->xml->setAttributes(array(
            'xmlns' => 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/facturaElectronica',
            'xmlns:ds' => 'http://www.w3.org/2000/09/xmldsig#',
            'xmlns:vc' => 'http://www.w3.org/2007/XMLSchema-versioning',
            'xmlns:xs' => 'http://www.w3.org/2001/XMLSchema'
        ));
        
        $this->xml->initiate();
        
        $this->xml->addNode('Clave', $clave);
        $this->xml->addNode('NumeroConsecutivo', $consecutivo);
        $this->xml->addNode('FechaEmision', $fechaEmision);
        /* EMISOR INICIO */
        $this->xml->startBranch('Emisor');
        $this->xml->addNode('Nombre', $emisorNombre);
        /* IDENTIFICACION INICIO */
        $this->xml->startBranch('Identificacion');
        $this->xml->addNode('Tipo', $emisorTipoIdentif);
        $this->xml->addNode('Numero', $emisorNumIdentif);
        $this->xml->endBranch();
        /* IDENTIFICACION FIN */
        
        $this->xml->addNode('NombreComercial', $nombreComercial);
        
        if ($emisorProv != '' && $emisorCanton != '' && $emisorDistrito != '' && $emisorOtrasSenas != '') {
            /* UBICACION INICIO */
            $this->xml->startBranch('Ubicacion');
            $this->xml->addNode('Provincia', $emisorProv);
            $this->xml->addNode('Canton', $emisorCanton);
            $this->xml->addNode('Distrito', $emisorDistrito);
            if ($emisorBarrio != '') {
                $this->xml->addNode('Barrio', $emisorBarrio);
            }
            $this->xml->addNode('OtrasSenas', $emisorOtrasSenas);
            $this->xml->endBranch();
            /* UBICACION FIN */
        }
        
        if ($emisorCodPaisTel != '' && $emisorTel != '') {
            /* TELEFONO INICIO */
            $this->xml->startBranch('Telefono');
            $this->xml->addNode('CodigoPais', $emisorCodPaisTel);
            $this->xml->addNode('NumTelefono', $emisorTel);
            $this->xml->endBranch();
            /* TELEFONO FIN */
        }
        
        if ($emisorCodPaisFax != '' && $emisorFax != '') {
            /* FAX INICIO */
            $this->xml->startBranch('Fax');
            $this->xml->addNode('CodigoPais', $emisorCodPaisFax);
            $this->xml->addNode('NumTelefono', $emisorFax);
            $this->xml->endBranch();
            /* FAX FIN */
        }
        
        $this->xml->addNode('CorreoElectronico', $emisorEmail);
        $this->xml->endBranch();
        /* EMISOR FIN */
        
        if ($omitir_receptor != 'true') {
            /* RECEPTOR INICIO */
            $this->xml->startBranch('Receptor');
            $this->xml->addNode('Nombre', $receptorNombre);
            
            if ($receptorTipoIdentif == '05') {
                if ($receptorTipoIdentif != '' && $receptorNumIdentif != '') {
                    $this->xml->addNode('IdentificacionExtranjero', $receptorNumIdentif);
                }
            } else {
                if ($receptorTipoIdentif != '' && $receptorNumIdentif != '') {
                    /* IDENTIFICACION INICIO */
                    $this->xml->startBranch('Identificacion');
                    $this->xml->addNode('Tipo', $receptorTipoIdentif);
                    $this->xml->addNode('Numero', $receptorNumIdentif);
                    $this->xml->endBranch();
                    /* IDENTIFICACION FIN */
                }
                
                if ($receptorProvincia != '' && $receptorCanton != '' && $receptorDistrito != '' && $receptorOtrasSenas != '') {
                    /* UBICACION INICIO */
                    $this->xml->startBranch('Ubicacion');
                    $this->xml->addNode('Provincia', $receptorProvincia);
                    $this->xml->addNode('Canton', $receptorCanton);
                    $this->xml->addNode('Distrito', $receptorDistrito);
                    if ($receptorBarrio != '') {
                        $this->xml->addNode('Barrio', $receptorBarrio);
                    }
                    $this->xml->addNode('OtrasSenas', $receptorOtrasSenas);
                    $this->xml->endBranch();
                    /* UBICACION FIN */
                }
            }
            
            if ($receptorCodPaisTel != '' && $receptorTel != '') {
                /* TELEFONO INICIO */
                $this->xml->startBranch('Telefono');
                $this->xml->addNode('CodigoPais', $receptorCodPaisTel);
                $this->xml->addNode('NumTelefono', $receptorTel);
                $this->xml->endBranch();
                /* TELEFONO FIN */
            }
            
            if ($receptorCodPaisFax != '' && $receptorFax != '') {
                /* FAX INICIO */
                $this->xml->startBranch('Fax');
                $this->xml->addNode('CodigoPais', $receptorCodPaisFax);
                $this->xml->addNode('NumTelefono', $receptorFax);
                $this->xml->endBranch();
                /* FAX FIN */
            }
            if ($receptorEmail != '') {
                $this->xml->addNode('CorreoElectronico', $receptorEmail);
            }
            $this->xml->endBranch();
            /* RECEPTOR FIN */
        }
        
        $this->xml->addNode('CondicionVenta', $condVenta);
        $this->xml->addNode('PlazoCredito', $plazoCredito);
        $this->xml->addNode('MedioPago', $medioPago);
        $this->xml->startBranch('DetalleServicio');
        $l = 1;
        foreach ($detalles as $d) {
            /* LINEA DETALLE INICIO */
            $this->xml->startBranch('LineaDetalle');
            $this->xml->addNode('NumeroLinea', $l);
            $this->xml->addNode('Cantidad', $d->cantidad);
            $this->xml->addNode('UnidadMedida', $d->unidadMedida);
            $this->xml->addNode('Detalle', $d->detalle);
            $this->xml->addNode('PrecioUnitario', $d->precioUnitario);
            $this->xml->addNode('MontoTotal', $d->montoTotal);
            
            if (isset($d->montoDescuento) && $d->montoDescuento != "") {
                $this->xml->addNode('MontoDescuento', $d->montoDescuento);
            }
            
            if (isset($d->naturalezaDescuento) && $d->naturalezaDescuento != "") {
                $this->xml->addNode('NaturalezaDescuento', $d->naturalezaDescuento);
            }
            
            $this->xml->addNode('SubTotal', $d->subtotal);
            
            if (isset($d->impuesto) && $d->impuesto != "") {
                foreach ($d->impuesto as $i) {
                    /* IMPUESTOS INICIO */
                    $this->xml->startBranch('Impuesto');
                    $this->xml->addNode('Codigo', $i->codigo);
                    $this->xml->addNode('Tarifa', $i->tarifa);
                    $this->xml->addNode('Monto', $i->monto);
                    
                    if (isset($i->exoneracion) && $i->exoneracion != "") {
                        /* EXONERACIONES INICIO */
                        $this->xml->startBranch('Exoneracion');
                        $this->xml->addNode('TipoDocumento', $i->exoneracion->tipoDocumento);
                        $this->xml->addNode('NumeroDocumento', $i->exoneracion->numeroDocumento);
                        $this->xml->addNode('NombreInstitucion', $i->exoneracion->nombreInstitucion);
                        $this->xml->addNode('FechaEmision', $i->exoneracion->fechaEmision);
                        $this->xml->addNode('MontoImpuesto', $i->exoneracion->montoImpuesto);
                        $this->xml->addNode('PorcentajeCompra', $i->exoneracion->porcentajeCompra);
                        $this->xml->endBranch();
                        /* EXONERACIONES FIN */
                    }
                    $this->xml->endBranch();
                    /* IMPUESTOS FIN */
                }
            }
            $this->xml->addNode('MontoTotalLinea', $d->montoTotalLinea);
            $this->xml->endBranch();
            $l++;
            /* LINEA DETALLE FIN */
        }
        $this->xml->endBranch();
        /* DETALLE SERVICIO FIN */
        /* RESUMEN FACTURA INICIO */
        $this->xml->startBranch('ResumenFactura');
        $this->xml->addNode('CodigoMoneda', $codMoneda);
        $this->xml->addNode('TipoCambio', $tipoCambio);
        $this->xml->addNode('TotalServGravados', $totalServGravados);
        $this->xml->addNode('TotalServExentos', $totalServExentos);
        $this->xml->addNode('TotalMercanciasGravadas', $totalMercGravadas);
        $this->xml->addNode('TotalMercanciasExentas', $totalMercExentas);
        $this->xml->addNode('TotalGravado', $totalGravados);
        $this->xml->addNode('TotalExento', $totalExentos);
        $this->xml->addNode('TotalVenta', $totalVentas);
        $this->xml->addNode('TotalDescuentos', $totalDescuentos);
        $this->xml->addNode('TotalVentaNeta', $totalVentasNeta);
        $this->xml->addNode('TotalImpuesto', $totalImp);
        $this->xml->addNode('TotalComprobante', $totalComprobante);
        $this->xml->endBranch();
        /* RESUMEN FACTURA FIN */
        /* NORMATIVA INICIO */
        $this->xml->startBranch('Normativa');
        $this->xml->addNode('NumeroResolucion', 'DGT-R-48-2016');
        $this->xml->addNode('FechaResolucion', '20-02-2017 08:05:00');
        $this->xml->endBranch();
        /* NORMATIVA FIN */
        
        if ($otros != '' && $otrosType != '') {
            $tipos = array(
                "Otros",
                "OtroTexto",
                "OtroContenido"
            );
            
            if (in_array($otrosType, $tipos)) {
                /* OTROS INICIO */
                $this->xml->startBranch('Otros');
                $this->xml->addNode($otrosType, $otros);
                $this->xml->endBranch();
                /* OTROS FIN */
            }
        }
        // Genera XML
        $xml_string = $this->xml->getXml(false);
        
        $respuesta = array(
            "clave" => $clave,
            "xml" => base64_encode($xmlString)
        );
        
        $this->set_response(array(
            "resp" => $respuesta
        ), REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    /**
     * Genera XML Nota Crédito
     */
    public function nc_post()
    {
        // Datos contribuyente
        $clave        = $this->post("clave");
        $consecutivo  = $this->post("consecutivo");
        $fechaEmision = $this->post("fecha_emision");
        
        // Datos emisor
        $emisorNombre      = $this->post("emisor_nombre");
        $emisorTipoIdentif = $this->post("emisor_tipo_indetif");
        $emisorNumIdentif  = $this->post("emisor_num_identif");
        $nombreComercial   = $this->post("nombre_comercial");
        $emisorProv        = $this->post("emisor_provincia");
        $emisorCanton      = $this->post("emisor_canton");
        $emisorDistrito    = $this->post("emisor_distrito");
        $emisorBarrio      = $this->post("emisor_barrio");
        $emisorOtrasSenas  = $this->post("emisor_otras_senas");
        $emisorCodPaisTel  = $this->post("emisor_cod_pais_tel");
        $emisorTel         = $this->post("emisor_tel");
        $emisorCodPaisFax  = $this->post("emisor_cod_pais_fax");
        $emisorFax         = $this->post("emisor_fax");
        $emisorEmail       = $this->post("emisor_email");
        
        // Datos receptor
        $omitir_receptor     = $this->post("omitir_receptor");
        $receptorNombre      = $this->post("receptor_nombre");
        $receptorTipoIdentif = $this->post("receptor_tipo_identif");
        $receptorNumIdentif  = $this->post("receptor_num_identif");
        $receptorProvincia   = $this->post("receptor_provincia");
        $receptorCanton      = $this->post("receptor_canton");
        $receptorDistrito    = $this->post("receptor_distrito");
        $receptorBarrio      = $this->post("receptor_barrio");
        $receptorOtrasSenas  = $this->post("receptor_otras_senas");
        $receptorCodPaisTel  = $this->post("receptor_cod_pais_tel");
        $receptorTel         = $this->post("receptor_tel");
        $receptorCodPaisFax  = $this->post("receptor_cod_pais_fax");
        $receptorFax         = $this->post("receptor_fax");
        $receptorEmail       = $this->post("receptor_email");
        
        // Detalles de tiquete / Factura
        $condVenta            = $this->post("condicion_venta");
        $plazoCredito         = $this->post("plazo_credito");
        $medioPago            = $this->post("medio_pago");
        $codMoneda            = $this->post("cod_moneda");
        $tipoCambio           = $this->post("tipo_cambio");
        $totalServGravados    = $this->post("total_serv_gravados");
        $totalServExentos     = $this->post("total_serv_exentos");
        $totalMercGravadas    = $this->post("total_merc_gravada");
        $totalMercExentas     = $this->post("total_merc_exenta");
        $totalGravados        = $this->post("total_gravados");
        $totalExentos         = $this->post("total_exentos");
        $totalVentas          = $this->post("total_ventas");
        $totalDescuentos      = $this->post("total_descuentos");
        $totalVentasNeta      = $this->post("total_ventas_neta");
        $totalImp             = $this->post("total_impuestos");
        $totalComprobante     = $this->post("total_comprobante");
        $otros                = $this->post("otros");
        $otrosType            = $this->post("otrosType");
        $infoRefeTipoDoc      = $this->post("infoRefeTipoDoc");
        $infoRefeNumero       = $this->post("infoRefeNumero");
        $infoRefeFechaEmision = $this->post("infoRefeFechaEmision");
        $infoRefeCodigo       = $this->post("infoRefeCodigo");
        $infoRefeRazon        = $this->post("infoRefeRazon");
        
        // Detalles de la compra
        $detalles = json_decode($this->post("detalles"));
        
        // Carga la librería XML
        $this->load->library('xml');
        
        $this->xml->setRootName('NotaCreditoElectronica');
        $this->xml->setAttributes(array(
            'xmlns' => 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/notaCreditoElectronica',
            'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance'
        ));
        
        $this->xml->initiate();
        
        $this->xml->addNode('Clave', $clave);
        $this->xml->addNode('NumeroConsecutivo', $consecutivo);
        $this->xml->addNode('FechaEmision', $fechaEmision);
        /* EMISOR INICIO */
        $this->xml->startBranch('Emisor');
        $this->xml->addNode('Nombre', $emisorNombre);
        /* IDENTIFICACION INICIO */
        $this->xml->startBranch('Identificacion');
        $this->xml->addNode('Tipo', $emisorTipoIdentif);
        $this->xml->addNode('Numero', $emisorNumIdentif);
        $this->xml->endBranch();
        /* IDENTIFICACION FIN */
        
        $this->xml->addNode('NombreComercial', $nombreComercial);
        
        if ($emisorProv != '' && $emisorCanton != '' && $emisorDistrito != '' && $emisorOtrasSenas != '') {
            /* UBICACION INICIO */
            $this->xml->startBranch('Ubicacion');
            $this->xml->addNode('Provincia', $emisorProv);
            $this->xml->addNode('Canton', $emisorCanton);
            $this->xml->addNode('Distrito', $emisorDistrito);
            if ($emisorBarrio != '') {
                $this->xml->addNode('Barrio', $emisorBarrio);
            }
            $this->xml->addNode('OtrasSenas', $emisorOtrasSenas);
            $this->xml->endBranch();
            /* UBICACION FIN */
        }
        
        if ($emisorCodPaisTel != '' && $emisorTel != '') {
            /* TELEFONO INICIO */
            $this->xml->startBranch('Telefono');
            $this->xml->addNode('CodigoPais', $emisorCodPaisTel);
            $this->xml->addNode('NumTelefono', $emisorTel);
            $this->xml->endBranch();
            /* TELEFONO FIN */
        }
        
        if ($emisorCodPaisFax != '' && $emisorFax != '') {
            /* FAX INICIO */
            $this->xml->startBranch('Fax');
            $this->xml->addNode('CodigoPais', $emisorCodPaisFax);
            $this->xml->addNode('NumTelefono', $emisorFax);
            $this->xml->endBranch();
            /* FAX FIN */
        }
        
        $this->xml->addNode('CorreoElectronico', $emisorEmail);
        $this->xml->endBranch();
        /* EMISOR FIN */
        
        if ($omitir_receptor != 'true') {
            /* RECEPTOR INICIO */
            $this->xml->startBranch('Receptor');
            $this->xml->addNode('Nombre', $receptorNombre);
            
            if ($receptorTipoIdentif == '05') {
                if ($receptorTipoIdentif != '' && $receptorNumIdentif != '') {
                    $this->xml->addNode('IdentificacionExtranjero', $receptorNumIdentif);
                }
            } else {
                if ($receptorTipoIdentif != '' && $receptorNumIdentif != '') {
                    /* IDENTIFICACION INICIO */
                    $this->xml->startBranch('Identificacion');
                    $this->xml->addNode('Tipo', $receptorTipoIdentif);
                    $this->xml->addNode('Numero', $receptorNumIdentif);
                    $this->xml->endBranch();
                    /* IDENTIFICACION FIN */
                }
                
                if ($receptorProvincia != '' && $receptorCanton != '' && $receptorDistrito != '' && $receptorOtrasSenas != '') {
                    /* UBICACION INICIO */
                    $this->xml->startBranch('Ubicacion');
                    $this->xml->addNode('Provincia', $receptorProvincia);
                    $this->xml->addNode('Canton', $receptorCanton);
                    $this->xml->addNode('Distrito', $receptorDistrito);
                    if ($receptorBarrio != '') {
                        $this->xml->addNode('Barrio', $receptorBarrio);
                    }
                    $this->xml->addNode('OtrasSenas', $receptorOtrasSenas);
                    $this->xml->endBranch();
                    /* UBICACION FIN */
                }
            }
            
            if ($receptorCodPaisTel != '' && $receptorTel != '') {
                /* TELEFONO INICIO */
                $this->xml->startBranch('Telefono');
                $this->xml->addNode('CodigoPais', $receptorCodPaisTel);
                $this->xml->addNode('NumTelefono', $receptorTel);
                $this->xml->endBranch();
                /* TELEFONO FIN */
            }
            
            if ($receptorCodPaisFax != '' && $receptorFax != '') {
                /* FAX INICIO */
                $this->xml->startBranch('Fax');
                $this->xml->addNode('CodigoPais', $receptorCodPaisFax);
                $this->xml->addNode('NumTelefono', $receptorFax);
                $this->xml->endBranch();
                /* FAX FIN */
            }
            if ($receptorEmail != '') {
                $this->xml->addNode('CorreoElectronico', $receptorEmail);
            }
            $this->xml->endBranch();
            /* RECEPTOR FIN */
        }
        
        $this->xml->addNode('CondicionVenta', $condVenta);
        $this->xml->addNode('PlazoCredito', $plazoCredito);
        $this->xml->addNode('MedioPago', $medioPago);
        $this->xml->startBranch('DetalleServicio');
        $l = 1;
        foreach ($detalles as $d) {
            /* LINEA DETALLE INICIO */
            $this->xml->startBranch('LineaDetalle');
            $this->xml->addNode('NumeroLinea', $l);
            $this->xml->addNode('Cantidad', $d->cantidad);
            $this->xml->addNode('UnidadMedida', $d->unidadMedida);
            $this->xml->addNode('Detalle', $d->detalle);
            $this->xml->addNode('PrecioUnitario', $d->precioUnitario);
            $this->xml->addNode('MontoTotal', $d->montoTotal);
            
            if (isset($d->montoDescuento) && $d->montoDescuento != "") {
                $this->xml->addNode('MontoDescuento', $d->montoDescuento);
            }
            
            if (isset($d->naturalezaDescuento) && $d->naturalezaDescuento != "") {
                $this->xml->addNode('NaturalezaDescuento', $d->naturalezaDescuento);
            }
            
            $this->xml->addNode('SubTotal', $d->subtotal);
            
            if (isset($d->impuesto) && $d->impuesto != "") {
                foreach ($d->impuesto as $i) {
                    /* IMPUESTOS INICIO */
                    $this->xml->startBranch('Impuesto');
                    $this->xml->addNode('Codigo', $i->codigo);
                    $this->xml->addNode('Tarifa', $i->tarifa);
                    $this->xml->addNode('Monto', $i->monto);
                    
                    if (isset($i->exoneracion) && $i->exoneracion != "") {
                        /* EXONERACIONES INICIO */
                        $this->xml->startBranch('Exoneracion');
                        $this->xml->addNode('TipoDocumento', $i->exoneracion->tipoDocumento);
                        $this->xml->addNode('NumeroDocumento', $i->exoneracion->numeroDocumento);
                        $this->xml->addNode('NombreInstitucion', $i->exoneracion->nombreInstitucion);
                        $this->xml->addNode('FechaEmision', $i->exoneracion->fechaEmision);
                        $this->xml->addNode('MontoImpuesto', $i->exoneracion->montoImpuesto);
                        $this->xml->addNode('PorcentajeCompra', $i->exoneracion->porcentajeCompra);
                        $this->xml->endBranch();
                        /* EXONERACIONES FIN */
                    }
                    $this->xml->endBranch();
                    /* IMPUESTOS FIN */
                }
            }
            $this->xml->addNode('MontoTotalLinea', $d->montoTotalLinea);
            $this->xml->endBranch();
            $l++;
            /* LINEA DETALLE FIN */
        }
        $this->xml->endBranch();
        /* DETALLE SERVICIO FIN */
        /* RESUMEN FACTURA INICIO */
        $this->xml->startBranch('ResumenFactura');
        $this->xml->addNode('CodigoMoneda', $codMoneda);
        $this->xml->addNode('TipoCambio', $tipoCambio);
        $this->xml->addNode('TotalServGravados', $totalServGravados);
        $this->xml->addNode('TotalServExentos', $totalServExentos);
        $this->xml->addNode('TotalMercanciasGravadas', $totalMercGravadas);
        $this->xml->addNode('TotalMercanciasExentas', $totalMercExentas);
        $this->xml->addNode('TotalGravado', $totalGravados);
        $this->xml->addNode('TotalExento', $totalExentos);
        $this->xml->addNode('TotalVenta', $totalVentas);
        $this->xml->addNode('TotalDescuentos', $totalDescuentos);
        $this->xml->addNode('TotalVentaNeta', $totalVentasNeta);
        $this->xml->addNode('TotalImpuesto', $totalImp);
        $this->xml->addNode('TotalComprobante', $totalComprobante);
        $this->xml->endBranch();
        /* RESUMEN FACTURA FIN */
        /* INFORMACION REFERENCIA INICIO */
        $this->xml->startBranch('InformacionReferencia');
        $this->xml->addNode('TipoDoc', $infoRefeTipoDoc);
        $this->xml->addNode('Numero', $infoRefeNumero);
        $this->xml->addNode('FechaEmision', $infoRefeFechaEmision);
        $this->xml->addNode('Codigo', $infoRefeCodigo);
        $this->xml->addNode('Razon', $infoRefeRazon);
        $this->xml->endBranch();
        /* INFORMACION REFERENCIA FIN */
        /* NORMATIVA INICIO */
        $this->xml->startBranch('Normativa');
        $this->xml->addNode('NumeroResolucion', 'DGT-R-48-2016');
        $this->xml->addNode('FechaResolucion', '20-02-2017 08:05:00');
        $this->xml->endBranch();
        /* NORMATIVA FIN */
        
        if ($otros != '' && $otrosType != '') {
            $tipos = array(
                "Otros",
                "OtroTexto",
                "OtroContenido"
            );
            
            if (in_array($otrosType, $tipos)) {
                /* OTROS INICIO */
                $this->xml->startBranch('Otros');
                $this->xml->addNode($otrosType, $otros);
                $this->xml->endBranch();
                /* OTROS FIN */
            }
        }
        // Genera XML
        $xml_string = $this->xml->getXml(false);
        
        $respuesta = array(
            "clave" => $clave,
            "xml" => base64_encode($xml_string)
        );
        
        $this->set_response($respuesta, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    /**
     * Genera XML Nota Débito
     */
    public function nd_post()
    {
        // Datos contribuyente
        $clave        = $this->post("clave");
        $consecutivo  = $this->post("consecutivo");
        $fechaEmision = $this->post("fecha_emision");
        
        // Datos emisor
        $emisorNombre      = $this->post("emisor_nombre");
        $emisorTipoIdentif = $this->post("emisor_tipo_indetif");
        $emisorNumIdentif  = $this->post("emisor_num_identif");
        $nombreComercial   = $this->post("nombre_comercial");
        $emisorProv        = $this->post("emisor_provincia");
        $emisorCanton      = $this->post("emisor_canton");
        $emisorDistrito    = $this->post("emisor_distrito");
        $emisorBarrio      = $this->post("emisor_barrio");
        $emisorOtrasSenas  = $this->post("emisor_otras_senas");
        $emisorCodPaisTel  = $this->post("emisor_cod_pais_tel");
        $emisorTel         = $this->post("emisor_tel");
        $emisorCodPaisFax  = $this->post("emisor_cod_pais_fax");
        $emisorFax         = $this->post("emisor_fax");
        $emisorEmail       = $this->post("emisor_email");
        
        // Datos receptor
        $omitir_receptor     = $this->post("omitir_receptor");
        $receptorNombre      = $this->post("receptor_nombre");
        $receptorTipoIdentif = $this->post("receptor_tipo_identif");
        $receptorNumIdentif  = $this->post("receptor_num_identif");
        $receptorProvincia   = $this->post("receptor_provincia");
        $receptorCanton      = $this->post("receptor_canton");
        $receptorDistrito    = $this->post("receptor_distrito");
        $receptorBarrio      = $this->post("receptor_barrio");
        $receptorOtrasSenas  = $this->post("receptor_otras_senas");
        $receptorCodPaisTel  = $this->post("receptor_cod_pais_tel");
        $receptorTel         = $this->post("receptor_tel");
        $receptorCodPaisFax  = $this->post("receptor_cod_pais_fax");
        $receptorFax         = $this->post("receptor_fax");
        $receptorEmail       = $this->post("receptor_email");
        
        // Detalles de tiquete / Factura
        $condVenta            = $this->post("condicion_venta");
        $plazoCredito         = $this->post("plazo_credito");
        $medioPago            = $this->post("medio_pago");
        $codMoneda            = $this->post("cod_moneda");
        $tipoCambio           = $this->post("tipo_cambio");
        $totalServGravados    = $this->post("total_serv_gravados");
        $totalServExentos     = $this->post("total_serv_exentos");
        $totalMercGravadas    = $this->post("total_merc_gravada");
        $totalMercExentas     = $this->post("total_merc_exenta");
        $totalGravados        = $this->post("total_gravados");
        $totalExentos         = $this->post("total_exentos");
        $totalVentas          = $this->post("total_ventas");
        $totalDescuentos      = $this->post("total_descuentos");
        $totalVentasNeta      = $this->post("total_ventas_neta");
        $totalImp             = $this->post("total_impuestos");
        $totalComprobante     = $this->post("total_comprobante");
        $otros                = $this->post("otros");
        $otrosType            = $this->post("otrosType");
        $infoRefeTipoDoc      = $this->post("infoRefeTipoDoc");
        $infoRefeNumero       = $this->post("infoRefeNumero");
        $infoRefeFechaEmision = $this->post("infoRefeFechaEmision");
        $infoRefeCodigo       = $this->post("infoRefeCodigo");
        $infoRefeRazon        = $this->post("infoRefeRazon");
        
        // Detalles de la compra
        $detalles = json_decode($this->post("detalles"));
        
        // Carga la librería XML
        $this->load->library('xml');
        
        $this->xml->setRootName('NotaDebitoElectronica');
        $this->xml->setAttributes(array(
            'xmlns' => 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/notaDebitoElectronica',
            'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance'
        ));
        
        $this->xml->initiate();
        
        $this->xml->addNode('Clave', $clave);
        $this->xml->addNode('NumeroConsecutivo', $consecutivo);
        $this->xml->addNode('FechaEmision', $fechaEmision);
        
        /* EMISOR INICIO */
        $this->xml->startBranch('Emisor');
        $this->xml->addNode('Nombre', $emisorNombre);
        /* IDENTIFICACION INICIO */
        $this->xml->startBranch('Identificacion');
        $this->xml->addNode('Tipo', $emisorTipoIdentif);
        $this->xml->addNode('Numero', $emisorNumIdentif);
        $this->xml->endBranch();
        /* IDENTIFICACION FIN */
        
        $this->xml->addNode('NombreComercial', $nombreComercial);
        
        if ($emisorProv != '' && $emisorCanton != '' && $emisorDistrito != '' && $emisorOtrasSenas != '') {
            /* UBICACION INICIO */
            $this->xml->startBranch('Ubicacion');
            $this->xml->addNode('Provincia', $emisorProv);
            $this->xml->addNode('Canton', $emisorCanton);
            $this->xml->addNode('Distrito', $emisorDistrito);
            if ($emisorBarrio != '') {
                $this->xml->addNode('Barrio', $emisorBarrio);
            }
            $this->xml->addNode('OtrasSenas', $emisorOtrasSenas);
            $this->xml->endBranch();
            /* UBICACION FIN */
        }
        
        if ($emisorCodPaisTel != '' && $emisorTel != '') {
            /* TELEFONO INICIO */
            $this->xml->startBranch('Telefono');
            $this->xml->addNode('CodigoPais', $emisorCodPaisTel);
            $this->xml->addNode('NumTelefono', $emisorTel);
            $this->xml->endBranch();
            /* TELEFONO FIN */
        }
        
        if ($emisorCodPaisFax != '' && $emisorFax != '') {
            /* FAX INICIO */
            $this->xml->startBranch('Fax');
            $this->xml->addNode('CodigoPais', $emisorCodPaisFax);
            $this->xml->addNode('NumTelefono', $emisorFax);
            $this->xml->endBranch();
            /* FAX FIN */
        }
        
        $this->xml->addNode('CorreoElectronico', $emisorEmail);
        $this->xml->endBranch();
        /* EMISOR FIN */
        
        if ($omitir_receptor != 'true') {
            /* RECEPTOR INICIO */
            $this->xml->startBranch('Receptor');
            $this->xml->addNode('Nombre', $receptorNombre);
            
            if ($receptorTipoIdentif == '05') {
                if ($receptorTipoIdentif != '' && $receptorNumIdentif != '') {
                    $this->xml->addNode('IdentificacionExtranjero', $receptorNumIdentif);
                }
            } else {
                if ($receptorTipoIdentif != '' && $receptorNumIdentif != '') {
                    /* IDENTIFICACION INICIO */
                    $this->xml->startBranch('Identificacion');
                    $this->xml->addNode('Tipo', $receptorTipoIdentif);
                    $this->xml->addNode('Numero', $receptorNumIdentif);
                    $this->xml->endBranch();
                    /* IDENTIFICACION FIN */
                }
                
                if ($receptorProvincia != '' && $receptorCanton != '' && $receptorDistrito != '' && $receptorOtrasSenas != '') {
                    /* UBICACION INICIO */
                    $this->xml->startBranch('Ubicacion');
                    $this->xml->addNode('Provincia', $receptorProvincia);
                    $this->xml->addNode('Canton', $receptorCanton);
                    $this->xml->addNode('Distrito', $receptorDistrito);
                    if ($receptorBarrio != '') {
                        $this->xml->addNode('Barrio', $receptorBarrio);
                    }
                    $this->xml->addNode('OtrasSenas', $receptorOtrasSenas);
                    $this->xml->endBranch();
                    /* UBICACION FIN */
                }
            }
            
            if ($receptorCodPaisTel != '' && $receptorTel != '') {
                /* TELEFONO INICIO */
                $this->xml->startBranch('Telefono');
                $this->xml->addNode('CodigoPais', $receptorCodPaisTel);
                $this->xml->addNode('NumTelefono', $receptorTel);
                $this->xml->endBranch();
                /* TELEFONO FIN */
            }
            
            if ($receptorCodPaisFax != '' && $receptorFax != '') {
                /* FAX INICIO */
                $this->xml->startBranch('Fax');
                $this->xml->addNode('CodigoPais', $receptorCodPaisFax);
                $this->xml->addNode('NumTelefono', $receptorFax);
                $this->xml->endBranch();
                /* FAX FIN */
            }
            if ($receptorEmail != '') {
                $this->xml->addNode('CorreoElectronico', $receptorEmail);
            }
            $this->xml->endBranch();
            /* RECEPTOR FIN */
        }
        
        $this->xml->addNode('CondicionVenta', $condVenta);
        $this->xml->addNode('PlazoCredito', $plazoCredito);
        $this->xml->addNode('MedioPago', $medioPago);
        $this->xml->startBranch('DetalleServicio');
        $l = 1;
        foreach ($detalles as $d) {
            /* LINEA DETALLE INICIO */
            $this->xml->startBranch('LineaDetalle');
            $this->xml->addNode('NumeroLinea', $l);
            $this->xml->addNode('Cantidad', $d->cantidad);
            $this->xml->addNode('UnidadMedida', $d->unidadMedida);
            $this->xml->addNode('Detalle', $d->detalle);
            $this->xml->addNode('PrecioUnitario', $d->precioUnitario);
            $this->xml->addNode('MontoTotal', $d->montoTotal);
            
            if (isset($d->montoDescuento) && $d->montoDescuento != "") {
                $this->xml->addNode('MontoDescuento', $d->montoDescuento);
            }
            
            if (isset($d->naturalezaDescuento) && $d->naturalezaDescuento != "") {
                $this->xml->addNode('NaturalezaDescuento', $d->naturalezaDescuento);
            }
            
            $this->xml->addNode('SubTotal', $d->subtotal);
            
            if (isset($d->impuesto) && $d->impuesto != "") {
                foreach ($d->impuesto as $i) {
                    /* IMPUESTOS INICIO */
                    $this->xml->startBranch('Impuesto');
                    $this->xml->addNode('Codigo', $i->codigo);
                    $this->xml->addNode('Tarifa', $i->tarifa);
                    $this->xml->addNode('Monto', $i->monto);
                    
                    if (isset($i->exoneracion) && $i->exoneracion != "") {
                        /* EXONERACIONES INICIO */
                        $this->xml->startBranch('Exoneracion');
                        $this->xml->addNode('TipoDocumento', $i->exoneracion->tipoDocumento);
                        $this->xml->addNode('NumeroDocumento', $i->exoneracion->numeroDocumento);
                        $this->xml->addNode('NombreInstitucion', $i->exoneracion->nombreInstitucion);
                        $this->xml->addNode('FechaEmision', $i->exoneracion->fechaEmision);
                        $this->xml->addNode('MontoImpuesto', $i->exoneracion->montoImpuesto);
                        $this->xml->addNode('PorcentajeCompra', $i->exoneracion->porcentajeCompra);
                        $this->xml->endBranch();
                        /* EXONERACIONES FIN */
                    }
                    $this->xml->endBranch();
                    /* IMPUESTOS FIN */
                }
            }
            $this->xml->addNode('MontoTotalLinea', $d->montoTotalLinea);
            $this->xml->endBranch();
            $l++;
            /* LINEA DETALLE FIN */
        }
        $this->xml->endBranch();
        /* DETALLE SERVICIO FIN */
        /* RESUMEN FACTURA INICIO */
        $this->xml->startBranch('ResumenFactura');
        $this->xml->addNode('CodigoMoneda', $codMoneda);
        $this->xml->addNode('TipoCambio', $tipoCambio);
        $this->xml->addNode('TotalServGravados', $totalServGravados);
        $this->xml->addNode('TotalServExentos', $totalServExentos);
        $this->xml->addNode('TotalMercanciasGravadas', $totalMercGravadas);
        $this->xml->addNode('TotalMercanciasExentas', $totalMercExentas);
        $this->xml->addNode('TotalGravado', $totalGravados);
        $this->xml->addNode('TotalExento', $totalExentos);
        $this->xml->addNode('TotalVenta', $totalVentas);
        $this->xml->addNode('TotalDescuentos', $totalDescuentos);
        $this->xml->addNode('TotalVentaNeta', $totalVentasNeta);
        $this->xml->addNode('TotalImpuesto', $totalImp);
        $this->xml->addNode('TotalComprobante', $totalComprobante);
        $this->xml->endBranch();
        /* RESUMEN FACTURA FIN */
        /* INFORMACION REFERENCIA INICIO */
        $this->xml->startBranch('InformacionReferencia');
        $this->xml->addNode('TipoDoc', $infoRefeTipoDoc);
        $this->xml->addNode('Numero', $infoRefeNumero);
        $this->xml->addNode('FechaEmision', $infoRefeFechaEmision);
        $this->xml->addNode('Codigo', $infoRefeCodigo);
        $this->xml->addNode('Razon', $infoRefeRazon);
        $this->xml->endBranch();
        /* INFORMACION REFERENCIA FIN */
        /* NORMATIVA INICIO */
        $this->xml->startBranch('Normativa');
        $this->xml->addNode('NumeroResolucion', 'DGT-R-48-2016');
        $this->xml->addNode('FechaResolucion', '20-02-2017 08:05:00');
        $this->xml->endBranch();
        /* NORMATIVA FIN */
        
        if ($otros != '' && $otrosType != '') {
            $tipos = array(
                "Otros",
                "OtroTexto",
                "OtroContenido"
            );
            
            if (in_array($otrosType, $tipos)) {
                /* OTROS INICIO */
                $this->xml->startBranch('Otros');
                $this->xml->addNode($otrosType, $otros);
                $this->xml->endBranch();
                /* OTROS FIN */
            }
        }
        // Genera XML
        $xml_string = $this->xml->getXml(false);
        
        $respuesta = array(
            "clave" => $clave,
            "xml" => base64_encode($xml_string)
        );
        
        $this->set_response(array(
            "resp" => $respuesta
        ), REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    /**
     * Genera XML Ticket Electrónico
     */
    function te_post()
    {
        // Datos contribuyente
        $clave        = $this->post("clave");
        $consecutivo  = $this->post("consecutivo");
        $fechaEmision = $this->post("fecha_emision");
        
        // Datos emisor
        $emisorNombre      = $this->post("emisor_nombre");
        $emisorTipoIdentif = $this->post("emisor_tipo_indetif");
        $emisorNumIdentif  = $this->post("emisor_num_identif");
        $nombreComercial   = $this->post("nombre_comercial");
        $emisorProv        = $this->post("emisor_provincia");
        $emisorCanton      = $this->post("emisor_canton");
        $emisorDistrito    = $this->post("emisor_distrito");
        $emisorBarrio      = $this->post("emisor_barrio");
        $emisorOtrasSenas  = $this->post("emisor_otras_senas");
        $emisorCodPaisTel  = $this->post("emisor_cod_pais_tel");
        $emisorTel         = $this->post("emisor_tel");
        $emisorCodPaisFax  = $this->post("emisor_cod_pais_fax");
        $emisorFax         = $this->post("emisor_fax");
        $emisorEmail       = $this->post("emisor_email");
        
        // Datos receptor
        $omitir_receptor     = $this->post("omitir_receptor");
        $receptorNombre      = $this->post("receptor_nombre");
        $receptorTipoIdentif = $this->post("receptor_tipo_identif");
        $receptorNumIdentif  = $this->post("receptor_num_identif");
        $receptorProvincia   = $this->post("receptor_provincia");
        $receptorCanton      = $this->post("receptor_canton");
        $receptorDistrito    = $this->post("receptor_distrito");
        $receptorBarrio      = $this->post("receptor_barrio");
        $receptorOtrasSenas  = $this->post("receptor_otras_senas");
        $receptorCodPaisTel  = $this->post("receptor_cod_pais_tel");
        $receptorTel         = $this->post("receptor_tel");
        $receptorCodPaisFax  = $this->post("receptor_cod_pais_fax");
        $receptorFax         = $this->post("receptor_fax");
        $receptorEmail       = $this->post("receptor_email");
        
        // Detalles de tiquete / Factura
        $condVenta         = $this->post("condicion_venta");
        $plazoCredito      = $this->post("plazo_credito");
        $medioPago         = $this->post("medio_pago");
        $codMoneda         = $this->post("cod_moneda");
        $tipoCambio        = $this->post("tipo_cambio");
        $totalServGravados = $this->post("total_serv_gravados");
        $totalServExentos  = $this->post("total_serv_exentos");
        $totalMercGravadas = $this->post("total_merc_gravada");
        $totalMercExentas  = $this->post("total_merc_exenta");
        $totalGravados     = $this->post("total_gravados");
        $totalExentos      = $this->post("total_exentos");
        $totalVentas       = $this->post("total_ventas");
        $totalDescuentos   = $this->post("total_descuentos");
        $totalVentasNeta   = $this->post("total_ventas_neta");
        $totalImp          = $this->post("total_impuestos");
        $totalComprobante  = $this->post("total_comprobante");
        $otros             = $this->post("otros");
        $otrosType         = $this->post("otrosType");
        
        // Detalles de la compra
        $detalles = json_decode($this->post("detalles"));
        
        
        // Carga la librería XML
        $this->load->library('xml');
        
        $this->xml->setRootName('TiqueteElectronico');
        $this->xml->setAttributes(array(
            'xmlns' => 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/tiqueteElectronico',
            'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance'
        ));
        
        $this->xml->initiate();
        
        $this->xml->addNode('Clave', $clave);
        $this->xml->addNode('NumeroConsecutivo', $consecutivo);
        $this->xml->addNode('FechaEmision', $fechaEmision);
        
        /* EMISOR INICIO */
        $this->xml->startBranch('Emisor');
        $this->xml->addNode('Nombre', $emisorNombre);
        /* IDENTIFICACION INICIO */
        $this->xml->startBranch('Identificacion');
        $this->xml->addNode('Tipo', $emisorTipoIdentif);
        $this->xml->addNode('Numero', $emisorNumIdentif);
        $this->xml->endBranch();
        /* IDENTIFICACION FIN */
        
        $this->xml->addNode('NombreComercial', $nombreComercial);
        
        if ($emisorProv != '' && $emisorCanton != '' && $emisorDistrito != '' && $emisorOtrasSenas != '') {
            /* UBICACION INICIO */
            $this->xml->startBranch('Ubicacion');
            $this->xml->addNode('Provincia', $emisorProv);
            $this->xml->addNode('Canton', $emisorCanton);
            $this->xml->addNode('Distrito', $emisorDistrito);
            if ($emisorBarrio != '') {
                $this->xml->addNode('Barrio', $emisorBarrio);
            }
            $this->xml->addNode('OtrasSenas', $emisorOtrasSenas);
            $this->xml->endBranch();
            /* UBICACION FIN */
        }
        
        if ($emisorCodPaisTel != '' && $emisorTel != '') {
            /* TELEFONO INICIO */
            $this->xml->startBranch('Telefono');
            $this->xml->addNode('CodigoPais', $emisorCodPaisTel);
            $this->xml->addNode('NumTelefono', $emisorTel);
            $this->xml->endBranch();
            /* TELEFONO FIN */
        }
        
        if ($emisorCodPaisFax != '' && $emisorFax != '') {
            /* FAX INICIO */
            $this->xml->startBranch('Fax');
            $this->xml->addNode('CodigoPais', $emisorCodPaisFax);
            $this->xml->addNode('NumTelefono', $emisorFax);
            $this->xml->endBranch();
            /* FAX FIN */
        }
        
        $this->xml->addNode('CorreoElectronico', $emisorEmail);
        $this->xml->endBranch();
        /* EMISOR FIN */
        $this->xml->addNode('CondicionVenta', $condVenta);
        $this->xml->addNode('PlazoCredito', $plazoCredito);
        $this->xml->addNode('MedioPago', $medioPago);
        /* DETALLE SERVICIO INICIO */
        $this->xml->startBranch('DetalleServicio');
        $l = 1;
        foreach ($detalles as $d) {
            /* LINEA DETALLE INICIO */
            $this->xml->startBranch('LineaDetalle');
            $this->xml->addNode('NumeroLinea', $l);
            $this->xml->addNode('Cantidad', $d->cantidad);
            $this->xml->addNode('UnidadMedida', $d->unidadMedida);
            $this->xml->addNode('Detalle', $d->detalle);
            $this->xml->addNode('PrecioUnitario', $d->precioUnitario);
            $this->xml->addNode('MontoTotal', $d->montoTotal);
            
            if (isset($d->montoDescuento) && $d->montoDescuento != "") {
                $this->xml->addNode('MontoDescuento', $d->montoDescuento);
            }
            
            if (isset($d->naturalezaDescuento) && $d->naturalezaDescuento != "") {
                $this->xml->addNode('NaturalezaDescuento', $d->naturalezaDescuento);
            }
            
            $this->xml->addNode('SubTotal', $d->subtotal);
            
            if (isset($d->impuesto) && $d->impuesto != "") {
                foreach ($d->impuesto as $i) {
                    /* IMPUESTOS INICIO */
                    $this->xml->startBranch('Impuesto');
                    $this->xml->addNode('Codigo', $i->codigo);
                    $this->xml->addNode('Tarifa', $i->tarifa);
                    $this->xml->addNode('Monto', $i->monto);
                    
                    if (isset($i->exoneracion) && $i->exoneracion != "") {
                        /* EXONERACIONES INICIO */
                        $this->xml->startBranch('Exoneracion');
                        $this->xml->addNode('TipoDocumento', $i->exoneracion->tipoDocumento);
                        $this->xml->addNode('NumeroDocumento', $i->exoneracion->numeroDocumento);
                        $this->xml->addNode('NombreInstitucion', $i->exoneracion->nombreInstitucion);
                        $this->xml->addNode('FechaEmision', $i->exoneracion->fechaEmision);
                        $this->xml->addNode('MontoImpuesto', $i->exoneracion->montoImpuesto);
                        $this->xml->addNode('PorcentajeCompra', $i->exoneracion->porcentajeCompra);
                        $this->xml->endBranch();
                        /* EXONERACIONES FIN */
                    }
                    $this->xml->endBranch();
                    /* IMPUESTOS FIN */
                }
            }
            $this->xml->addNode('MontoTotalLinea', $d->montoTotalLinea);
            $this->xml->endBranch();
            $l++;
            /* LINEA DETALLE FIN */
        }
        $this->xml->endBranch();
        /* DETALLE SERVICIO FIN */
        /* RESUMEN FACTURA INICIO */
        $this->xml->startBranch('ResumenFactura');
        $this->xml->addNode('CodigoMoneda', $codMoneda);
        $this->xml->addNode('TipoCambio', $tipoCambio);
        $this->xml->addNode('TotalServGravados', $totalServGravados);
        $this->xml->addNode('TotalServExentos', $totalServExentos);
        $this->xml->addNode('TotalMercanciasGravadas', $totalMercGravadas);
        $this->xml->addNode('TotalMercanciasExentas', $totalMercExentas);
        $this->xml->addNode('TotalGravado', $totalGravados);
        $this->xml->addNode('TotalExento', $totalExentos);
        $this->xml->addNode('TotalVenta', $totalVentas);
        $this->xml->addNode('TotalDescuentos', $totalDescuentos);
        $this->xml->addNode('TotalVentaNeta', $totalVentasNeta);
        $this->xml->addNode('TotalImpuesto', $totalImp);
        $this->xml->addNode('TotalComprobante', $totalComprobante);
        $this->xml->endBranch();
        /* RESUMEN FACTURA FIN */
        /* NORMATIVA INICIO */
        $this->xml->startBranch('Normativa');
        $this->xml->addNode('NumeroResolucion', 'DGT-R-48-2016');
        $this->xml->addNode('FechaResolucion', '20-02-2017 08:05:00');
        $this->xml->endBranch();
        /* NORMATIVA FIN */
        
        if ($otros != '' && $otrosType != '') {
            $tipos = array(
                "Otros",
                "OtroTexto",
                "OtroContenido"
            );
            
            if (in_array($otrosType, $tipos)) {
                /* OTROS INICIO */
                $this->xml->startBranch('Otros');
                $this->xml->addNode($otrosType, $otros);
                $this->xml->endBranch();
                /* OTROS FIN */
            }
        }
        // Genera XML
        $xml_string = $this->xml->getXml(false);
        
        $respuesta = array(
            "clave" => $clave,
            "xml" => base64_encode($xmlString)
        );
        
        $this->set_response(array(
            "resp" => $respuesta
        ), REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
    
    /**
     * Genera XML Mensaje Receptor
     */
    function mr_post()
    {
        
        $clave              = $this->post("clave"); // d{50,50}
        // Datos vendedor = emisor
        $numeroCedulaEmisor = $this->post("numero_cedula_emisor"); // d{12,12} cedula fisica,juridica,NITE,DIMEX
        $numeroCedulaEmisor = str_pad($numeroCedulaEmisor, 12, "0", STR_PAD_LEFT);
        
        // Datos mensaje receptor
        $fechaEmisionDoc           = $this->post("fecha_emision_doc"); // fecha de emision de la confirmacion
        $mensaje                   = $this->post("mensaje"); // 1 - Aceptado, 2 - Aceptado Parcialmente, 3 - Rechazado
        $detalleMensaje            = $this->post("detalle_mensaje");
        $montoTotalImpuesto        = $this->post("monto_total_impuesto"); // d18,5 opcional /obligatorio si comprobante tenga impuesto
        $totalFactura              = $this->post("total_factura"); // d18,5
        $numeroConsecutivoReceptor = $this->post("numero_consecutivo_receptor"); // d{20,20} numeracion consecutiva de los mensajes de confirmacion
        
        // Datos comprador = receptor
        $numeroCedulaReceptor = $this->post("numero_cedula_receptor"); // d{12,12}cedula fisica, juridica, NITE, DIMEX del comprador
        $numeroCedulaReceptor = str_pad($numeroCedulaReceptor, 12, "0", STR_PAD_LEFT);
        
        // Carga la librería XML
        $this->load->library('xml');
        
        $this->xml->setRootName('MensajeReceptor');
        $this->xml->setAttributes(array(
            'xmlns' => 'https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/mensajeReceptor',
            'xmlns:ds' => 'http://www.w3.org/2000/09/xmldsig#',
            'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance'
        ));
        
        $this->xml->initiate();
        
        $this->xml->addNode('clave', $clave);
        $this->xml->addNode('NumeroCedulaEmisor', $numeroCedulaEmisor);
        $this->xml->addNode('FechaEmisionDoc', $fechaEmisionDoc);
        $this->xml->addNode('Mensaje', $mensaje);
        
        if (!empty($detalleMensaje)) {
            $this->xml->addNode('DetalleMensaje', $detalleMensaje);
        }
        
        if (!empty($montoTotalImpuesto)) {
            $this->xml->addNode('MontoTotalImpuesto', $montoTotalImpuesto);
        }
        
        $this->xml->addNode('TotalFactura', $totalFactura);
        $this->xml->addNode('NumeroCedulaReceptor', $numeroCedulaReceptor);
        $this->xml->addNode('NumeroConsecutivoReceptor', $numeroConsecutivoReceptor);
        
        // Genera XML
        $xml_string = $this->xml->getXml(false);
        
        $respuesta = array(
            "clave" => $clave,
            "xml" => base64_encode($xml_string)
        );
        
        $this->set_response(array(
            "resp" => $respuesta
        ), REST_Controller::HTTP_OK);
    }
    
    /**
     * Genera Token Hacienda
     */
    public function token_post()
    {
        $client_id  = $this->post("client_id");
        $grant_type = $this->post("grant_type");
        
        $url = cliente_token($client_id);
        
        $data = array();
        
        // Get Data from Post
        if ($grant_type == "password") {
            $client_secret = $this->post("client_secret");
            $username      = $this->post("username");
            $password      = $this->post("password");
            
            if ($client_id == '') {
                $this->response(array(
                    "error" => "El parametro Client ID es requerido"
                ), REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
            } else if ($grant_type == '') {
                $this->response(array(
                    "error" => "El parametro Grant Type es requerido"
                ), REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
            } else if ($username == '') {
                $this->response(array(
                    "error" => "El parametro Username es requerido"
                ), REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
            } else if ($password == '') {
                $this->response(array(
                    "error" => "El parametro Password es requerido"
                ), REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
            }
            
            $data = array(
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'grant_type' => $grant_type,
                'username' => $username,
                'password' => $password
            );
            
        } else if ($grant_type == "refresh_token") {
            $client_secret = $this->post("client_secret");
            $refresh_token = $this->post("refresh_token");
            
            if ($client_id == '') {
                $this->response(array(
                    "error" => "El parametro Client ID es requerido"
                ), REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
            } else if ($grant_type == '') {
                $this->response(array(
                    "error" => "El parametro Grant Type es requerido"
                ), REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
            } else if ($refresh_token == '') {
                $this->response(array(
                    "error" => "El parametro Refresh Token es requerido"
                ), REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
            }
            
            $data = array(
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'grant_type' => $grant_type,
                'refresh_token' => $refresh_token
            );
        }
        
        $curl = curl_init($url);
        $data = http_build_query($data);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, 'Content-Type: application/x-www-form-urlencoded');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        
        $respuesta = curl_exec($curl);
        $status    = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error       = json_decode(curl_error($curl));
        curl_close($curl);
        if ($error) {
            $respuesta = array(
                "Status" => $status,
                "text" => $error
            );
            $this->set_response($respuesta, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->set_response(json_decode($respuesta), REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
    }
}
