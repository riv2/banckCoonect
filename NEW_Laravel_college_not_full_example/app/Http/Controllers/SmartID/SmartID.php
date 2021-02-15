<?php

require_once __DIR__ . '/php7SmartIdEngine.php';

$arr = [];

function OutputRecognitionResult($recog_result) {
  //printf("Document type: %s\n", $recog_result->GetDocumentType());
  $arr['doctype'] = $recog_result->GetDocumentType();
  
  //printf("String fields:<br>\n");
  $string_field_names = $recog_result->GetStringFieldNames();
  for ($i = 0; $i < $string_field_names->size(); $i++) {
    $field = $recog_result->GetStringField($string_field_names->get($i));
    //$is_accepted = $field->IsAccepted() ? " [+] " : " [-] ";
    $value = $field->GetUtf8Value(); //iconv("UTF-8", "CP437", $field->GetUtf8Value());
    
	$arrStr[$field->GetName()] = $value;
  }
  $arr['str'] = $arrStr;

  //printf("Image fields:\n");
  $image_field_names = $recog_result->GetImageFieldNames();
  for ($i = 0; $i < $image_field_names->size(); $i++) {
    $field = $recog_result->GetImageField($image_field_names->get($i));
    
    //$is_accepted = $field->IsAccepted() ? " [+] " : " [-] ";
    //printf("    %s\t%s W: %d H: %d\n", $field->GetName(), $is_accepted, $field->GetValue()->getWidth(), $field->GetValue()->getHeight());
    //$arrImg[$field->GetName()]['w'] = $field->GetValue()->getWidth();
    //$arrImg[$field->GetName()]['h'] = $field->GetValue()->getHeight();

    $outSize = $field->GetValue()->GetRequiredBase64BufferLength();
    $base64 = str_repeat(" ", $outSize); 
    $field->GetValue()->CopyBase64ToBuffer($base64,  $outSize);
    $arrImg[$field->GetName()] = $base64;

  }
  $arr['img'] = $arrImg;
  
  print json_encode($arr);

  //printf("Result terminal: %s\n", $recog_result->IsTerminal() ? " [+] " : " [-] ");
}

function main($argc, $argv) {
    if (empty($argc) || empty($argv)) {
        return null;
    }

  $image_path = $argv[1];
  $config_path = $argv[2];
  $document_types = $argv[3];

  try {
    $engine = new RecognitionEngine($config_path);

    $session_settings = $engine->CreateSessionSettings();

    

    // specify a concrete document type or wildcard mask
    $session_settings->AddEnabledDocumentTypes($document_types);

    $enabled_document_types = $session_settings->GetEnabledDocumentTypes();
    //printf("Spawning session with enabled document types: [ ");
    for ($d = 0; $d < $enabled_document_types->size(); $d++) {
      //printf("%s ", $enabled_document_types->get($d));
    }
    //printf("]<br>\n");

    @$session = $engine->SpawnSession($session_settings, $optional_reporter); 

    // Uses engine's internal image loading, supports format: png, jpg, jpeg, tif
    $result = $session->ProcessImageFile($image_path);

    OutputRecognitionResult($result);

  } catch (Exception $e) {
    printf("Exception caught: %s\n", $e->getMessage());
    exit(-2);
  }
}

$argc = $argc ?? null;
$argv = $argv ?? null;

main($argc, $argv);

?>