<?php
/**
 * Created by PhpStorm.
 * User: lmalicki
 * Date: 11/28/16
 * Time: 11:45 AM
 */

namespace CoreBundle\Handler;

use FOS\RestBundle\View\ExceptionWrapperHandlerInterface;

class ExceptionWrapperHandler implements ExceptionWrapperHandlerInterface {

    public function wrap($data)
    {
        $newException = array(
            'success' => false,
            'message' => $data['status_text'],
            'exception' => array()
        );

        return $newException;
    }
}