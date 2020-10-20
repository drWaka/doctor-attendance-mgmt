<?php

class SystemFeatures {
    public static function index() {
        return [];   
    }


    public static function create() {
        return [];   
    }

    public static function show() {
        return [];   
    }

    public static function update() {
        return [];   
    }

    public static function delete() {
        return [];   
    }

    public static function filter($filter) {
        $where = "";
        if (isset($filter['PK_system_features'])) {
            $where .= (strlen($where) > 0) ? "AND" : "WHERE";
            $where .= " PK_system_features = '{$filter['PK_system_features']}'";
        }

        $query = "SELECT * FROM system_features {$where}";
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];   
    }

    public static function isFeatureEnabled($featureId) {
        $featureRec = self::filter(array(
            "PK_system_features" => $featureId
        ));
        if (count($featureRec)) {
            return boolval($featureRec[0]['value']);
        }
        return false;   
    }
}