<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/24/15
 * Time: 17:16
 */

namespace ReactHttp;


use Exception;
use React\Http\Request;
use ReactHttp\Exception\ReactHttpException;
use ReactHttp\Validator\Validator;

class HttpRequest
{
    /**
     * @var Request
     */
    private $request;

    private $parameters = array();

    /**
     * HttpRequest constructor.
     * @param Request $request
     */
    public function __construct(Request $request,$jsonData)
    {
        $this->request = $request;

        $this->parameters = $request->getQuery();
        $this->parameters = array_merge($this->parameters,$request->getHeaders());
        $this->parameters = array_merge($this->parameters,(array)$jsonData);

    }



    /**
     * @param $key
     * @param string $default
     * @param Validator|null $validator
     * @return mixed
     * @throws ReactHttpException
     */
    public function get($key, $default = "", $validator = null)
    {

        if (isset($this->parameters[$key]) ) {
            $value = $this->parameters[$key];
        } else if ( isset($_REQUEST[$key]) ) {
            $value = $_REQUEST[$key];
        } else {
            $value = $this->processDefaultValue($default);
        }

        if ($validator instanceof Validator) {
            if ( $validator->execute($value) ) {
                return $value;
            } else {
                return $this->processDefaultValue($default);
            }

        } else if (is_callable($validator)) {
            try {
                if ($validator($value) ) {
                    return $value;
                } else {
                    return $this->processDefaultValue($default);
                }
            } catch (Exception $e) {
                throw ReactHttpException::internalError("Validator error");
            }
        }
        return $value;
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key) {
        if ( isset($this->parameters[$key])) return true;
        return false;
    }
    /**
     * Set Value Manually
     *
     * @param $key
     * @param $value
     */
    public function set( $key, $value ) {
        $this->parameters[$key] = $value;
    }
    private function processDefaultValue( $default ) {
        if ( $default instanceof ReactHttpException ) {
            throw $default;
        } else if ( is_callable( $default ) ) {
            return $default();
        }
        return $default;
    }

    public function getAll() {
        return $this->parameters;
    }

    public function getMethod() {
        return $this->request->getMethod();
    }

}