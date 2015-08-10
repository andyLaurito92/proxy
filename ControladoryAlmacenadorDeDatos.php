<?php

require_once('BalanceadorDePedidos.php');

class ControladorYAlmacenadorDeDatos{

    var $rutaArchivoDeDatosDeRequests = 'datosRequests.json';

    var $unBalanceadorDePedidos;

    var $accesos_por_ip = array();

    //Las apis que actualmente existen las podemos ver en http://developers.mercadolibre.com/api-health-view/
    var $accesos_por_recurso = array(
        "users"=>0,
        "shipments"=>0,
        "search"=>0,
        "questions"=>0,
        );

    const MAXIMA_CANTIDAD_ACCESOS_POR_IP = 1;

    const MAXIMA_CANTIDAD_ACCESOS_POR_RECURSO = 2;


    public function ControladorYAlmacenadorDeDatos($unBalanceador){

        $this->unBalanceadorDePedidos = $unBalanceador;

        $this->iniciarTarea();
    }

    public function iniciarTarea()
    {
        if( ! $this->estaVacio($this->rutaArchivoDeDatosDeRequests) )
        {
            $this->procesarYAlmacenarDatosDelArchivoDeRequests();

            $this->actualizarListaDeAccesosDelBalanceador();

            $this->unBalanceadorDePedidos->imprimirAccesList();
        }
    }


    function procesarYAlmacenarDatosDelArchivoDeRequests()
    {
        $listaJsons = $this->obtenerDatosDelArchivo();

        foreach ( $listaJsons as $jsonConDatos )
        {
            $recursoAccedido = $this->obtenerNombreDelRecurso($jsonConDatos['request']);

            $this->almacenarDatosEnBase($recursoAccedido,$jsonConDatos);

            $this->incrementarAccesosDeLaIP($jsonConDatos['ipOrigen']);

            $this->accesos_por_recurso[$recursoAccedido] += 1;

        }

    }

    function actualizarListaDeAccesosDelBalanceador()
    {
        $ipsQueSuperanElMaximo = $this->obtenerValoresQueSuperanElMaximo($this->accesos_por_ip,self::MAXIMA_CANTIDAD_ACCESOS_POR_IP);

        $recursosQueSuperanElMaximo = $this->obtenerValoresQueSuperanElMaximo($this->accesos_por_recurso,self::MAXIMA_CANTIDAD_ACCESOS_POR_RECURSO);

        if( array_size($ipsQueSuperanElMaximo) > 0 )        $this->unBalanceadorDePedidos->actualizarListaDeAccesosDeIps($ipsQueSuperanElMaximo);

        if( array_size($recursosQueSuperanElMaximo) > 0 )    $this->unBalanceadorDePedidos->actualizarLIstaDeAccesosDeRecursos($recursosQueSuperanElMaximo);

    }

    function obtenerDatosDelArchivo()
    {
        //Estas dos operaciones de a continuación deberian ser atómicas

        $datos = file_get_contents($this->rutaArchivoDeDatosDeRequests);

        file_put_contents($this->rutaArchivoDeDatosDeRequests, "");

        $json = json_decode($datos,true);

        if( $json == NULL )     die("Ocurrio un error al tratar de decodificar el json");

        return $json['datos'];
    }

    function obtenerNombreDelRecurso($unaUri)
    {
        $path_url = parse_url($unaUri,PHP_URL_PATH);
        return explode('/',$path_url)[1] ;
    }

    function almacenarDatosEnBase($unRecurso,$unJsonConDatos)
    {

    }

    function incrementarAccesosDeLaIP($unaIp)
    {
        if( ! array_key_exists($unaIp, $this->accesos_por_ip ) ) $this->accesos_por_ip[$unaIp] = 0;

        $this->accesos_por_ip[$unaIp] += 1;
    }

    function obtenerValoresQueSuperanElMaximo($unArray,$unMaximo)
    {
        $arrayKeysQueSuperanElMaximo = array();
        foreach ( $unArray as $key=>$valor )
        {
            if ( $valor > $unMaximo )
                $arrayKeysQueSuperanElMaximo[$key] = $valor;
        }
        return $arrayKeysQueSuperanElMaximo;
    }

    function estaVacio($unaPathAUnArchivo)
    {
        return filesize($unaPathAUnArchivo) == 0;
    }

}

$controlador = new ControladorYAlmacenadorDeDatos( new BalanceadorDePedidos() );


?>
