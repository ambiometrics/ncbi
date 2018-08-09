<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 09-08-18
 * Time: 17:58
 */

namespace edwrodrig\ncbi\taxonomy\service;


class Controller
{
    public function __construct() {
        $json = json_decode(file_get_contents('php://input'));
    }



    public function process() {
            $params = json_decode(file_get_contents('php://input'));
        $params = $_GET;

        $args = [];

        if ( !isset($params["a"]) ) {
            throw new \Exception('UNDEFINED_ARGUMENT');
        } else {
            $param = $params['a'];
        }

        $args[] = $param;


        if ( !isset($params["b"]) ) {
            throw new \Exception('UNDEFINED_ARGUMENT');
        } else {
            $param = $params['b'];
        }

        $args[] = $param;


        $return = \minimum(...$args);
        $return = [
            'status' => 0,
            'data' => $return
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($return);

    } catch ( \Exception $e ) {

$return = [
'status' => -1,
'message' => $e->getMessage()
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($return);
}
    }
}