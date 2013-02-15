<?php
require "lessc.inc.php";
$file = $_GET["file"];
function autoCompileLess($inputFile, $outputFile) {
    $file_base = dirname(realpath($inputFile));
    $b = dirname(__FILE__).'/../';
    $base = realpath($b);
    if(strpos($file_base, $base) === false) return;
    $cacheFile = $outputFile.".cache";
    if (file_exists($cacheFile)) {
        $cache = unserialize(file_get_contents($cacheFile));
    } else {
        $cache = $inputFile;
    }
    $less = new lessc;
    $less->setFormatter("compressed");
    $import_dir = dirname($inputFile);
    $less->addImportDir($import_dir);
    $newCache = $less->cachedCompile($cache, true);
    if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {
        $dir = dirname($outputFile);
        if(file_exists($dir) == false){
            mkdir($dir,0777,true);
        }
        file_put_contents($cacheFile, serialize($newCache));
        chmod($cacheFile, 0666);
        file_put_contents($outputFile, $newCache['compiled']);
        chmod($outputFile, 0666);
    }
}
$input_file = $file;
chdir('../');
$output_file = dirname(__FILE__).'/cache/'.$file;
autoCompileLess($input_file, $output_file);
header('Content-type: text/css');
readfile($output_file);
?>