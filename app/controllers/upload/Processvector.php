<?php
namespace app\controllers\upload;

use \app\conf\App;
use \app\inc\Response;
use \app\conf\Connection;
use \app\inc\Session;

class Processvector extends \app\inc\Controller
{
    public function get_index()
    {
        $dir = App::$param['path'] . "app/tmp/" . Connection::$param["postgisdb"] . "/__vectors";
        $safeName = \app\inc\Model::toAscii($_REQUEST['name'], array(), "_");

        if (is_numeric($safeName[0])) {
            $safeName = "_" . $safeName;
        }

        //Check if file is .zip
        $zipCheck1 = explode(".", $_REQUEST['file']);
        $zipCheck2 = array_reverse($zipCheck1);

        if (strtolower($zipCheck2[0]) == "zip" || strtolower($zipCheck2[0]) == "rar") {
            $ext = array("shp", "tab", "geojson", "gml", "kml", "mif", "gdb");
            $folderArr = array();
            $safeNameArr = array();
            for ($i = 0; $i < sizeof($zipCheck1) - 1; $i++) {
                $folderArr[] = $zipCheck1[$i];
            }
            $folder = implode(".", $folderArr);

            if (strtolower($zipCheck2[0]) == "zip") {
                // ZIP start
                $zip = new \ZipArchive;
                $res = $zip->open($dir . "/" . $_REQUEST['file']);
                if ($res === false) {
                    $response['success'] = false;
                    $response['message'] = "Could not unzip file";
                    return Response::json($response);
                }
                $zip->extractTo($dir . "/" . $folder);
                $zip->close();
                // ZIP end
            }

            if (strtolower($zipCheck2[0]) == "rar") {
                // RAR start
                $rar_file = rar_open($dir . "/" . $_REQUEST['file']);
                if (!$rar_file) {
                    $response['success'] = false;
                    $response['message'] = "Could not unrar file";
                    return Response::json($response);
                }

                $list = rar_list($rar_file);
                foreach ($list as $file) {
                    $entry = rar_entry_get($rar_file, $file);
                    $file->extract($dir . "/" . $folder); // extract to the current dir
                }
                rar_close($rar_file);
                // RAR end
            }
            if ($handle = opendir($dir . "/" . $folder)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry !== "." && $entry !== "..") {
                        $zipCheck1 = explode(".", $entry);
                        $zipCheck2 = array_reverse($zipCheck1);
                        if (in_array(strtolower($zipCheck2[0]), $ext)) {
                            $_REQUEST['file'] = $folder . "/" . $entry;
                            for ($i = 0; $i < sizeof($zipCheck1) - 1; $i++) {
                                $safeNameArr[] = $zipCheck1[$i];
                            }
                            $safeName = \app\inc\Model::toAscii(implode(".", $safeNameArr), array(), "_");
                            break;
                        }
                        $_REQUEST['file'] = $folder;
                    }
                }
            }
        }
        $srid = ($_REQUEST['srid']) ? : "4326";
        $encoding = ($_REQUEST['encoding']) ? : "LATIN1";

        switch ($_REQUEST['type']) {
            case "Point":
                $type = "point";
                break;
            case "Polygon":
                $type = "multipolygon";
                break;
            case "Line":
                $type = "multilinestring";
                break;
            case "Geometry":
                $type = "geometry";
                break;
            default:
                $type = "PROMOTE_TO_MULTI";
                break;
        }
        $cmd = "PGCLIENTENCODING={$encoding} ogr2ogr " .
            "-overwrite " .
            "-dim 2 " .
            "-lco 'GEOMETRY_NAME=the_geom' " .
            "-lco 'FID=gid' " .
            "-lco 'PRECISION=NO' " .
            "-a_srs 'EPSG:{$srid}' " .
            "-f 'PostgreSQL' PG:'host=" . Connection::$param["postgishost"] . " user=" . Connection::$param["postgisuser"] . " dbname=" . Connection::$param["postgisdb"] . " active_schema=" . Connection::$param["postgisschema"] . "' " .
            "'" . $dir . "/" . $_REQUEST['file'] . "' " .
            "-nln {$safeName} " .
            "-nlt {$type}";

        exec($cmd . ' 2>&1', $out, $err);

        $model = new \app\inc\Model();
        $geoType = $model->getGeometryColumns(Connection::$param["postgisschema"] . "." . $safeName, "type");
        $key = Connection::$param["postgisschema"] . "." . $safeName . ".the_geom";
        $class = new \app\models\Classification($key);
        $arr = $class->getAll();

        // Set layer editable
        $join = new \app\models\Table("settings.geometry_columns_join");
        $json = '{"data":{"editable":true,"_key_":"' . $key . '"}}';
        $data = (array)json_decode(urldecode($json));
        $join->updateRecord($data, "_key_");

        if (empty($arr['data'])) {
            $class->insert();
            $class->update("0", \app\models\Classification::createClass($geoType));
        }

        $def = new \app\models\Tile($key);
        $arr = $def->get();
        if (empty($arr['data'][0])) {
            $json = '{
            "theme_column":"",
            "label_column":"",
            "query_buffer":"",
            "opacity":"",
            "label_max_scale":"",
            "label_min_scale":"",
            "meta_tiles":false,
            "meta_size":"3",
            "meta_buffer":"10",
            "ttl":""}';
            $def->update($json);
        }

        if ($out[0] == "") {
            $response['success'] = true;
            $response['message'] = "Layer <b>{$safeName}</b> is created";
            $response['type'] = $geoType;
        } else {

            $response['success'] = false;
            $response['message'] = "Some thing went wrong. Check the log.";
            Session::createLog($out, $_REQUEST['file']);
        }
        $response['cmd'] = $cmd;
        return Response::json($response);
    }
}