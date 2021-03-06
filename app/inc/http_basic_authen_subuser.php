<?php
if (!function_exists("makeExceptionReport")) {
    function makeExceptionReport($value)
    {
        ob_get_clean();
        ob_start();

        echo '<ServiceExceptionReport
	   version="1.2.0"
	   xmlns="http://www.opengis.net/ogc"
	   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	   xsi:schemaLocation="http://www.opengis.net/ogc http://wfs.plansystem.dk:80/geoserver/schemas//wfs/1.0.0/OGC-exception.xsd">
	   <ServiceException>';
        if (is_array($value)) {
            if (sizeof($value) == 1) {
                print $value[0];
            } else {
                print_r($value);
            }
        } else {
            print $value;
        }
        echo '</ServiceException>
	</ServiceExceptionReport>';
        $data = ob_get_clean();
        echo $data;
        die();
    }
}

if (sizeof($dbSplit) == 2) { //Sub-user
    $db = $dbSplit[1];
    // We set the SESSION, if request is coming from outside a session
    $subUser = $_SESSION["subuser"] = $dbSplit[0];
    if ($dbSplit[0] != $postgisschema) {
        $sql = "SELECT * FROM settings.geometry_columns_view WHERE _key_ LIKE :schema";
        $res = $postgisObject->prepare($sql);
        try {
            $res->execute(array("schema" => $postgisschema . "." . $HTTP_FORM_VARS["TYPENAME"] . ".%"));

        } catch (\PDOException $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            $response['code'] = 401;
            makeExceptionReport($response);
        }
        while ($row = $postgisObject->fetchRow($res, "assoc")) {
            $privileges = (array)json_decode($row["privileges"]);
            switch ($transaction) {
                case false:
                    if ($privileges[$subUser] == false || $privileges[$subUser] == "none") {
                        makeExceptionReport(array("You don't have privileges to see this layer. Please contact the database owner, which can grant you privileges."));
                    }
                    break;
                case true:
                    if ($privileges[$subUser] == false || $privileges[$subUser] == "none" || $privileges[$subUser] == "read") {
                        makeExceptionReport(array("You don't have privileges to edit this layer. Please contact the database owner, which can grant you privileges."));
                    }
                    break;
            }
        }
    }
}
