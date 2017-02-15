<?php
/**
 * Created by PhpStorm.
 * User: lmalicki
 * Date: 11/28/16
 * Time: 11:45 AM
 */

namespace CoreBundle\Handler;

use Symfony\Component\HttpFoundation\Request;

class RequestHandler
{

    public function handle(Request $request)
    {

        $data = $request->query->all();
        if (!isset($data['page']) || $data['page'] <= 0) {
            $page = 1;
        } else {
            $page = $data['page'];
        }

        if (!isset($data['limit']) || $data['limit'] <= 0) {
            $limit = 100;
        } else {
            $limit = $data['limit'];
        }

        $response = $data;

        $response['page'] = $page;
        $response['limit'] = $limit;

        return $response;
    }
}