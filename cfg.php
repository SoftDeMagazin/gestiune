<?php 
require_once (DOC_ROOT."common/user_profile.php");
//error_reporting(0);
session_start();
header("Cache-control: no-cache");

require_once (DOC_ROOT."setari/gui.php");
require_once (DOC_ROOT."setari/factura.php");
require_once (DOC_ROOT."setari/app.php");
require_once (DOC_ROOT."setari/nomenclator.php");
require_once (DOC_ROOT."setari/path.php");
require_once (DOC_ROOT."setari/pdf.php");
require_once (DOC_ROOT."setari/transferuri.php");
require_once (DOC_ROOT."setari/db.php");

require_once (DOC_ROOT."app/include/db/mysqli.php");
require_once (DOC_ROOT."app/include/helpers/helpers.all.php");
require_once (DOC_ROOT."app/include/db/data_source.php");
require_once (DOC_ROOT."app/include/db/model.php");
require_once (DOC_ROOT."app/include/db/proc.php");



require_once (DOC_ROOT."app/thirdparty/xajax/xajax_core/xajax.inc.php");

$db = new MySQL();

$modelList = array();
if ($handle = opendir(DOC_ROOT.'app/include/models'))
{
    while (false !== ($file = readdir($handle)))
    {
        if ($file != "model.php" && $file != '.' && $file != '..')
            if (is_file(DOC_ROOT."app/include/models/".$file))
            {
                if (IS_DEBUG)
                    require_once (DOC_ROOT."app/include/models/".$file);
                $file_part = explode(".", $file);
                $modelList[] = $file_part[0];
            }
    }
}

$viewsList = array();
if ($handle = opendir(DOC_ROOT.'app/include/views'))
{
    while (false !== ($file = readdir($handle)))
    {
        if ($file != "model.php" && $file != '.' && $file != '..')
            if (is_file(DOC_ROOT."app/include/views/".$file))
            {
                if (IS_DEBUG)
                    require_once (DOC_ROOT."app/include/views/".$file);
                $file_part = explode(".", $file);
                $viewsList[] = $file_part[0];
                
            }
    }
}

$classesList = array();
if ($handle = opendir(DOC_ROOT.'app/include/classes'))
{
    while (false !== ($file = readdir($handle)))
    {
        if ($file != "model.php" && $file != '.' && $file != '..')
            if (is_file(DOC_ROOT."app/include/classes/".$file))
            {
                if (IS_DEBUG)
                    require_once (DOC_ROOT."app/include/classes/".$file);
                $file_part = explode(".", $file);
                $classesList[] = $file_part[0];
                
            }
    }
}

$rptList = array();
if ($handle = opendir(DOC_ROOT.'app/include/rapoarte'))
{
    while (false !== ($file = readdir($handle)))
    {
        if ($file != "model.php" && $file != '.' && $file != '..')
            if (is_file(DOC_ROOT."app/include/rapoarte/".$file))
            {
                if (IS_DEBUG)
                    require_once (DOC_ROOT."app/include/rapoarte/".$file);
                $file_part = explode(".", $file);
                $rptList[] = $file_part[0];
                
            }
    }
}

$printList = array();
if ($handle = opendir(DOC_ROOT.'app/include/print'))
{
    while (false !== ($file = readdir($handle)))
    {
        if ($file != "model.php" && $file != '.' && $file != '..')
            if (is_file(DOC_ROOT."app/include/print/".$file))
            {
                if (IS_DEBUG)
                    require_once (DOC_ROOT."app/include/print/".$file);
                $file_part = explode(".", $file);
                $printList[] = $file_part[0];
                
            }
    }
}

function __autoload($class_name)
{
    global $modelList;
    global $viewsList;
    global $classesList;
    global $rptList;
    global $printList;
    if (in_array(camelCaseToUnderline($class_name), $modelList))
    {
        require_once (DOC_ROOT."app/include/models/".camelCaseToUnderline($class_name).".php");
    }
    if (in_array(camelCaseToUnderline($class_name), $viewsList))
    {
        require_once (DOC_ROOT."app/include/views/".camelCaseToUnderline($class_name).".php");
    }
    if (in_array(camelCaseToUnderline($class_name), $classesList))
    {
        require_once (DOC_ROOT."app/include/classes/".camelCaseToUnderline($class_name).".php");
    }
    if (in_array(camelCaseToUnderline($class_name), $rptList))
    {
        require_once (DOC_ROOT."app/include/rapoarte/".camelCaseToUnderline($class_name).".php");
    }
    if (in_array(camelCaseToUnderline($class_name), $printList))
    {
        require_once (DOC_ROOT."app/include/print/".camelCaseToUnderline($class_name).".php");
    }
}
?>
