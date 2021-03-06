<?php
namespace app\api\v1;

class Baselayerjs extends \app\inc\Controller
{
    function __construct()
    {
        header("content-type: application/javascript");
        echo "window.gc2Options = {\n";
        echo "leafletDraw: " . ((\app\conf\App::$param['leafletDraw']) ? "true" : "false") . ",\n";
        echo "reverseLayerOrder: " . ((\app\conf\App::$param['reverseLayerOrder']) ? "true" : "false") . ",\n";
        echo "epsg: '" . ((\app\conf\App::$param['epsg']) ? : "4326") . "',\n";
        echo "extraShareFields: " . ((\app\conf\App::$param['extraShareFields']) ? "true": "false") . ",\n";
        echo "hideUngroupedLayers: " . ((\app\conf\App::$param['hideUngroupedLayers']) ? "true": "false") . ",\n";
        echo "staticMapHost: '" . ((\app\conf\App::$param['staticMapHost']) ? : \app\conf\App::$param['host']) . "',\n";
        echo "encoding: '" . ((\app\conf\App::$param['encoding']) ? : "UTF8") . "',\n";
        echo "osmConfig: " . json_encode(\app\conf\App::$param['osmConfig']) . ",\n";
        echo "mergeSchemata: " . json_encode(\app\conf\App::$param['mergeSchemata']) . ",\n";
        echo "hereApp: " . json_encode(\app\conf\App::$param['hereApp']);
        echo "};\n";
        if (\app\conf\App::$param['bingApiKey']) {
            echo "window.bingApiKey = '" . \app\conf\App::$param['bingApiKey'] . "';\n";
        }
        if (\app\conf\App::$param['digitalGlobeKey']) {
            echo "window.digitalGlobeKey = '" . \app\conf\App::$param['digitalGlobeKey'] . "';\n";
        }
        if (\app\conf\App::$param['baseLayers']) {
            echo "window.setBaseLayers = " . json_encode(\app\conf\App::$param['baseLayers']) . ";\n";
        }
        if (\app\conf\App::$param['mapAttribution']) {
            echo "window.mapAttribution = '" . \app\conf\App::$param['mapAttribution'] . "';\n";
        }

        $locales = array("en_US", "da_DK", "fr_FR", "es_ES", "it_IT");
        $arr = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        //echo $_SERVER['HTTP_ACCEPT_LANGUAGE'] . "\n";
        //echo $requestedLan."\n";
        $requestedLan = (\app\conf\App::$param['locale']) ? : str_replace("-", "_", $arr[0]);
        // Match both language and country
        if (in_array($requestedLan, $locales)) {
            echo "window.gc2Al='" . $requestedLan . "'\n";
            // Match only language
        } else {
            foreach ($locales as $locale) {
                if (substr($locale, 0, 2) == substr($requestedLan, 0, 2)) {
                    echo "window.gc2Al='" . $locale . "'\n";
                    exit();
                }
            }
            // Default
            echo "window.gc2Al='" . $locales[0] . "'\n";
        }
        exit();
    }

    public function get_index()
    {

    }
}
