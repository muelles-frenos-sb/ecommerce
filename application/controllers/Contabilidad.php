<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author: 	John Arley Cano Salinas
 * Fecha: 		4 de octubre de 2025
 * Programa:  	Simón Bolívar | Módulo de Contabilidad
 * Email: 		johnarleycano@hotmail.com
 */
class Contabilidad extends CI_Controller {
    function __construct() {
        parent::__construct();
     
        // Carga de modelos y librerías
        $this->load->model(['contabilidad_model', 'productos_model']);
    }

    var $directorio_raiz = 'archivos/documentos_contables/';

    /**
     * Gestión de comprobantes
     *
     * @return void
     */
    function comprobantes($tipo) {
        switch ($tipo) {
            case 'validacion':
                $this->data['contenido_principal'] = 'contabilidad/validacion_comprobantes/index';
                $this->load->view('core/body', $this->data);
            break;
        }
    }

    /**
     * De cada documento contable, busca si tiene más documentos como soporte
     *
     * @param string $ruta_carpeta
     * @param string $nombre_base
     * @return void
     */
    function buscar_documentos_adicionales($ruta_carpeta, $nombre_base) {
        $documentos = [];
        
        // Se escanean todos los archivos de la carpeta
        $archivos = scandir($ruta_carpeta);
        
        foreach ($archivos as $archivo) {
            if ($archivo === '.' || $archivo === '..') continue;
            
            $ruta_completa = $ruta_carpeta . '/' . $archivo;
            
            // Si no es el archivo principal, es un documento adicional
            if (is_file($ruta_completa) && $archivo !== $nombre_base . '.pdf') {
                $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                $tipo = obtener_tipo_archivo($extension);
                
                $documentos[] = [
                    'directorio' => $nombre_base,
                    'archivo' => $archivo,
                    'ruta_completa' => $ruta_completa,
                    'fecha' => date('Y-m-d'),
                    'tipo' => $tipo,
                    'tamanio' => filesize($ruta_completa),
                    // 'tamanio_formateado' => formatear_tamanio(filesize($ruta_completa))
                ];
            }
        }
        
        if(!empty($documentos)) $this->contabilidad_model->crear('comprobantes_contables_validacion_detalle', $documentos);
    }

