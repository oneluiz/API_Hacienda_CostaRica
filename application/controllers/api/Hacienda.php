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
class Hacienda extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function fe_post()
    {
        // Datos contribuyente
        $clave                  = $this->post("clave");
        $consecutivo            = $this->post("consecutivo");
        $fechaEmision           = $this->post("fecha_emision");

        // Datos emisor
        $emisorNombre           = $this->post("emisor_nombre");
        $emisorTipoIdentif      = $this->post("emisor_tipo_indetif");
        $emisorNumIdentif       = $this->post("emisor_num_identif");
        $nombreComercial        = $this->post("nombre_comercial");
        $emisorProv             = $this->post("emisor_provincia");
        $emisorCanton           = $this->post("emisor_canton");
        $emisorDistrito         = $this->post("emisor_distrito");
        $emisorBarrio           = $this->post("emisor_barrio");
        $emisorOtrasSenas       = $this->post("emisor_otras_senas");
        $emisorCodPaisTel       = $this->post("emisor_cod_pais_tel");
        $emisorTel              = $this->post("emisor_tel");
        $emisorCodPaisFax       = $this->post("emisor_cod_pais_fax");
        $emisorFax              = $this->post("emisor_fax");
        $emisorEmail            = $this->post("emisor_email");

        // Datos receptor
        $omitir_receptor        = $this->post("omitir_receptor");
        $receptorNombre         = $this->post("receptor_nombre");
        $receptorTipoIdentif    = $this->post("receptor_tipo_identif");
        $receptorNumIdentif     = $this->post("receptor_num_identif");
        $receptorProvincia      = $this->post("receptor_provincia");
        $receptorCanton         = $this->post("receptor_canton");
        $receptorDistrito       = $this->post("receptor_distrito");
        $receptorBarrio         = $this->post("receptor_barrio");
        $receptorOtrasSenas     = $this->post("receptor_otras_senas");
        $receptorCodPaisTel     = $this->post("receptor_cod_pais_tel");
        $receptorTel            = $this->post("receptor_tel");
        $receptorCodPaisFax     = $this->post("receptor_cod_pais_fax");
        $receptorFax            = $this->post("receptor_fax");
        $receptorEmail          = $this->post("receptor_email");

        // Detalles de tiquete / Factura
        $condVenta              = $this->post("condicion_venta");
        $plazoCredito           = $this->post("plazo_credito");
        $medioPago              = $this->post("medio_pago");
        $codMoneda              = $this->post("cod_moneda");
        $tipoCambio             = $this->post("tipo_cambio");
        $totalServGravados      = $this->post("total_serv_gravados");
        $totalServExentos       = $this->post("total_serv_exentos");
        $totalMercGravadas      = $this->post("total_merc_gravada");
        $totalMercExentas       = $this->post("total_merc_exenta");
        $totalGravados          = $this->post("total_gravados");
        $totalExentos           = $this->post("total_exentos");
        $totalVentas            = $this->post("total_ventas");
        $totalDescuentos        = $this->post("total_descuentos");
        $totalVentasNeta        = $this->post("total_ventas_neta");
        $totalImp               = $this->post("total_impuestos");
        $totalComprobante       = $this->post("total_comprobante");
        $otros                  = $this->post("otros");
        $otrosType              = $this->post("otrosType");

        
        $detalles = json_decode($this->post("detalles"));

        $xmlString = '<?xml version="1.0" encoding="utf-8"?>
        <FacturaElectronica xmlns="https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/facturaElectronica" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/facturaElectronica FacturaElectronica_V.4.2.xsd">
            <Clave>' . $clave . '</Clave>
            <NumeroConsecutivo>' . $consecutivo . '</NumeroConsecutivo>
            <FechaEmision>' . $fechaEmision . '</FechaEmision>
            <Emisor>
                <Nombre>' . $emisorNombre . '</Nombre>
                <Identificacion>
                    <Tipo>' . $emisorTipoIdentif . '</Tipo>
                    <Numero>' . $emisorNumIdentif . '</Numero>
                </Identificacion>
                <NombreComercial>' . $nombreComercial . '</NombreComercial>';
    
        if ($emisorProv != '' && $emisorCanton != '' && $emisorDistrito != '' && $emisorOtrasSenas != '')
        {
            $xmlString .= '
            <Ubicacion>
                <Provincia>' . $emisorProv . '</Provincia>
                <Canton>' . $emisorCanton . '</Canton>
                <Distrito>' . $emisorDistrito . '</Distrito>';
            if ($emisorBarrio != '')
                $xmlString .= '<Barrio>' . $emisorBarrio . '</Barrio>';
            $xmlString .= '
                    <OtrasSenas>' . $emisorOtrasSenas . '</OtrasSenas>
                </Ubicacion>';
        }
    
        if ($emisorCodPaisTel != '' && $emisorTel != '')
        {
            $xmlString .= '
                <Telefono>
                    <CodigoPais>' . $emisorCodPaisTel . '</CodigoPais>
                    <NumTelefono>' . $emisorTel . '</NumTelefono>
                </Telefono>';
        }
    
        if ($emisorCodPaisFax != '' && $emisorFax != '')
        {
            $xmlString .= '
                <Fax>
                    <CodigoPais>' . $emisorCodPaisFax . '</CodigoPais>
                    <NumTelefono>' . $emisorFax . '</NumTelefono>
                </Fax>';
        }
    
        $xmlString .= '<CorreoElectronico>' . $emisorEmail . '</CorreoElectronico>
            </Emisor>';
    
        if ($omitir_receptor != 'true')
        {
            $xmlString .= '<Receptor>
                <Nombre>' . $receptorNombre . '</Nombre>';
    
            if ($receptorTipoIdentif == '05')
            {
                if ($receptorTipoIdentif != '' &&  $receptorNumIdentif != '')
                {
                    $xmlString .= '<IdentificacionExtranjero>'
                            . $receptorNumIdentif 
                            . ' </IdentificacionExtranjero>';
                }
            }
            else
            {
                if ($receptorTipoIdentif != '' &&  $receptorNumIdentif != '')
                {
                    $xmlString .= '<Identificacion>
                        <Tipo>' . $receptorTipoIdentif . '</Tipo>
                        <Numero>' . $receptorNumIdentif . '</Numero>
                    </Identificacion>';
                }
    
                if ($receptorProvincia != '' && $receptorCanton != '' && $receptorDistrito != '' && $receptorOtrasSenas != '')
                {
                    $xmlString .= '
                        <Ubicacion>
                            <Provincia>' . $receptorProvincia . '</Provincia>
                            <Canton>' . $receptorCanton . '</Canton>
                            <Distrito>' . $receptorDistrito . '</Distrito>';
                    if ($receptorBarrio != '')
                        $xmlString .= '<Barrio>' . $receptorBarrio . '</Barrio>';
                    $xmlString .= '
                            <OtrasSenas>' . $receptorOtrasSenas . '</OtrasSenas>
                        </Ubicacion>';
                }
            }
    
            if ($receptorCodPaisTel != '' && $receptorTel != '')
            {
                $xmlString .= '<Telefono>
                                  <CodigoPais>' . $receptorCodPaisTel . '</CodigoPais>
                                  <NumTelefono>' . $receptorTel . '</NumTelefono>
                        </Telefono>';
            }
    
            if ($receptorCodPaisFax != '' && $receptorFax != '')
            {
                $xmlString .= '<Fax>
                                  <CodigoPais>' . $receptorCodPaisFax . '</CodigoPais>
                                 <NumTelefono>' . $receptorFax . '</NumTelefono>
                        </Fax>';
            }
    
            if ($receptorEmail != '')
                $xmlString .= '<CorreoElectronico>' . $receptorEmail . '</CorreoElectronico>';
    
            $xmlString .= '</Receptor>';
        }
    
        $xmlString .= '
            <CondicionVenta>' . $condVenta . '</CondicionVenta>
            <PlazoCredito>' . $plazoCredito . '</PlazoCredito>
            <MedioPago>' . $medioPago . '</MedioPago>
            <DetalleServicio>';
    
        $l = 1;
        foreach ($detalles as $d)
        {
            $xmlString .= '<LineaDetalle>
                      <NumeroLinea>' . $l . '</NumeroLinea>
                      <Cantidad>' . $d->cantidad . '</Cantidad>
                      <UnidadMedida>' . $d->unidadMedida . '</UnidadMedida>
                      <Detalle>' . $d->detalle . '</Detalle>
                      <PrecioUnitario>' . $d->precioUnitario . '</PrecioUnitario>
                      <MontoTotal>' . $d->montoTotal . '</MontoTotal>';
    
            if (isset($d->montoDescuento) && $d->montoDescuento != "")
                $xmlString .= '<MontoDescuento>' . $d->montoDescuento . '</MontoDescuento>';
    
            if (isset($d->naturalezaDescuento) && $d->naturalezaDescuento != "")
                $xmlString .= '<NaturalezaDescuento>' . $d->naturalezaDescuento . '</NaturalezaDescuento>';
    
            $xmlString .= '<SubTotal>' . $d->subtotal . '</SubTotal>';
            if (isset($d->impuesto) && $d->impuesto != "")
            {
                foreach ($d->impuesto as $i)
                {
                    $xmlString .= '<Impuesto>
                    <Codigo>' . $i->codigo . '</Codigo>
                    <Tarifa>' . $i->tarifa . '</Tarifa>
                    <Monto>' . $i->monto . '</Monto>';
                    if (isset($i->exoneracion) && $i->exoneracion != "")
                    {
                        $xmlString .= '
                        <Exoneracion>
                            <TipoDocumento>' . $i->exoneracion->tipoDocumento . '</TipoDocumento>
                            <NumeroDocumento>' . $i->exoneracion->numeroDocumento . '</NumeroDocumento>
                            <NombreInstitucion>' . $i->exoneracion->nombreInstitucion . '</NombreInstitucion>
                            <FechaEmision>' . $i->exoneracion->fechaEmision . '</FechaEmision>
                            <MontoImpuesto>' . $i->exoneracion->montoImpuesto . '</MontoImpuesto>
                            <PorcentajeCompra>' . $i->exoneracion->porcentajeCompra . '</PorcentajeCompra>
                        </Exoneracion>';
                    }
    
                    $xmlString .= '</Impuesto>';
                }
            }
    
            $xmlString .= '<MontoTotalLinea>' . $d->montoTotalLinea . '</MontoTotalLinea>';
            $xmlString .= '</LineaDetalle>';
            $l++;
        }
    
        $xmlString .= '</DetalleServicio>
            <ResumenFactura>
            <CodigoMoneda>' . $codMoneda . '</CodigoMoneda>
            <TipoCambio>' . $tipoCambio . '</TipoCambio>
            <TotalServGravados>' . $totalServGravados . '</TotalServGravados>
            <TotalServExentos>' . $totalServExentos . '</TotalServExentos>
            <TotalMercanciasGravadas>' . $totalMercGravadas . '</TotalMercanciasGravadas>
            <TotalMercanciasExentas>' . $totalMercExentas . '</TotalMercanciasExentas>
            <TotalGravado>' . $totalGravados . '</TotalGravado>
            <TotalExento>' . $totalExentos . '</TotalExento>
            <TotalVenta>' . $totalVentas . '</TotalVenta>
            <TotalDescuentos>' . $totalDescuentos . '</TotalDescuentos>
            <TotalVentaNeta>' . $totalVentasNeta . '</TotalVentaNeta>
            <TotalImpuesto>' . $totalImp . '</TotalImpuesto>
            <TotalComprobante>' . $totalComprobante . '</TotalComprobante>
            </ResumenFactura>
            <Normativa>
            <NumeroResolucion>DGT-R-48-2016</NumeroResolucion>
            <FechaResolucion>07-10-2016 08:00:00</FechaResolucion>
            </Normativa>';
    
        if ($otros != '' && $otrosType != '')
        {
            $tipos = array("Otros", "OtroTexto", "OtroContenido");
            if (in_array($otrosType, $tipos))
            {
                $xmlString .= '
                    <Otros>
                <' . $otrosType . '>' . $otros . '</' . $otrosType . '>
                </Otros>';
            }
        }
    
        $xmlString .= '
        </FacturaElectronica>';
        $arrayResp = array(
            "clave" => $clave,
            "xml"   => base64_encode($xmlString)
        );

        $this->set_response($arrayResp, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function nc_post()
    {
        // Datos contribuyente
        $clave                  = $this->post("clave");
        $consecutivo            = $this->post("consecutivo");
        $fechaEmision           = $this->post("fecha_emision");
    
        // Datos emisor
        $emisorNombre           = $this->post("emisor_nombre");
        $emisorTipoIdentif      = $this->post("emisor_tipo_indetif");
        $emisorNumIdentif       = $this->post("emisor_num_identif");
        $nombreComercial        = $this->post("nombre_comercial");
        $emisorProv             = $this->post("emisor_provincia");
        $emisorCanton           = $this->post("emisor_canton");
        $emisorDistrito         = $this->post("emisor_distrito");
        $emisorBarrio           = $this->post("emisor_barrio");
        $emisorOtrasSenas       = $this->post("emisor_otras_senas");
        $emisorCodPaisTel       = $this->post("emisor_cod_pais_tel");
        $emisorTel              = $this->post("emisor_tel");
        $emisorCodPaisFax       = $this->post("emisor_cod_pais_fax");
        $emisorFax              = $this->post("emisor_fax");
        $emisorEmail            = $this->post("emisor_email");
    
        // Datos receptor
        $omitir_receptor        = $this->post("omitir_receptor");
        $receptorNombre         = $this->post("receptor_nombre");
        $receptorTipoIdentif    = $this->post("receptor_tipo_identif");
        $receptorNumIdentif     = $this->post("receptor_num_identif");
        $receptorProvincia      = $this->post("receptor_provincia");
        $receptorCanton         = $this->post("receptor_canton");
        $receptorDistrito       = $this->post("receptor_distrito");
        $receptorBarrio         = $this->post("receptor_barrio");
        $receptorOtrasSenas     = $this->post("receptor_otras_senas");
        $receptorCodPaisTel     = $this->post("receptor_cod_pais_tel");
        $receptorTel            = $this->post("receptor_tel");
        $receptorCodPaisFax     = $this->post("receptor_cod_pais_fax");
        $receptorFax            = $this->post("receptor_fax");
        $receptorEmail          = $this->post("receptor_email");
    
        // Detalles de tiquete / Factura
        $condVenta              = $this->post("condicion_venta");
        $plazoCredito           = $this->post("plazo_credito");
        $medioPago              = $this->post("medio_pago");
        $codMoneda              = $this->post("cod_moneda");
        $tipoCambio             = $this->post("tipo_cambio");
        $totalServGravados      = $this->post("total_serv_gravados");
        $totalServExentos       = $this->post("total_serv_exentos");
        $totalMercGravadas      = $this->post("total_merc_gravada");
        $totalMercExentas       = $this->post("total_merc_exenta");
        $totalGravados          = $this->post("total_gravados");
        $totalExentos           = $this->post("total_exentos");
        $totalVentas            = $this->post("total_ventas");
        $totalDescuentos        = $this->post("total_descuentos");
        $totalVentasNeta        = $this->post("total_ventas_neta");
        $totalImp               = $this->post("total_impuestos");
        $totalComprobante       = $this->post("total_comprobante");
        $otros                  = $this->post("otros");
        $otrosType              = $this->post("otrosType");
        $infoRefeTipoDoc        = $this->post("infoRefeTipoDoc");
        $infoRefeNumero         = $this->post("infoRefeNumero");
        $infoRefeFechaEmision   = $this->post("infoRefeFechaEmision");
        $infoRefeCodigo         = $this->post("infoRefeCodigo");
        $infoRefeRazon          = $this->post("infoRefeRazon");
    
        // Detalles de la compra
        $detalles               = json_decode($this->post("detalles"));
    
        $xmlString = '<?xml version = "1.0" encoding = "utf-8"?>
        <NotaCreditoElectronica xmlns="https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/notaCreditoElectronica" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/notaCreditoElectronica NotaCreditoElectronica_V4.2.xsd">
        <Clave>' . $clave . '</Clave>
        <NumeroConsecutivo>' . $consecutivo . '</NumeroConsecutivo>
        <FechaEmision>' . $fechaEmision . '</FechaEmision>
        <Emisor>
            <Nombre>' . $emisorNombre . '</Nombre>
            <Identificacion>
                <Tipo>' . $emisorTipoIdentif . '</Tipo>
                <Numero>' . $emisorNumIdentif . '</Numero>
            </Identificacion>
            <NombreComercial>' . $nombreComercial . '</NombreComercial>';
    
    
        if ($emisorProv != '' && $emisorCanton != '' && $emisorDistrito != '' && $emisorOtrasSenas != '')
        {
            $xmlString .= '
            <Ubicacion>
                <Provincia>' . $emisorProv . '</Provincia>
                <Canton>' . $emisorCanton . '</Canton>
                <Distrito>' . $emisorDistrito . '</Distrito>';
            if ($emisorBarrio != '')
                $xmlString .= '<Barrio>' . $emisorBarrio . '</Barrio>';
            $xmlString .= '
                    <OtrasSenas>' . $emisorOtrasSenas . '</OtrasSenas>
                </Ubicacion>';
        }
    
        if ($emisorCodPaisTel != '' && $emisorTel != '')
        {
            $xmlString .= '
            <Telefono>
                <CodigoPais>' . $emisorCodPaisTel . '</CodigoPais>
                <NumTelefono>' . $emisorTel . '</NumTelefono>
            </Telefono>';
        }
    
        if ($emisorCodPaisFax != '' && $emisorFax != '')
        {
            $xmlString .= '
            <Fax>
                <CodigoPais>' . $emisorCodPaisFax . '</CodigoPais>
                <NumTelefono>' . $emisorFax . '</NumTelefono>
            </Fax>';
        }
    
    
        $xmlString .= '<CorreoElectronico>' . $emisorEmail . '</CorreoElectronico>
        </Emisor>';
    
        if ($omitir_receptor != 'true')
        {
            $xmlString .= '<Receptor>
                <Nombre>' . $receptorNombre . '</Nombre>';
    
            if ($receptorTipoIdentif == '05')
            {
                if ($receptorTipoIdentif != '' && $receptorNumIdentif != '')
                {
                    $xmlString .= '<IdentificacionExtranjero>'
                            . $receptorNumIdentif 
                            . ' </IdentificacionExtranjero>';
                }
            }
            else
            {
                if ($receptorTipoIdentif != '' && $receptorNumIdentif != '')
                {
                    $xmlString .= '<Identificacion>
                        <Tipo>' . $receptorTipoIdentif . '</Tipo>
                        <Numero>' . $receptorNumIdentif . '</Numero>
                    </Identificacion>';
                }
    
                if ($receptorProvincia != '' && $receptorCanton != '' && $receptorDistrito != '' && $receptorOtrasSenas != '')
                {
                    $xmlString .= '
                        <Ubicacion>
                            <Provincia>' . $receptorProvincia . '</Provincia>
                            <Canton>' . $receptorCanton . '</Canton>
                            <Distrito>' . $receptorDistrito . '</Distrito>';
                    if ($receptorBarrio != '')
                        $xmlString .= '<Barrio>' . $receptorBarrio . '</Barrio>';
                    $xmlString .= '
                            <OtrasSenas>' . $receptorOtrasSenas . '</OtrasSenas>
                        </Ubicacion>';
                }
            }
    
            if ($receptorCodPaisTel != '' && $receptorTel != '')
            {
                $xmlString .= '<Telefono>
                                  <CodigoPais>' . $receptorCodPaisTel . '</CodigoPais>
                                  <NumTelefono>' . $receptorTel . '</NumTelefono>
                        </Telefono>';
            }
    
            if ($receptorCodPaisFax != '' && $receptorFax != '')
            {
                $xmlString .= '<Fax>
                                  <CodigoPais>' . $receptorCodPaisFax . '</CodigoPais>
                                 <NumTelefono>' . $receptorFax . '</NumTelefono>
                        </Fax>';
            }
    
            if ($receptorEmail != '')
                $xmlString .= '<CorreoElectronico>' . $receptorEmail . '</CorreoElectronico>';
    
            $xmlString .= '</Receptor>';
        }
    
        $xmlString .= '
        <CondicionVenta>' . $condVenta . '</CondicionVenta>
        <PlazoCredito>' . $plazoCredito . '</PlazoCredito>
        <MedioPago>' . $medioPago . '</MedioPago>
        <DetalleServicio>';

        $l = 1;
        foreach ($detalles as $d)
        {
            $xmlString .= '<LineaDetalle>
                <NumeroLinea>' . $l . '</NumeroLinea>
                <Cantidad>' . $d->cantidad . '</Cantidad>
                <UnidadMedida>' . $d->unidadMedida . '</UnidadMedida>
                <Detalle>' . $d->detalle . '</Detalle>
                <PrecioUnitario>' . $d->precioUnitario . '</PrecioUnitario>
                <MontoTotal>' . $d->montoTotal . '</MontoTotal>';
            if (isset($d->montoDescuento) && $d->montoDescuento != "")
                $xmlString .= '<MontoDescuento>' . $d->montoDescuento . '</MontoDescuento>';
    
            if (isset($d->naturalezaDescuento) && $d->naturalezaDescuento != "")
                $xmlString .= '<NaturalezaDescuento>' . $d->naturalezaDescuento . '</NaturalezaDescuento>';
    
            $xmlString .= '<SubTotal>' . $d->subtotal . '</SubTotal>';
    
            if (isset($d->impuesto) && $d->impuesto != "")
            {
                foreach ($d->impuesto as $i)
                {
                    $xmlString .= '<Impuesto>
                    <Codigo>' . $i->codigo . '</Codigo>
                    <Tarifa>' . $i->tarifa . '</Tarifa>
                    <Monto>' . $i->monto . '</Monto>';
    
                    if (isset($i->exoneracion) && $i->exoneracion != "")
                    {
                        $xmlString .= '
                        <Exoneracion>
                            <TipoDocumento>' . $i->exoneracion->tipoDocumento . '</TipoDocumento>
                            <NumeroDocumento>' . $i->exoneracion->numeroDocumento . '</NumeroDocumento>
                            <NombreInstitucion>' . $i->exoneracion->nombreInstitucion . '</NombreInstitucion>
                            <FechaEmision>' . $i->exoneracion->fechaEmision . '</FechaEmision>
                            <MontoImpuesto>' . $i->exoneracion->montoImpuesto . '</MontoImpuesto>
                            <PorcentajeCompra>' . $i->exoneracion->porcentajeCompra . '</PorcentajeCompra>
                        </Exoneracion>';
                    }
    
                    $xmlString .= '</Impuesto>';
                }
            }
    
            $xmlString .= '<MontoTotalLinea>' . $d->montoTotalLinea . '</MontoTotalLinea>';
            $xmlString .= '</LineaDetalle>';
            $l++;
        }
    
        $xmlString .= '</DetalleServicio>
        <ResumenFactura>
            <CodigoMoneda>' . $codMoneda . '</CodigoMoneda>
            <TipoCambio>' . $tipoCambio . '</TipoCambio>
            <TotalServGravados>' . $totalServGravados . '</TotalServGravados>
            <TotalServExentos>' . $totalServExentos . '</TotalServExentos>
            <TotalMercanciasGravadas>' . $totalMercGravadas . '</TotalMercanciasGravadas>
            <TotalMercanciasExentas>' . $totalMercExentas . '</TotalMercanciasExentas>
            <TotalGravado>' . $totalGravados . '</TotalGravado>
            <TotalExento>' . $totalExentos . '</TotalExento>
            <TotalVenta>' . $totalVentas . '</TotalVenta>
            <TotalDescuentos>' . $totalDescuentos . '</TotalDescuentos>
            <TotalVentaNeta>' . $totalVentasNeta . '</TotalVentaNeta>
            <TotalImpuesto>' . $totalImp . '</TotalImpuesto>
            <TotalComprobante>' . $totalComprobante . '</TotalComprobante>
        </ResumenFactura>
        <InformacionReferencia>
            <TipoDoc>' . $infoRefeTipoDoc . '</TipoDoc>
            <Numero>' . $infoRefeNumero . '</Numero>
            <FechaEmision>' . $infoRefeFechaEmision . '</FechaEmision>
            <Codigo>' . $infoRefeCodigo . '</Codigo>
            <Razon>' . $infoRefeRazon . '</Razon>
        </InformacionReferencia>
        <Normativa>
            <NumeroResolucion>DGT-R-48-2016</NumeroResolucion>
            <FechaResolucion>07-10-2016 08:00:00</FechaResolucion>
        </Normativa>';
        if ($otros != '' && $otrosType != '')
        {
            $tipos = array("Otros", "OtroTexto", "OtroContenido");
            if (in_array($otrosType, $tipos))
            {
                $xmlString .= '
                    <Otros>
                <' . $otrosType . '>' . $otros . '</' . $otrosType . '>
                </Otros>';
            }
        }
        $xmlString .= '
        </NotaCreditoElectronica>';
    
        $arrayResp = array(
            "clave" => $clave,
            "xml"   => base64_encode($xmlString)
        );
    
        $this->set_response($arrayResp, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    public function nd_post()
    {
        // Datos contribuyente
        $clave                  = $this->post("clave");
        $consecutivo            = $this->post("consecutivo");
        $fechaEmision           = $this->post("fecha_emision");
    
        // Datos emisor
        $emisorNombre           = $this->post("emisor_nombre");
        $emisorTipoIdentif      = $this->post("emisor_tipo_indetif");
        $emisorNumIdentif       = $this->post("emisor_num_identif");
        $nombreComercial        = $this->post("nombre_comercial");
        $emisorProv             = $this->post("emisor_provincia");
        $emisorCanton           = $this->post("emisor_canton");
        $emisorDistrito         = $this->post("emisor_distrito");
        $emisorBarrio           = $this->post("emisor_barrio");
        $emisorOtrasSenas       = $this->post("emisor_otras_senas");
        $emisorCodPaisTel       = $this->post("emisor_cod_pais_tel");
        $emisorTel              = $this->post("emisor_tel");
        $emisorCodPaisFax       = $this->post("emisor_cod_pais_fax");
        $emisorFax              = $this->post("emisor_fax");
        $emisorEmail            = $this->post("emisor_email");
    
        // Datos receptor
        $omitir_receptor        = $this->post("omitir_receptor");
        $receptorNombre         = $this->post("receptor_nombre");
        $receptorTipoIdentif    = $this->post("receptor_tipo_identif");
        $receptorNumIdentif     = $this->post("receptor_num_identif");
        $receptorProvincia      = $this->post("receptor_provincia");
        $receptorCanton         = $this->post("receptor_canton");
        $receptorDistrito       = $this->post("receptor_distrito");
        $receptorBarrio         = $this->post("receptor_barrio");
        $receptorOtrasSenas     = $this->post("receptor_otras_senas");
        $receptorCodPaisTel     = $this->post("receptor_cod_pais_tel");
        $receptorTel            = $this->post("receptor_tel");
        $receptorCodPaisFax     = $this->post("receptor_cod_pais_fax");
        $receptorFax            = $this->post("receptor_fax");
        $receptorEmail          = $this->post("receptor_email");
    
        // Detalles de tiquete / Factura
        $condVenta              = $this->post("condicion_venta");
        $plazoCredito           = $this->post("plazo_credito");
        $medioPago              = $this->post("medio_pago");
        $codMoneda              = $this->post("cod_moneda");
        $tipoCambio             = $this->post("tipo_cambio");
        $totalServGravados      = $this->post("total_serv_gravados");
        $totalServExentos       = $this->post("total_serv_exentos");
        $totalMercGravadas      = $this->post("total_merc_gravada");
        $totalMercExentas       = $this->post("total_merc_exenta");
        $totalGravados          = $this->post("total_gravados");
        $totalExentos           = $this->post("total_exentos");
        $totalVentas            = $this->post("total_ventas");
        $totalDescuentos        = $this->post("total_descuentos");
        $totalVentasNeta        = $this->post("total_ventas_neta");
        $totalImp               = $this->post("total_impuestos");
        $totalComprobante       = $this->post("total_comprobante");
        $otros                  = $this->post("otros");
        $otrosType              = $this->post("otrosType");
        $infoRefeTipoDoc        = $this->post("infoRefeTipoDoc");
        $infoRefeNumero         = $this->post("infoRefeNumero");
        $infoRefeFechaEmision   = $this->post("infoRefeFechaEmision");
        $infoRefeCodigo         = $this->post("infoRefeCodigo");
        $infoRefeRazon          = $this->post("infoRefeRazon");
    
        // Detalles de la compra
        $detalles               = json_decode($this->post("detalles"));
    
        $xmlString = '<?xml version="1.0" encoding="utf-8"?>
        <NotaDebitoElectronica xmlns="https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/notaDebitoElectronica" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/notaDebitoElectronica NotaDebitoElectronica_V4.2.xsd">
        <Clave>' . $clave . '</Clave>
        <NumeroConsecutivo>' . $consecutivo . '</NumeroConsecutivo>
        <FechaEmision>' . $fechaEmision . '</FechaEmision>
        <Emisor>
            <Nombre>' . $emisorNombre . '</Nombre>
            <Identificacion>
                <Tipo>' . $emisorTipoIdentif . '</Tipo>
                <Numero>' . $emisorNumIdentif . '</Numero>
            </Identificacion>
            <NombreComercial>' . $nombreComercial . '</NombreComercial>';
    
        if ($emisorProv != '' && $emisorCanton != '' && $emisorDistrito != '' && $emisorOtrasSenas != '')
        {
            $xmlString .= '
            <Ubicacion>
                <Provincia>' . $emisorProv . '</Provincia>
                <Canton>' . $emisorCanton . '</Canton>
                <Distrito>' . $emisorDistrito . '</Distrito>';
            if ($emisorBarrio != '')
                $xmlString .= '<Barrio>' . $emisorBarrio . '</Barrio>';
            $xmlString .= '
                    <OtrasSenas>' . $emisorOtrasSenas . '</OtrasSenas>
                </Ubicacion>';
        }
    
        if ($emisorCodPaisTel != '' && $emisorTel != '')
        {
            $xmlString .= '
            <Telefono>
                <CodigoPais>' . $emisorCodPaisTel . '</CodigoPais>
                <NumTelefono>' . $emisorTel . '</NumTelefono>
            </Telefono>';
        }
    
        if ($emisorCodPaisFax != '' && $emisorFax != '')
        {
            $xmlString .= '
            <Fax>
                <CodigoPais>' . $emisorCodPaisFax . '</CodigoPais>
                <NumTelefono>' . $emisorFax . '</NumTelefono>
            </Fax>';
        }
    
        $xmlString .= '<CorreoElectronico>' . $emisorEmail . '</CorreoElectronico>
        </Emisor>';
    
        if ($omitir_receptor != 'true')
        {
            $xmlString .= '<Receptor>
                <Nombre>' . $receptorNombre . '</Nombre>';
    
            if ($receptorTipoIdentif == '05')
            {
                if ($receptorTipoIdentif != '' &&  $receptorNumIdentif != '')
                {
                    $xmlString .= '<IdentificacionExtranjero>'
                            . $receptorNumIdentif 
                            . ' </IdentificacionExtranjero>';
                }
            }
            else
            {
                if ($receptorTipoIdentif != '' && $receptorNumIdentif != '')
                {
                    $xmlString .= '<Identificacion>
                        <Tipo>' . $receptorTipoIdentif . '</Tipo>
                        <Numero>' . $receptorNumIdentif . '</Numero>
                    </Identificacion>';
                }
    
                if ($receptorProvincia != '' && $receptorCanton != '' && $receptorDistrito != '' && $receptorOtrasSenas != '')
                {
                    $xmlString .= '
                        <Ubicacion>
                            <Provincia>' . $receptorProvincia . '</Provincia>
                            <Canton>' . $receptorCanton . '</Canton>
                            <Distrito>' . $receptorDistrito . '</Distrito>';
                    if ($receptorBarrio != '')
                        $xmlString .= '<Barrio>' . $receptorBarrio . '</Barrio>';
                    $xmlString .= '
                            <OtrasSenas>' . $receptorOtrasSenas . '</OtrasSenas>
                        </Ubicacion>';
                }
            }
    
            if ($receptorCodPaisTel != '' && $receptorTel != '')
            {
                $xmlString .= '<Telefono>
                                  <CodigoPais>' . $receptorCodPaisTel . '</CodigoPais>
                                  <NumTelefono>' . $receptorTel . '</NumTelefono>
                        </Telefono>';
            }
    
            if ($receptorCodPaisFax != '' && $receptorFax != '')
            {
                $xmlString .= '<Fax>
                                  <CodigoPais>' . $receptorCodPaisFax . '</CodigoPais>
                                 <NumTelefono>' . $receptorFax . '</NumTelefono>
                        </Fax>';
            }
    
            if ($receptorEmail != '')
                $xmlString .= '<CorreoElectronico>' . $receptorEmail . '</CorreoElectronico>';
    
            $xmlString .= '</Receptor>';
        }
    
        $xmlString .= '
        <CondicionVenta>' . $condVenta . '</CondicionVenta>
        <PlazoCredito>' . $plazoCredito . '</PlazoCredito>
        <MedioPago>' . $medioPago . '</MedioPago>
        <DetalleServicio>';

        $l = 1;
        foreach ($detalles as $d)
        {
            $xmlString .= '<LineaDetalle>
                <NumeroLinea>' . $l . '</NumeroLinea>
                <Cantidad>' . $d->cantidad . '</Cantidad>
                <UnidadMedida>' . $d->unidadMedida . '</UnidadMedida>
                <Detalle>' . $d->detalle . '</Detalle>
                <PrecioUnitario>' . $d->precioUnitario . '</PrecioUnitario>
                <MontoTotal>' . $d->montoTotal . '</MontoTotal>';
    
            if (isset($d->montoDescuento) && $d->montoDescuento != "")
                $xmlString .= '<MontoDescuento>' . $d->montoDescuento . '</MontoDescuento>';
    
            if (isset($d->naturalezaDescuento) && $d->naturalezaDescuento != "")
                $xmlString .= '<NaturalezaDescuento>' . $d->naturalezaDescuento . '</NaturalezaDescuento>';
    
            $xmlString .= '<SubTotal>' . $d->subtotal . '</SubTotal>';
    
            if (isset($d->impuesto) && $d->impuesto != "")
            {
                foreach ($d->impuesto as $i)
                {
                    $xmlString .= '<Impuesto>
                    <Codigo>' . $i->codigo . '</Codigo>
                    <Tarifa>' . $i->tarifa . '</Tarifa>
                    <Monto>' . $i->monto . '</Monto>';
    
                    if (isset($i->exoneracion) && $i->exoneracion != "")
                    {
                        $xmlString .= '
                        <Exoneracion>
                            <TipoDocumento>' . $i->exoneracion->tipoDocumento . '</TipoDocumento>
                            <NumeroDocumento>' . $i->exoneracion->numeroDocumento . '</NumeroDocumento>
                            <NombreInstitucion>' . $i->exoneracion->nombreInstitucion . '</NombreInstitucion>
                            <FechaEmision>' . $i->exoneracion->fechaEmision . '</FechaEmision>
                            <MontoImpuesto>' . $i->exoneracion->montoImpuesto . '</MontoImpuesto>
                            <PorcentajeCompra>' . $i->exoneracion->porcentajeCompra . '</PorcentajeCompra>
                        </Exoneracion>';
                    }
    
                    $xmlString .= '</Impuesto>';
                }
            }
    
            $xmlString .= '<MontoTotalLinea>' . $d->montoTotalLinea . '</MontoTotalLinea>';
            $xmlString .= '</LineaDetalle>';
            $l++;
        }
    
        $xmlString .= '</DetalleServicio>
        <ResumenFactura>
            <CodigoMoneda>' . $codMoneda . '</CodigoMoneda>
            <TipoCambio>' . $tipoCambio . '</TipoCambio>
            <TotalServGravados>' . $totalServGravados . '</TotalServGravados>
            <TotalServExentos>' . $totalServExentos . '</TotalServExentos>
            <TotalMercanciasGravadas>' . $totalMercGravadas . '</TotalMercanciasGravadas>
            <TotalMercanciasExentas>' . $totalMercExentas . '</TotalMercanciasExentas>
            <TotalGravado>' . $totalGravados . '</TotalGravado>
            <TotalExento>' . $totalExentos . '</TotalExento>
            <TotalVenta>' . $totalVentas . '</TotalVenta>
            <TotalDescuentos>' . $totalDescuentos . '</TotalDescuentos>
            <TotalVentaNeta>' . $totalVentasNeta . '</TotalVentaNeta>
            <TotalImpuesto>' . $totalImp . '</TotalImpuesto>
            <TotalComprobante>' . $totalComprobante . '</TotalComprobante>
        </ResumenFactura>
        <InformacionReferencia>
            <TipoDoc>' . $infoRefeTipoDoc . '</TipoDoc>
            <Numero>' . $infoRefeNumero . '</Numero>
            <FechaEmision>' . $infoRefeFechaEmision . '</FechaEmision>
            <Codigo>' . $infoRefeCodigo . '</Codigo>
            <Razon>' . $infoRefeRazon . '</Razon>
        </InformacionReferencia>
        <Normativa>
            <NumeroResolucion>DGT-R-48-2016</NumeroResolucion>
            <FechaResolucion>07-10-2016 08:00:00</FechaResolucion>
        </Normativa>';
        if ($otros != '' && $otrosType != '')
        {
            $tipos = array("Otros", "OtroTexto", "OtroContenido");
            if (in_array($otrosType, $tipos))
            {
                $xmlString .= '
                    <Otros>
                <' . $otrosType . '>' . $otros . '</' . $otrosType . '>
                </Otros>';
            }
        }
    
        $xmlString .= '
            </NotaDebitoElectronica>';
    
        $arrayResp = array(
            "clave" => $clave,
            "xml" => base64_encode($xmlString)
        );

        $this->set_response($arrayResp, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    function te_post()
    {
        // Datos contribuyente
        $clave                  = $this->post("clave");
        $consecutivo            = $this->post("consecutivo");
        $fechaEmision           = $this->post("fecha_emision");
    
        // Datos emisor
        $emisorNombre           = $this->post("emisor_nombre");
        $emisorTipoIdentif      = $this->post("emisor_tipo_indetif");
        $emisorNumIdentif       = $this->post("emisor_num_identif");
        $nombreComercial        = $this->post("nombre_comercial");
        $emisorProv             = $this->post("emisor_provincia");
        $emisorCanton           = $this->post("emisor_canton");
        $emisorDistrito         = $this->post("emisor_distrito");
        $emisorBarrio           = $this->post("emisor_barrio");
        $emisorOtrasSenas       = $this->post("emisor_otras_senas");
        $emisorCodPaisTel       = $this->post("emisor_cod_pais_tel");
        $emisorTel              = $this->post("emisor_tel");
        $emisorCodPaisFax       = $this->post("emisor_cod_pais_fax");
        $emisorFax              = $this->post("emisor_fax");
        $emisorEmail            = $this->post("emisor_email");
    
        // Datos receptor
        $omitir_receptor        = $this->post("omitir_receptor");
        $receptorNombre         = $this->post("receptor_nombre");
        $receptorTipoIdentif    = $this->post("receptor_tipo_identif");
        $receptorNumIdentif     = $this->post("receptor_num_identif");
        $receptorProvincia      = $this->post("receptor_provincia");
        $receptorCanton         = $this->post("receptor_canton");
        $receptorDistrito       = $this->post("receptor_distrito");
        $receptorBarrio         = $this->post("receptor_barrio");
        $receptorOtrasSenas     = $this->post("receptor_otras_senas");
        $receptorCodPaisTel     = $this->post("receptor_cod_pais_tel");
        $receptorTel            = $this->post("receptor_tel");
        $receptorCodPaisFax     = $this->post("receptor_cod_pais_fax");
        $receptorFax            = $this->post("receptor_fax");
        $receptorEmail          = $this->post("receptor_email");
    
        // Detalles de tiquete / Factura
        $condVenta              = $this->post("condicion_venta");
        $plazoCredito           = $this->post("plazo_credito");
        $medioPago              = $this->post("medio_pago");
        $codMoneda              = $this->post("cod_moneda");
        $tipoCambio             = $this->post("tipo_cambio");
        $totalServGravados      = $this->post("total_serv_gravados");
        $totalServExentos       = $this->post("total_serv_exentos");
        $totalMercGravadas      = $this->post("total_merc_gravada");
        $totalMercExentas       = $this->post("total_merc_exenta");
        $totalGravados          = $this->post("total_gravados");
        $totalExentos           = $this->post("total_exentos");
        $totalVentas            = $this->post("total_ventas");
        $totalDescuentos        = $this->post("total_descuentos");
        $totalVentasNeta        = $this->post("total_ventas_neta");
        $totalImp               = $this->post("total_impuestos");
        $totalComprobante       = $this->post("total_comprobante");
        $otros                  = $this->post("otros");
        $otrosType              = $this->post("otrosType");
    
        // Detalles de la compra
        $detalles               = json_decode($this->post("detalles"));
    
        $xmlString = '<?xml version="1.0" encoding="utf-8"?>
        <TiqueteElectronico xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/tiqueteElectronico" xsi:schemaLocation="https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/tiqueteElectronico https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/tiqueteElectronico.xsd">
        <Clave>' . $clave . '</Clave>
        <NumeroConsecutivo>' . $consecutivo . '</NumeroConsecutivo>
        <FechaEmision>' . $fechaEmision . '</FechaEmision>
        <Emisor>
            <Nombre>' . $emisorNombre . '</Nombre>
            <Identificacion>
                <Tipo>' . $emisorTipoIdentif . '</Tipo>
                <Numero>' . $emisorNumIdentif . '</Numero>
            </Identificacion>
            <NombreComercial>' . $nombreComercial . '</NombreComercial>';
    
        if ($emisorProv != '' && $emisorCanton != '' && $emisorDistrito != '' && $emisorOtrasSenas != '')
        {
            $xmlString .= '
            <Ubicacion>
                <Provincia>' . $emisorProv . '</Provincia>
                <Canton>' . $emisorCanton . '</Canton>
                <Distrito>' . $emisorDistrito . '</Distrito>';
            if ($emisorBarrio != '')
                $xmlString .= '<Barrio>' . $emisorBarrio . '</Barrio>';
            $xmlString .= '
                    <OtrasSenas>' . $emisorOtrasSenas . '</OtrasSenas>
                </Ubicacion>';
        }
    
        if ($emisorCodPaisTel != '' && $emisorTel != '')
        {
            $xmlString .= '
            <Telefono>
                <CodigoPais>' . $emisorCodPaisTel . '</CodigoPais>
                <NumTelefono>' . $emisorTel . '</NumTelefono>
            </Telefono>';
        }
    
        if ($emisorCodPaisFax != '' && $emisorFax != '')
        {
            $xmlString .= '
            <Fax>
                <CodigoPais>' . $emisorCodPaisFax . '</CodigoPais>
                <NumTelefono>' . $emisorFax . '</NumTelefono>
            </Fax>';
        }
    
        $xmlString .= '<CorreoElectronico>' . $emisorEmail . '</CorreoElectronico>
        </Emisor>';
    
        $xmlString .= '
        <CondicionVenta>' . $condVenta . '</CondicionVenta>
        <PlazoCredito>' . $plazoCredito . '</PlazoCredito>
        <MedioPago>' . $medioPago . '</MedioPago>
        <DetalleServicio>';
    
        $l = 1;
        foreach ($detalles as $d)
        {
            $xmlString .= '<LineaDetalle>
                <NumeroLinea>' . $l . '</NumeroLinea>
                <Cantidad>' . $d->cantidad . '</Cantidad>
                <UnidadMedida>' . $d->unidadMedida . '</UnidadMedida>
                <Detalle>' . $d->detalle . '</Detalle>
                <PrecioUnitario>' . $d->precioUnitario . '</PrecioUnitario>
                <MontoTotal>' . $d->montoTotal . '</MontoTotal>';
    
            if (isset($d->montoDescuento) && $d->montoDescuento != "")
                $xmlString .= '<MontoDescuento>' . $d->montoDescuento . '</MontoDescuento>';
    
            if (isset($d->naturalezaDescuento) && $d->naturalezaDescuento != "")
                $xmlString .= '<NaturalezaDescuento>' . $d->naturalezaDescuento . '</NaturalezaDescuento>';
    
            $xmlString .= '<SubTotal>' . $d->subtotal . '</SubTotal>';
    
            if (isset($d->impuesto) && $d->impuesto != "")
            {
                foreach ($d->impuesto as $i)
                {
                    $xmlString .= '<Impuesto>
                    <Codigo>' . $i->codigo . '</Codigo>
                    <Tarifa>' . $i->tarifa . '</Tarifa>
                    <Monto>' . $i->monto . '</Monto>';
    
                    if (isset($i->exoneracion) && $i->exoneracion != "")
                    {
                        $xmlString .= '
                        <Exoneracion>
                            <TipoDocumento>' . $i->exoneracion->tipoDocumento . '</TipoDocumento>
                            <NumeroDocumento>' . $i->exoneracion->numeroDocumento . '</NumeroDocumento>
                            <NombreInstitucion>' . $i->exoneracion->nombreInstitucion . '</NombreInstitucion>
                            <FechaEmision>' . $i->exoneracion->fechaEmision . '</FechaEmision>
                            <MontoImpuesto>' . $i->exoneracion->montoImpuesto . '</MontoImpuesto>
                            <PorcentajeCompra>' . $i->exoneracion->porcentajeCompra . '</PorcentajeCompra>
                        </Exoneracion>';
                    }
    
                    $xmlString .= '</Impuesto>';
                }
            }
    
            $xmlString .= '<MontoTotalLinea>' . $d->montoTotalLinea . '</MontoTotalLinea>';
            $xmlString .= '</LineaDetalle>';
            $l++;
        }
    
        $xmlString .= '</DetalleServicio>
        <ResumenFactura>
            <CodigoMoneda>' . $codMoneda . '</CodigoMoneda>
            <TipoCambio>' . $tipoCambio . '</TipoCambio>
            <TotalServGravados>' . $totalServGravados . '</TotalServGravados>
            <TotalServExentos>' . $totalServExentos . '</TotalServExentos>
            <TotalMercanciasGravadas>' . $totalMercGravadas . '</TotalMercanciasGravadas>
            <TotalMercanciasExentas>' . $totalMercExentas . '</TotalMercanciasExentas>
            <TotalGravado>' . $totalGravados . '</TotalGravado>
            <TotalExento>' . $totalExentos . '</TotalExento>
            <TotalVenta>' . $totalVentas . '</TotalVenta>
            <TotalDescuentos>' . $totalDescuentos . '</TotalDescuentos>
            <TotalVentaNeta>' . $totalVentasNeta . '</TotalVentaNeta>
            <TotalImpuesto>' . $totalImp . '</TotalImpuesto>
            <TotalComprobante>' . $totalComprobante . '</TotalComprobante>
        </ResumenFactura>
        <Normativa>
            <NumeroResolucion>DGT-R-48-2016</NumeroResolucion>
            <FechaResolucion>07-10-2016 08:00:00</FechaResolucion>
        </Normativa>';
        if ($otros != '' && $otrosType != '')
        {
            $tipos = array("Otros", "OtroTexto", "OtroContenido");
            if (in_array($otrosType, $tipos))
            {
                $xmlString .= '
                    <Otros>
                <' . $otrosType . '>' . $otros . '</' . $otrosType . '>
                </Otros>';
            }
        }
    
        $xmlString .= '
        </TiqueteElectronico>';
        $arrayResp = array(
            "clave" => $clave,
            "xml" => base64_encode($xmlString)
        );

        $this->set_response($arrayResp, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }

    function mr_post()
    {
        $clave                          = $this->post("clave");                                      // d{50,50}
        // Datos vendedor = emisor
        $numeroCedulaEmisor             = $this->post("numero_cedula_emisor");                       // d{12,12} cedula fisica,juridica,NITE,DIMEX
        $numeroCedulaEmisor             = str_pad($numeroCedulaEmisor, 12, "0", STR_PAD_LEFT);
    
        // Datos mensaje receptor
        $fechaEmisionDoc                = $this->post("fecha_emision_doc");                          // fecha de emision de la confirmacion
        $mensaje                        = $this->post("mensaje");                                    // 1 - Aceptado, 2 - Aceptado Parcialmente, 3 - Rechazado
        $detalleMensaje                 = $this->post("detalle_mensaje");
        $montoTotalImpuesto             = $this->post("monto_total_impuesto");                       // d18,5 opcional /obligatorio si comprobante tenga impuesto
        $totalFactura                   = $this->post("total_factura");                              // d18,5
        $numeroConsecutivoReceptor      = $this->post("numero_consecutivo_receptor");                // d{20,20} numeracion consecutiva de los mensajes de confirmacion
    
        // Datos comprador = receptor
        $numeroCedulaReceptor           = $this->post("numero_cedula_receptor");                     // d{12,12}cedula fisica, juridica, NITE, DIMEX del comprador
        $numeroCedulaReceptor           = str_pad($numeroCedulaReceptor, 12, "0", STR_PAD_LEFT);
    
        $xmlString = '<?xml version="1.0" encoding="utf-8"?>
        <MensajeReceptor xmlns="https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/mensajeReceptor" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.2/mensajeReceptor MensajeReceptor_4.2.xsd">
        <Clave>' . $clave . '</Clave>
        <NumeroCedulaEmisor>' . $numeroCedulaEmisor . '</NumeroCedulaEmisor>
        <FechaEmisionDoc>' . $fechaEmisionDoc . '</FechaEmisionDoc>
        <Mensaje>' . $mensaje . '</Mensaje>';
        if (!empty($detalleMensaje))
            $xmlString .= '<DetalleMensaje>' . $detalleMensaje . '</DetalleMensaje>';
    
        if (!empty($montoTotalImpuesto))
            $xmlString .= '<MontoTotalImpuesto>' . $montoTotalImpuesto . '</MontoTotalImpuesto>';
    
        $xmlString .= '<TotalFactura>' . $totalFactura . '</TotalFactura>
        <NumeroCedulaReceptor>' . $numeroCedulaReceptor . '</NumeroCedulaReceptor>
        <NumeroConsecutivoReceptor>' . $numeroConsecutivoReceptor . '</NumeroConsecutivoReceptor>';
    
        $xmlString .= '</MensajeReceptor>';
        $arrayResp = array(
            "clave" => $clave,
            "xml"   => base64_encode($xmlString)
        );
    
        $this->set_response($arrayResp, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    }
}
