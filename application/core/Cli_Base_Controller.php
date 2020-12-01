<?php

use Common\Helper\ResponseHeader;
use AccountService\VersionControl\VersionControlService;

class Cli_Base_Controller extends System_Base_Controller {

    function __construct() {
        parent::__construct();
    }

    protected function _authoriseClient() {
        if ($this->checkIsCli()) {

            $app_id = $this->getArgument(0);
            $version = NULL;

            $this->load->model('versioncontrol/Version_control_model');
            $ver_serv = new VersionControlService($this->Version_control_model);

            list($stat, $result) = $ver_serv->authorizeClient($app_id, $version);
            if ($stat === true) {
                if (isset($result['token']))
                    $this->clientToken = $result['token'];

                return true;
            } else {
                $this->_respondWithCode($ver_serv->getResponseCode(), ResponseHeader::HEADER_NOT_FOUND, array('result' => $result));
                $this->_respondAndTerminate();
                return false;
            }
        }

        return false;
    }

    public function checkIsCli() {
        if (!is_cli()) {
            $this->response_message->getHeader()->setStatus(ResponseHeader::HEADER_UNAUTHORIZED);
            $this->response_message->setStatusCode(ResponseHeader::HEADER_UNAUTHORIZED);
            $this->response_message->setMessage('Client authentication failed');
            $this->_respondAndTerminate();
            return false;
        }

        return true;
    }

    public function getArgument($n) {
        $arr = $this->getArguments();

        if (isset($arr[$n]))
            return $arr[$n];

        return false;
    }

    public function getArguments() {
        $arr = array();

        $args = $_SERVER['argv'];

        $n = 0;
        foreach ($args as $arg) {
            if ($n > 3) {//only take the 4th element onward
                $arr[] = $arg;
            }

            $n = $n + 1;
        }

        return $arr;
    }

}
