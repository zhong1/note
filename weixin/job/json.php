<?php
    
    $json_errno = 0;
    $json_error = "OK";

    function jsonErrno() {
        global $json_errno;
        $errno = $json_errno;
        $json_errno = 0;
        return $errno;
    }

    function jsonError() {
        global $json_error;
        return $json_error;
    }

    function setJsonErrorInfo($errno, $error) {
        global $json_errno;
        global $json_error;

        $json_errno = $errno;
        $json_error = $error;
    }

    function stringToObj($data, $toBeArr=false) {
        $obj = json_decode($data, $toBeArr);
        if (is_null($obj)) {
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    setJsonErrorInfo(-90001, "No errors");
                    break;
                case JSON_ERROR_DEPTH:
                    setJsonErrorInfo(-90001, "Maximum stack depth exceeded");
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    setJsonErrorInfo(-90001, "Underflow or the modes mismatch");
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    setJsonErrorInfo(-90001, "Unexpected control character found");
                    break;
                case JSON_ERROR_SYNTAX:
                    setJsonErrorInfo(-90001, "Syntax error, malformed JSON");
                    break;
                case JSON_ERROR_UTF8:
                    setJsonErrorInfo(-90001, "Malformed UTF-8 characters, possibly incorrectly encoded");
                    break;
            }
        }
        return $obj;
    }
?>
