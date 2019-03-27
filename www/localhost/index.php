<?php

require 'vendor/autoload.php';
$logger = new \Katzgrau\KLogger\Logger("./logs");
$url = $_POST['url'];
$dir = dirname($url);
$pdf = str_replace("docx","pdf",$url);
$wordFile = str_replace('\\','/',realpath($url));
$dirName = dirname($wordFile);
$pdfName = date('Ymd') . md5(substr(microtime(), 2, 6)).".pdf";
$realPdf = $dirName.'/'.$pdfName;
if (PATH_SEPARATOR != ';') {
    $cmd = "export HOME=/var/wwwroot/ && unoconv -f pdf -o ".$pdf." ".$url." && echo \"success\"";
    exec($cmd, $result);
    if ($result[0] == "success") {
        echo json_encode(['status' => 1, 'msg' => $pdf]);
    } else {
        echo json_encode(['status' => 0, 'msg' => "无法完成转换"]);
    }
}else{

    try {
        $word = new \COM("Word.Application") or die ("Could not initialise Object.");
        $word->Documents->Open($wordFile);
        $word->ActiveDocument->ExportAsFixedFormat($realPdf, 17, false, 1, 0, 0, 0, 0, false, false, 0, false, false, false);
        $word->Quit(false);
    }catch(\Exception $exception){
        echo '有错误信息';
        $logger->error($exception->getLine());
        $logger->error($exception->getCode());
        $logger->error($exception->getMessage());
        die;
    }
    echo json_encode(['status' => 1, 'msg' => $dir.'/'.$pdfName]);
}

