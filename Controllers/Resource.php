<?php
namespace iWriter\Controllers {
    class Resource extends \MiniRest\Resource {
        protected function getJsonResult($code, $msg, $data = null) {
            $result = array('code' => $code, 'msg' => $msg);
            if(!is_null($data)) {
                $result['data'] = $data;
            }

            return json_encode($result);
        }
    }
}
