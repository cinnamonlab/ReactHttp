<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/24/15
 * Time: 14:51
 */

namespace ReactHttp\Exception;
use Exception;


/**
 * Class ReactPHPException
 *
 * Exception class directly related to the ReactHttp Framework
 *
 * @package ReactHttp\Exception
 */

class ReactHttpException extends Exception
{

    const ERROR_CODE_LACK_PARAMETER = 400;
    const ERROR_CODE_NOT_AUTHENTICATED = 401;
    const ERROR_CODE_FORBIDDEN = 403;
    const ERROR_CODE_NOT_FOUND = 404;
    const ERROR_CODE_INTERNAL = 500;
    const ERROR_CODE_MAINTENANCE = 503;

    public function __construct($message = "", $code = 0)
    {
        parent::__construct($message, $code, null);
    }

    /**
     * Get NotFound errors catchable
     *
     * @param $entity_name
     * @return ReactHttpException
     */
    public static function notFound( $entity_name ) {
        return new ReactHttpException("$entity_name not found",
            ReactHttpException::ERROR_CODE_NOT_FOUND);
    }

    public static function lackParameter( $parameter_name ) {
        return new ReactHttpException("Request lacks parameter $parameter_name",
            ReactHttpException::ERROR_CODE_LACK_PARAMETER);
    }

    public static function internalError( $message ) {
        return new ReactHttpException($message,
            ReactHttpException::ERROR_CODE_INTERNAL);
    }

}