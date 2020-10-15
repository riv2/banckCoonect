<?php
function dump_multi($vars,$die = false) {
    if (!is_array($vars)) {
        dump($vars);
    } else {
        foreach ($vars as $k => $v) {
            dump($v,$k);
        }
    }
    if($die) { die(); }
}

function dump_die($var, $info = FALSE) {
    dump($var, $info);
    die();
}

function dumpVar($var, $info = FALSE) {
    $scope = false;
    $prefix = 'unique';
    $suffix = 'value';

    if($scope) $vals = $scope;
    else $vals = $GLOBALS;

    $old = $var;
    $var = $new = $prefix.rand().$suffix; $vname = FALSE;
    foreach($vals as $key => $val) if($val === $new) $vname = $key;
    $var = $old;

    echo "<pre style='margin: 0px 0px 10px 0px; display: block; background: white; color: black; font-family: Verdana; border: 1px solid #cccccc; padding: 5px; font-size: 10px; line-height: 13px; text-align:left; width: 95%; float: left;'>";
    if($info != FALSE) echo "<b style='color: red;'>$info:</b><br>";
    do_dump($var, '$'.$vname);
    echo "</pre><div style='clear: both;'></div>";
}

function do_dump(&$var, $var_name = NULL, $indent = NULL, $reference = NULL) {
    $do_dump_indent = "<span style='color:#eeeeee;'>|</span> &nbsp;&nbsp; ";
    $reference = $reference.$var_name;
    $keyvar = 'the_do_dump_recursion_protection_scheme'; $keyname = 'referenced_object_name';

    if (is_array($var) && isset($var[$keyvar]))
    {
        $real_var = &$var[$keyvar];
        $real_name = &$var[$keyname];
        $type = ucfirst(gettype($real_var));
        echo "$indent$var_name <span style='color:#a2a2a2'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
    }
    else
    {
        $var = array($keyvar => $var, $keyname => $reference);
        $avar = &$var[$keyvar];

        $type = ucfirst(gettype($avar));
        if($type == "String") $type_color = "<span style='color:green'>";
        elseif($type == "Integer") $type_color = "<span style='color:red'>";
        elseif($type == "Double"){ $type_color = "<span style='color:#0099c5'>"; $type = "Float"; }
        elseif($type == "Boolean") $type_color = "<span style='color:#92008d'>";
        elseif($type == "NULL") $type_color = "<span style='color:black'>";

        if(is_array($avar))
        {
            $count = count($avar);
            echo "$indent" . ($var_name ? "$var_name => ":"") . "<span style='color:#a2a2a2'>$type ($count)</span><br>$indent(<br>";
            $keys = array_keys($avar);
            foreach($keys as $name)
            {
                $value = &$avar[$name];
                do_dump($value, "['$name']", $indent.$do_dump_indent, $reference);
            }
            echo "$indent)<br>";
        }
        elseif(is_object($avar))
        {
            echo "$indent$var_name <span style='color:#a2a2a2'>$type</span><br>$indent(<br>";
            foreach($avar as $name=>$value) do_dump($value, "$name", $indent.$do_dump_indent, $reference);
            echo "$indent)<br>";
        }
        elseif(is_int($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color$avar</span><br>";
        elseif(is_string($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color\"$avar\"</span><br>";
        elseif(is_float($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color$avar</span><br>";
        elseif(is_bool($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color".($avar == 1 ? "TRUE":"FALSE")."</span><br>";
        elseif(is_null($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> {$type_color}NULL</span><br>";
        else echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $avar<br>";

        $var = $var[$keyvar];
    }
}

function dumpRead($filename = 'dump.log') {

    $pathLog = '../storage/logs/';

    $fileArr = [];

    $file_handle = fopen($pathLog . $filename, "r");
    while (!feof($file_handle)) {
        $line = fgets($file_handle);
        $fileArr[] = $line;
    }
    fclose($file_handle);

    return $fileArr;

}

function dumpLog($var, $info = false, $filename = 'dump.log', $rewrite = false, $uid = null) {
    $pathLog = __DIR__ . '\..\storage\logs\\';

    $mode = $rewrite ? 'w' : 'a';

    if (empty($filename)) {
        $filename = 'dump.log';
    }
    $f = fopen($pathLog . $filename, $mode);

    if (!$f) {
        return;
    }

    if (!$info) {
        $info = '$';
    }
    $info = date('d-m-Y h:i:s').' '.$info;
    flock($f, LOCK_EX);
    formatDumpLog($f, $var, $info);
    fwrite($f, "\n\n");
    flock($f, LOCK_UN);
    fclose($f);
}

function formatDumpLog($f, &$var, $var_name = NULL, $indent = NULL, $reference = NULL) {
    $do_dump_indent = "  |  ";
    $reference = $reference.$var_name;
    $keyvar = 'the_do_dump_recursion_protection_scheme';
    $keyname = 'referenced_object_name';

    if (is_array($var) && isset($var[$keyvar])) {
        $real_var = &$var[$keyvar];
        $real_name = &$var[$keyname];
        $type = ucfirst(gettype($real_var));

        fwrite($f, "$indent$var_name [$type] = $real_name\n");
    } else {
        $var = array($keyvar => $var, $keyname => $reference);
        $avar = &$var[$keyvar];

        $type = gettype($avar);

        if($type == "Double"){
            $type = "Float";
        }

        if(is_array($avar)) {
            $count = count($avar);
            fwrite($f, "$indent" . ($var_name ? "$var_name => ":"") . "$type ($count)\n$indent(\n");
            $keys = array_keys($avar);
            foreach($keys as $name) {
                $value = &$avar[$name];
                formatDumpLog($f, $value, "['$name']", $indent.$do_dump_indent, $reference);
            }
            fwrite($f, "$indent)\n");
        } elseif(is_object($avar)) {
            fwrite($f, "$indent$var_name [$type]\n$indent(\n");
            foreach($avar as $name=>$value) {
                formatDumpLog($f, $value, "$name", $indent.$do_dump_indent, $reference);
            }
            fwrite($f, "$indent)\n");
        } elseif(is_int($avar)) {
            fwrite($f, "$indent$var_name = $type(".strlen($avar).") $avar\n");
        } elseif(is_string($avar)) {
            fwrite($f, "$indent$var_name = $type(".strlen($avar).") \"$avar\"\n");
        } elseif(is_float($avar)) {
            fwrite($f, "$indent$var_name = $type(".strlen($avar).") $avar\n");
        } elseif(is_bool($avar)) {
            fwrite($f, "$indent$var_name = $type(".strlen($avar).") " . ($avar == 1 ? "TRUE":"FALSE") . "\n");
        } elseif(is_null($avar)) {
            fwrite($f, "$indent$var_name = NULL\n");
        } else {
            fwrite($f, "$indent$var_name = $type(".strlen($avar).") $avar\n");
        }
        $var = $var[$keyvar];
    }
}