    /**
     * De un archivo en PDF extrae el texto buscado
     *
     * @param string $pdf_path
     * @param string $tipo
     * @param integer $pagina
     * @return void
     */
    function extraer_texto($pdf_path, $tipo, $pagina = 1) {
        // Verificar que el archivo existe
        if (!file_exists($pdf_path)) {
            echo "Archivo PDF no encontrado: $pdf_path";
            throw new Exception("Archivo PDF no encontrado: $pdf_path");
        }
        
        // Cargar PDF Parser si está disponible
        if (class_exists('Smalot\PdfParser\Parser')) {
            $parser = new Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdf_path);
            $paginas = $pdf->getPages();
            
            if ($pagina > count($paginas)) {
                echo "La página $pagina no existe";
                throw new Exception("La página $pagina no existe");
            }
            
            $pagina_actual = $paginas[$pagina - 1];
            
            // Obtener todo el texto de la página
            $texto_completo = $pagina_actual->getText();
            // echo "$texto_completo<hr>";

            // En PDF Parser no hay acceso directo a coordenadas del texto
            // Esta es una aproximación básica
            $lineas = explode("\n", $texto_completo);
            $texto_en_area = '';
            
            foreach ($lineas as $linea) {
                $patron = '/\d{3}-'.$tipo.'-\d{8}/';

                // Buscar el patrón específico que necesitas
                if (preg_match($patron, $linea, $coincidencias)) {
                    $texto_en_area = $coincidencias[0];
                    break;
                }
            }
            
            return $texto_en_area ?: 'Texto no encontrado';
        } else {
            throw new Exception("Se requiere PDF Parser: composer require smalot/pdfparser");
        }
    }

    /**
     * Toma el número de documento y lo formatea
     *
     * @param string $codigo
     * @param string $tipo
     * @return void
     */
    function formatear_codigo($codigo, $tipo) {
        // Primero quitar todos los guiones para limpiar
        $codigo_limpio = str_replace('-', '', $codigo);

        $patron = '/^(\d{3})('.$tipo.')(\d+)$/';

        // Patrón: 100FRC52378 -> 100-FRC-52378 (últimos 8 dígitos con ceros a la izquierda)
        if (preg_match($patron, $codigo_limpio, $coincidencias)) {
            // Rellenar con ceros a la izquierda para tener siempre 8 dígitos
            $numeroFormateado = str_pad($coincidencias[3], 8, '0', STR_PAD_LEFT);

            return $coincidencias[1] . '-' . $coincidencias[2] . '-' . $numeroFormateado;
        }

        // Devolver el código original si no coincide el patrón
        return $codigo;
    }

    function obtener_datos_tabla() {
        if (!$this->input->is_ajax_request()) redirect('inicio');

        $tipo = $this->input->get("tipo");
        $busqueda = $this->input->get("search")["value"];
        $indice = $this->input->get("start");
        $cantidad = $this->input->get("length");
        $columns = $this->input->get("columns");
        $order = $this->input->get("order");
        $ordenar = null;

        // Filtros personalizados de las columnas
        // $filtro_fecha_creacion = $this->input->get("filtro_fecha_creacion");

        // Si en la tabla se aplico un orden se obtiene el campo por el que se ordena
        if ($order) {
            $columna = $order[0]["column"];
            $orden = $order[0]["dir"];
            $campo = $columns[$columna]["data"];
            if ($campo) $ordenar = "$campo $orden";
        }

        switch ($tipo) {
            case "comprobantes_contables_validacion":
                // Se definen los filtros
                $datos = [
                    "contar" => true,
                    "busqueda" => $busqueda
                ];

                // Filtros personalizados
                // if(isset($filtro_fecha_creacion)) $datos['filtro_fecha_creacion'] = $filtro_fecha_creacion;

                // De acuerdo a los filtros se obtienen el número de registros filtrados
                $total_resultados = $this->contabilidad_model->obtener("comprobantes_contables_validacion", $datos);

                // Se quita campo para solo contar los registros
                unset($datos["contar"]);

                // Se agregan campos para limitar y ordenar
                $datos["indice"] = $indice;
                $datos["cantidad"] = $cantidad;
                if ($ordenar) $datos["ordenar"] = $ordenar;

                // Se obtienen los registros
                $resultados = $this->contabilidad_model->obtener("comprobantes_contables_validacion", $datos);

                print json_encode([
                    "draw" => $this->input->get("draw"),
                    "recordsTotal" => $total_resultados,
                    "recordsFiltered" => $total_resultados,
                    "data" => $resultados
                ]);
            break;
        }
    }

    /**
     * Procesa la carpeta y crea los registros en base de datos
     *
     * @param int $id_comprobante_tipo
     * @param int $anio
     * @param int $id_sede
     * @param int $mes
     * @return void
     */
    function procesar_comprobantes() {
        $datos = json_decode($this->input->post('datos'), true);

        $this->contabilidad_model->eliminar('comprobantes_contables_validacion', ['fecha' => date('Y-m-d')]);
        $this->contabilidad_model->eliminar('comprobantes_contables_validacion_detalle', ['fecha' => date('Y-m-d')]);

        // Obtenemos los datos necesarios
        $tipo_comprobante = $this->configuracion_model->obtener('comprobantes_contables_tipos', ['id' => $datos['id_comprobante_tipo']]);
        $sede = $this->configuracion_model->obtener('centros_operacion', ['id' => $datos['id_sede']]);
        $periodo = $this->configuracion_model->obtener('periodos', ['mes' => $datos['mes']]);

        $resultado = $this->procesar_directorio("$tipo_comprobante->ruta/{$datos['anio']}/$sede->ruta/$periodo->nombre_comprobante_contable", $tipo_comprobante->abreviatura);
    
        if(empty($resultado['documento_principal'])) {
            print json_encode([
                'error' => false,
                'resultado' => false,
                'mensaje' => 'No hay archivos por procesar. Por favor verifica que las carpetas tengan la estructura correcta e intenta nuevamente.',
            ]);
            return false;
        }
                  
        // Se almacena en la base de datos el registros todos los documentos procesados
        $cantidad_documentos_procesados = $this->contabilidad_model->crear('comprobantes_contables_validacion', $resultado['documento_principal']);
        
        print json_encode([
            'error' => false,
            'resultado' => true,
            'mensaje' => "$cantidad_documentos_procesados Documentos procesados exitosamente.",
        ]);
    }

    /**
     * Procesa el directorio indicado
     *
     * @param string $carpeta
     * @param string $tipo
     * @param integer $pagina
     * @return void
     */
    function procesar_directorio($carpeta, $tipo, $pagina = 1) {
        $carpeta_base = $this->directorio_raiz . $carpeta;

        // Verificar que la carpeta base existe
        if (!is_dir($carpeta_base)) {
            throw new Exception("Carpeta base no encontrada: $carpeta_base");
        }

        $resultado = [];
        $resultado_documentos_adicionales = [];
        
        // Escanear todas las subcarpetas
        $subcarpetas = scandir($carpeta_base);
        
        foreach ($subcarpetas as $subcarpeta) {
            if ($subcarpeta === '.' || $subcarpeta === '..') continue;
            // echo "$subcarpeta<hr>";
            
            $ruta_subcarpeta = $carpeta_base . '/' . $subcarpeta;

            $patron = '/^\d{3}'.$tipo.'\d+$/';

            // Verificar que es una carpeta y tiene el formato de código
            if (is_dir($ruta_subcarpeta) && preg_match($patron, str_replace('-', '', $subcarpeta))) {
                // echo "$subcarpeta<hr>";

                // Buscar documentos adicionales
                $documentos_adicionales = $this->buscar_documentos_adicionales($ruta_subcarpeta, $subcarpeta);

                if(!empty($documentos_adicionales)) array_push($resultado_documentos_adicionales, $documentos_adicionales);
                
                // Buscar el archivo PDF dentro de la subcarpeta
                $ruta_pdf = $ruta_subcarpeta . '/' . $subcarpeta . '.pdf';
                
                if (file_exists($ruta_pdf)) {
                    // echo "$ruta_pdf<hr>";

                    // Formatear el código: 100FRC52378 -> 100-FRC-00052378
                    $codigo_formateado = $this->formatear_codigo($subcarpeta, $tipo);
                    // echo "$codigo_formateado<hr>";
                    
                    // Buscar el código formateado en el PDF
                    $codigo_encontrado = $this->extraer_texto($ruta_pdf, $tipo, $pagina);
                    
                    $resultado[] = [
                        'directorio' => $subcarpeta,
                        'archivo' => $subcarpeta . '.pdf',
                        'fecha' => date('Y-m-d'),
                        'ruta_completa' => $ruta_pdf,
                        'codigo_formateado' => $codigo_formateado,
                        'codigo_encontrado' => $codigo_encontrado,
                        'validado' => ($codigo_formateado === $codigo_encontrado) ? 1 : 0,
                    ];
                }
            }
        }

        return [
            'documento_principal' => $resultado,
            'documentos_adicionales' => $resultado_documentos_adicionales
        ];
    }
}