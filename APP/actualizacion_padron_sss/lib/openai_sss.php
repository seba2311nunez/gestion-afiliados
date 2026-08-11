<?php
include_once(__DIR__.'/../../../Config/openai.inc.php');

function sss_openai_texto_respuesta($respuesta){
    $fragmentos=array();
    if(!isset($respuesta['output']) || !is_array($respuesta['output'])) return '';
    foreach($respuesta['output'] as $item){
        if(!isset($item['content']) || !is_array($item['content'])) continue;
        foreach($item['content'] as $contenido){
            if(isset($contenido['type'],$contenido['text']) && $contenido['type']==='output_text') $fragmentos[]=$contenido['text'];
        }
    }
    return trim(implode("\n",$fragmentos));
}

function sss_openai_responder($pregunta,$contexto=array()){
    $config=openai_private_config();
    $promptPath=dirname(__DIR__).DIRECTORY_SEPARATOR.'prompts'.DIRECTORY_SEPARATOR.'asistente_actualizacion_padron_sss.md';
    if(!is_file($promptPath)) throw new Exception('No se encontro el prompt del asistente SSS.');
    $payload=array(
        'model'=>$config['model'],
        'instructions'=>file_get_contents($promptPath),
        'input'=>"CONTEXTO JSON:\n".json_encode($contexto)."\n\nPREGUNTA DEL USUARIO:\n".$pregunta,
        'max_output_tokens'=>isset($config['max_output_tokens']) ? intval($config['max_output_tokens']) : 1200
    );
    $curl=curl_init('https://api.openai.com/v1/responses');
    curl_setopt($curl,CURLOPT_POST,true);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,20);
    curl_setopt($curl,CURLOPT_TIMEOUT,120);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,true);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,2);
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Authorization: Bearer '.$config['api_key'],'Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($payload));
    $cruda=curl_exec($curl); $http=intval(curl_getinfo($curl,CURLINFO_HTTP_CODE)); $error=curl_error($curl); curl_close($curl);
    if($cruda===false) throw new Exception('Error de conexion con OpenAI: '.$error);
    $respuesta=json_decode($cruda,true);
    if(!is_array($respuesta)) throw new Exception('OpenAI devolvio una respuesta no valida.');
    if($http<200 || $http>=300){
        $mensaje=isset($respuesta['error']['message']) ? $respuesta['error']['message'] : 'HTTP '.$http;
        throw new Exception('OpenAI: '.$mensaje);
    }
    $texto=sss_openai_texto_respuesta($respuesta);
    if($texto==='') throw new Exception('OpenAI no devolvio texto.');
    return array('respuesta'=>$texto,'modelo'=>$config['model']);
}
?>
