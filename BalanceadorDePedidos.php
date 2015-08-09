<?php

    class BalanceadorDePedidos{

        var $rutaArchivoDeDatosDeRequests = 'datosRequests.json';

        var $access_list = array();

        function BalanceadorDePedidos(){



        }

        function recibirRequest($request_solicitada)
        {

        }

        function actualizarListaDeAccesosDeIps($unaListaDeIpsQueSuperanMaximo)
        {
            foreach ( $unaListaDeIpsQueSuperanMaximo as $ip=>$valor )
                $this->access_list[$ip] = 'FORBIDDEN';
        }

        function actualizarLIstaDeAccesosDeRecursos($unaListaDeRecursosQueSuperanMaximo)
        {
            foreach ( $unaListaDeRecursosQueSuperanMaximo as $recurso=>$cantidadAccesos )
                $this->access_list[$recurso] = 'FORBIDDEN';
        }

        function imprimirAccesList()
        {
            ob_start();
            var_dump($this->access_list);
            $access_list = ob_get_clean();

            file_put_contents('listaAccessos.txt', $access_list);
        }


    }











?>