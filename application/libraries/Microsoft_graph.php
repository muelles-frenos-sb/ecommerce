<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Microsoft_graph {
    // Configuración de Microsoft Graph API
    private $client_id;
    private $client_secret;
    private $tenant_id;
    private $scope;
    private $email_usuario;

    public function __construct() {
        $this->ci =& get_instance();
        
        // Carga las credenciales desde config
        $this->ci->config->load('microsoft_graph', TRUE);
        $this->client_id = $this->ci->config->item('client_id', 'microsoft_graph');
        $this->client_secret = $this->ci->config->item('client_secret', 'microsoft_graph');
        $this->tenant_id = $this->ci->config->item('tenant_id', 'microsoft_graph');
        $this->scope = $this->ci->config->item('scope', 'microsoft_graph');
        $this->email_usuario = $this->ci->config->item('email_usuario', 'microsoft_graph');
    }

    /**
     * Obtiene el token de acceso de Microsoft Graph API
     */
    function obtener_token() {
        $url = "https://login.microsoftonline.com/{$this->tenant_id}/oauth2/v2.0/token";
        
        $datos = [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'client_credentials',
            'scope' => $this->scope
        ];

        return $this->enviar_peticion($url, $datos);
    }

    /**
     * Busca una carpeta por nombre
     */
    function buscar_carpeta($token, $nombre_carpeta) {
        // Si es "Inbox", usar directamente ese ID
        if(strtolower($nombre_carpeta) == 'inbox') return 'Inbox';

        $url = "https://graph.microsoft.com/v1.0/users/{$this->email_usuario}/mailFolders";

        $peticion = $this->enviar_peticion($url, null, $token);
        
        if($peticion['http_code'] == 200) {
            // Buscar la carpeta por nombre
            foreach($peticion['respuesta']['value'] as $carpeta) {
                if(strtolower($carpeta['displayName']) == strtolower($nombre_carpeta)) {
                    return $carpeta['id'];
                }
            }
        }
        
        return null;
    }

    /**
     * Envía correos electrónicos
     */
    function enviar_email($token, $datos) {
        $this->ci =& get_instance();
        $this->email_usuario = $this->ci->config->item('email_usuario_tienda', 'microsoft_graph');

        $url = "https://graph.microsoft.com/v1.0/users/{$this->email_usuario}/sendMail";

        return $this->enviar_peticion($url, json_encode($datos), $token);
    }

    function obtener_mensajes_con_adjuntos($token, $carpeta_id, $fecha_inicio = null, $fecha_fin = null) {
        $filtros = ["hasAttachments eq true"];

        if(!empty($fecha_inicio) && ($ts = strtotime($fecha_inicio)) !== false) {
            $filtros[] = "receivedDateTime ge " . date('Y-m-d', $ts) . "T00:00:00Z";
        }
        if(!empty($fecha_fin) && ($ts = strtotime($fecha_fin)) !== false) {
            $filtros[] = "receivedDateTime le " . date('Y-m-d', $ts) . "T23:59:59Z";
        }

        $filter_str = urlencode(implode(' and ', $filtros));
        $url = "https://graph.microsoft.com/v1.0/users/{$this->email_usuario}/mailFolders/{$carpeta_id}/messages";
        $url .= "?\$filter={$filter_str}&\$select=id,subject,hasAttachments&\$top=50";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$token}",
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code == 200) {
            $resultado = json_decode($response, true);
            return $resultado['value'] ?? [];
        }
        
        return [];
    }

    private function enviar_peticion($url, $datos = null, $token = null) {
        $peticion = curl_init();
        $peticion = curl_init();

        if($token) {
            curl_setopt($peticion, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $token",
                'Content-Type: application/json'
            ]);
        }
         
        if(!empty($datos)) {
            curl_setopt($peticion, CURLOPT_POST, true);
            curl_setopt($peticion, CURLOPT_POSTFIELDS, $datos);
        } 
        
        curl_setopt($peticion, CURLOPT_URL, $url);
        curl_setopt($peticion, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($peticion, CURLOPT_SSL_VERIFYPEER, false);
        
        $respuesta = curl_exec($peticion);
        $codigo_respuesta = curl_getinfo($peticion, CURLINFO_HTTP_CODE);
        $error = curl_error($peticion);
        
        curl_close($peticion);
        
        return [
            'status' => ($codigo_respuesta >= 200 && $codigo_respuesta < 300),
            'http_code' => $codigo_respuesta,
            'respuesta' => json_decode($respuesta, true),
            'error' => $error
        ];
    }

    /**
     * Lista las carpetas de correo disponibles desde Microsoft Graph
     */
    function listar_carpetas($token) {
        $carpetas = [];

        $url = "https://graph.microsoft.com/v1.0/users/{$this->email_usuario}/mailFolders?\$top=100";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: $token",
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        
        $resultado = json_decode($response, true);
        foreach($resultado['value'] as $carpeta) {
            $carpetas[] = [
                'id'   => $carpeta['id'],
                'nombre' => $carpeta['displayName'],
                'total' => $carpeta['totalItemCount'] ?? 0
            ];
        }
        

        return $carpetas;
    }

    /**
     * Obtiene mensajes con adjuntos de una carpeta
     */
    function obtener_mensajes($token, $carpeta_id) {
        $url = "https://graph.microsoft.com/v1.0/users/{$this->email_usuario}/mailFolders/{$carpeta_id}/messages";
        $url .= '?$top=500'; // Cantidad de registros
        // $url .= "&\$filter=hasAttachments%20eq%20true&\$select=id,subject,hasAttachments";
        $url .= '&$filter=isRead%20eq%20false';

        return $this->enviar_peticion($url, null, $token);
    }

    function descargar_adjuntos_mensaje($token, $mensaje_id, $carpeta_destino) {
        // Obtener los adjuntos del mensaje
        $url = "https://graph.microsoft.com/v1.0/users/{$this->email_usuario}/messages/{$mensaje_id}/attachments";        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$token}",
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($http_code != 200) {
            return ['exito' => false, 'error' => 'No se pudieron obtener los adjuntos', 'cantidad' => 0];
        }
        
        $resultado = json_decode($response, true);
        $adjuntos = $resultado['value'] ?? [];
        
        $archivos_guardados = 0;
        
        // Crear directorio si no existe
        $ruta_base = './archivos/correos/' . $carpeta_destino . '/';
        if(!is_dir($ruta_base)) {
            mkdir($ruta_base, 0777, true);
        }
        
        foreach($adjuntos as $adjunto) {
            if($adjunto['@odata.type'] == '#microsoft.graph.fileAttachment') {
                $nombre_archivo = $adjunto['name'];
                $contenido = base64_decode($adjunto['contentBytes']);
                
                // Extraer NIT, Nombre y Monto del nombre del archivo
                // Formato esperado: ALGO_QUE_CONTIENE_NIT_Nombre_Monto.ext
                $nombre_procesado = $this->procesar_nombre_archivo($nombre_archivo);
                
                // Guardar el archivo
                $ruta_completa = $ruta_base . $nombre_procesado;
                if(file_put_contents($ruta_completa, $contenido)) {
                    $archivos_guardados++;
                    
                    // Registrar en base de datos
                    /*$this->registrar_descarga([
                        'carpeta' => $carpeta_destino,
                        'nombre_original' => $nombre_archivo,
                        'nombre_procesado' => $nombre_procesado,
                        'ruta' => $ruta_completa,
                        'mensaje_id' => $mensaje_id,
                        'fecha_descarga' => date('Y-m-d H:i:s')
                    ]);*/
                }
            }
        }
        
        return ['exito' => true, 'cantidad' => $archivos_guardados];
    }

    private function procesar_nombre_archivo($nombre_original) {
        
        if(preg_match('/(\d+)_(.+?)_(\d+)\./', $nombre_original, $matches)) {
            return $nombre_original; // Ya tiene el formato correcto
        }
        
        // Si no, intentar extraer de metadatos o devolver con timestamp
        $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
        $timestamp = date('YmdHis');
        
        // Formato: TIMESTAMP_NombreOriginal
        return "{$timestamp}_{$nombre_original}";
    }

    /**
     * Descarga los adjuntos de un mensaje
     */
    function descargar_archivos_adjuntos($token, $mensaje_id, $ruta_archivo) {
        // Obtener los adjuntos del mensaje
        $url = "https://graph.microsoft.com/v1.0/users/{$this->email_usuario}/messages/{$mensaje_id}/attachments";        
        $peticion = $this->enviar_peticion($url, null, $token);
    
        $adjuntos = $peticion['respuesta']['value'];
        
        $archivos_guardados = 0;
        
        // // Crear directorio si no existe
        // if(!is_dir($ruta_base)) {
        //     mkdir($ruta_base, 0777, true);
        // }
        
        foreach($adjuntos as $adjunto) {
            // Si es un adjunto en PDF
            if($adjunto['@odata.type'] == '#microsoft.graph.fileAttachment' && $adjunto['@odata.mediaContentType'] == 'application/pdf') {
                $nombre_archivo = $adjunto['name'];
                $contenido = base64_decode($adjunto['contentBytes']);
                
        //         // Extraer NIT, Nombre y Monto del nombre del archivo
        //         // Formato esperado: ALGO_QUE_CONTIENE_NIT_Nombre_Monto.ext
        //         $nombre_procesado = $this->procesar_nombre_archivo($nombre_archivo);
                
                // Guardar el archivo
                if(file_put_contents($ruta_archivo, $contenido)) {
                    $archivos_guardados++;
                }
            }
        }
        
        return ['exito' => true, 'cantidad' => $archivos_guardados];
    }
}

