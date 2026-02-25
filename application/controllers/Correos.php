<?php
date_default_timezone_set('America/Bogota');

defined('BASEPATH') OR exit('El acceso directo a este archivo no está permitido');

class Correos extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('microsoft_graph');
        
        
        $this->load->model('correos_model'); 
    }

    function index() {
        $this->data['contenido_principal'] = 'correos/descargar/index';
        $this->load->view('core/body', $this->data);
    }

    function descargar() {
        switch ($this->uri->segment(3)) {
            case 'ver':
                if(!$this->session->userdata('usuario_id')) redirect('inicio');
                
                $this->data['contenido_principal'] = 'correos/descargar/index';
                $this->load->view('core/body', $this->data);
                break;
        }
    }

    /**
     * Procesa la descarga usando la LIBRERÍA
     */
    function procesar() {
        if(!$this->session->userdata('usuario_id')) {
            echo json_encode(['error' => true, 'mensaje' => 'Sesión no válida']);
            return;
        }

        $datos = json_decode($this->input->post('datos'), true);
        
        if(empty($datos['nombre_carpeta'])) {
            echo json_encode(['error' => true, 'mensaje' => 'Debe proporcionar el nombre de la carpeta']);
            return;
        }

        try {
            // 2. Uso de la librería para obtener el token
            $token = $this->microsoft_graph->obtener_token(); 
            
            if(!$token) {
                echo json_encode(['error' => true, 'mensaje' => 'No se pudo obtener el token']);
                return;
            }

            // 3. Uso de la librería para buscar la carpeta
            $carpeta_id = $this->microsoft_graph->buscar_carpeta($token['respuesta']['access_token'], $datos['nombre_carpeta']);
            
            if(!$carpeta_id) {
                echo json_encode(['error' => true, 'mensaje' => 'No se encontró la carpeta']);
                return;
            }

            // 4. Obtener mensajes mediante la librería
            $mensajes = $this->microsoft_graph->obtener_mensajes_con_adjuntos($token['respuesta']['access_token'], $carpeta_id);
            
            if(empty($mensajes)) {
                echo json_encode([
                    'error' => false, 
                    'mensaje' => 'No hay mensajes con adjuntos',
                    'archivos_descargados' => 0
                ]);
                return;
            }

            $archivos_descargados = 0;
            $errores = [];
            
            foreach($mensajes as $mensaje) {
                // 5. Descarga de adjuntos mediante la librería
                $resultado = $this->microsoft_graph->descargar_adjuntos_mensaje(
                    $token['respuesta']['access_token'], 
                    $mensaje['id'],
                    $datos['nombre_carpeta']
                );
                
                if($resultado['exito']) {
                    $archivos_descargados += $resultado['cantidad'];
                } else {
                    $errores[] = $resultado['error'];
                }
            }

            echo json_encode([
                'error' => false,
                'mensaje' => "Se descargaron {$archivos_descargados} archivos correctamente",
                'archivos_descargados' => $archivos_descargados,
                'errores' => $errores
            ]);

        } catch(Exception $e) {
            echo json_encode(['error' => true, 'mensaje' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
 * Lista los archivos leyendo directamente el sistema de archivos (sin BD)
 */
    function listar() {
        if(!$this->session->userdata('usuario_id')) {
            $this->data['archivos'] = [];
        } else {
            $ruta_base = './archivos/correos/';
            $lista_final = [];

            // Verificar si la carpeta existe
            if (is_dir($ruta_base)) {
                // Escaneamos las subcarpetas (que son los nombres de las carpetas de correo)
                $subcarpetas = array_diff(scandir($ruta_base), array('..', '.'));

                foreach ($subcarpetas as $carpeta) {
                    $ruta_subcarpeta = $ruta_base . $carpeta;
                    
                    if (is_dir($ruta_subcarpeta)) {
                        $archivos = array_diff(scandir($ruta_subcarpeta), array('..', '.'));
                        
                        foreach ($archivos as $archivo) {
                            $path_completo = $ruta_subcarpeta . '/' . $archivo;
                            
                            // Obtenemos info del archivo
                            $lista_final[] = [
                                'carpeta' => $carpeta,
                                'nombre_procesado' => $archivo,
                                'fecha_descarga' => date("Y-m-d H:i:s", filemtime($path_completo)),
                                'ruta' => 'archivos/correos/' . $carpeta . '/' . $archivo
                            ];
                        }
                    }
                }
            }

            // Ordenar por fecha de descarga (más reciente primero)
            usort($lista_final, function($a, $b) {
                return strtotime($b['fecha_descarga']) - strtotime($a['fecha_descarga']);
            });

            $this->data['archivos'] = $lista_final;
        }
        
        $this->load->view('correos/descargar/listar', $this->data);
    }

    function obtener_carpetas() {
        if(!$this->session->userdata('usuario_id')) {
            echo json_encode(['error' => true, 'carpetas' => []]);
            return;
        }

        // Uso de la librería
        $token = $this->microsoft_graph->obtener_token();
        if(!$token) {
            echo json_encode(['error' => true, 'mensaje' => 'Error de token', 'carpetas' => []]);
            return;
        }
        $carpetas = $this->microsoft_graph->listar_carpetas($token['respuesta']['access_token']);
        echo json_encode(['error' => false, 'carpetas' => $carpetas]);
    }
}