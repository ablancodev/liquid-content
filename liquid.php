<?php
/**
 * Plugin Name: Liquid
 * Description: Liquid content with AI
 * Version: 1.0
 * Author: ablancodev
 * Author URI: https://ablancodev.com
 */
function liquid_shortcode($atts, $content = null) {

    // get param ref from url
    $origin = sanitize_text_field($_GET['ref']);
    switch ($origin) {
        case 'z':
            $origin = 'z';
            break;
        case 'x':
            $origin = 'x';
            break;
        case 'bb':
            $origin = 'baby boomers';
            break;
        default:
            $origin = null;
            break;
    }
    $modified_content = $content;
    if ( $origin != null) {
        $modified_content = liquid_get_content_version($content, $origin);
    }

    return '<p>' . $modified_content . '</p>';
}
add_shortcode('liquid', 'liquid_shortcode');

function liquid_get_content_version($content, $version) {
    $content_v2 = $content;

    // ChatGPT

    $url = 'https://api.openai.com/v1/chat/completions';

    // Pedimos sentimiento
    $curl = curl_init();
    $fields = array(
        'model' => 'gpt-3.5-turbo',
        "messages" => array(
            array("role" => "system", "content" => 'Actúa como un redactor de contenidos y darle un toque orientado al público de la generación ' . $version . ', pero de una longitud no mayor a 60 palabras. El contenido es: [' . $content_v2 . ']')
        ),
        'max_tokens' => 350,
        'temperature' => 0.2
    );
    $json_string = json_encode($fields);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer sk-xxxxxxxxxxxxxxxxxxxxxxxxxxx'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
    $data = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($data, true);
    $valor = $data['choices'][0]['message']['content'];

    $content_v2 = $valor;

    return $content_v2;
}