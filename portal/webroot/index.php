<?php

require_once('../config.php');
require_once(ENGINE_ROOT.'Engine.php');
require_once(USER_DEFINED_CLASSES.'PageBuilderExt.php');
//require_once(USER_DEFINED_MODULE_CLASSES.'ModuleNAFExt.php');
require_once(USER_DEFINED_MODULE_CLASSES.'ModuleCatalogueExt.php');

/*$fp = fopen("d:/log.txt", "a");

fwrite($fp, "\n\n Start session at ".date("H:i:s")."\n");
fwrite($fp, print_r($_REQUEST, true));

fclose($fp);*/

$engine     = new Engine();
//$page       = new PageBuilderExt();
//$naf        = new ModuleNAFExt();
$cat        = new ModuleCatalogueExt();

/** @todo PageBuilder should bbe redeclared before Modules */
//$engine->setExtendedPageHandler($page);
//$engine->setExtendedModuleNAFHandler($naf);
$engine->setExtendedModuleCatalogueHandler($cat);

$engine->RenderPage();

/* include FirePHP code */
if (ENABLE_FIREPHP){
    require_once(FIREPHP_ROOT.'fb.php');
    ob_start();

    fb('Hello World');

    fb('Log message'  ,FirePHP::LOG);
    fb('Info message' ,FirePHP::INFO);
    fb('Warn message' ,FirePHP::WARN);
    fb('Error message',FirePHP::ERROR);

    fb($_SERVER, 'Server Variables', FirePHP::LOG);

    fb(array('Table name', /* The summary line */
            array( /* Contains each table row */
                array('first column','second column','third column'),
                array('row 1','val 1',array('a' => 'sub array 1, val 1','b' => 'sub array 1, val 2')),
                array('row 2','val 2',array('a' => 'sub array 2, val 1','b' => 'sub array 2, val 2')),
                array('row 3','val 3',array('a' => 'sub array 3, val 1','b' => 'sub array 3, val 2'))
            )
        ),FirePHP::TABLE);
}
?>