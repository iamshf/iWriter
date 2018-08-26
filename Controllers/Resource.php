<?php
namespace iWriter\Controllers {
    class Resource extends \MiniRest\Resource {
        protected function getJsonResult($code, $msg, $status_code = 200, $data = null) {
            $result = array('code' => $code, 'msg' => $msg);
            $this->_status = $status_code;
            if(!is_null($data)) {
                $result['data'] = $data;
            }

            return json_encode($result);
        }
    }
}
