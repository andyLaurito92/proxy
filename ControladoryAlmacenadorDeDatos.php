<?php

class ControladorYAlmacenadorDeDatos{

    var $rutaArchivoDeDatosDeRequests = 'datosRequests.json';

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


    public function ControladorYAlmacenadorDeDatos(){

        $this->iniciarTarea();
    }

    public function iniciarTarea()
    {
        if( ! $this->estaVacio($this->rutaArchivoDeDatosDeRequests) )
        {
            $this->procesarYAlmacenarDatosDelArchivoDeRequests();

            $this->actualizarListaDeAccesosDelBalanceador();
        }
    }


    function procesarYAlmacenarDatosDelArchivoDeRequests(){

        //Estas dos operaciones de a continuación deberian ser atómicas

            $datos = file_get_contents($this->rutaArchivoDeDatosDeRequests);

            file_put_contents($this->rutaArchivoDeDatosDeRequests, "");

        $json = json_decode($datos,true);

        if( $json == NULL )     die("Ocurrio un error al tratar de decodificar el json");

        $listaDeDatosAAlmacenar = $json['datos'];

        foreach ( $listaDeDatosAAlmacenar as $jsonConDatos )
        {
            $recursoAccedido = $this->obtenerNombreDelRecurso($jsonConDatos['request']);

            //Guardamos los datos en la base mysql

            $this->incrementarAccesosDeLaIP($jsonConDatos['ipOrigen']);

            $this->accesos_por_recurso[$recursoAccedido] += 1;

        }

        echo $json['datos'][0]['ipOrigen'];

        var_dump($this->accesos_por_recurso);

    }

    function actualizarListaDeAccesosDelBalanceador()
    {
        $this->verificarMaximosEn($this->accesos_por_ip,self::MAXIMA_CANTIDAD_ACCESOS_POR_IP);

        $this->verificarMaximosEn($this->accesos_por_recurso,self::MAXIMA_CANTIDAD_ACCESOS_POR_RECURSO);

    }

    function verificarMaximosEn($unArray,$unMaximo)
    {
        foreach ( $unArray as $key=>$valor )
        {
            if ( $valor > $unMaximo )
                echo 'Se supero el máximo de '.$key;
        }
    }

    function obtenerNombreDelRecurso($unaUri)
    {
        $path_url = parse_url($unaUri,PHP_URL_PATH);
        return explode('/',$path_url)[1] ;
    }

    function incrementarAccesosDeLaIP($unaIp)
    {
        if( ! array_key_exists($unaIp, $this->accesos_por_ip ) ) $this->accesos_por_ip[$unaIp] = 0;

        $this->accesos_por_ip[$unaIp] += 1;
    }

    function estaVacio($unaPathAUnArchivo)
    {
        return filesize($unaPathAUnArchivo) == 0;
    }

}

echo date('m/d/Y h:i:s a', time());

$controlador = new ControladorYAlmacenadorDeDatos();


?>
