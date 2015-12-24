<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 12/24/15
 * Time: 15:02
 */

namespace ReactHttp;


use React\Http\Response;

class HttpResponse
{
    /**
     * @var \React\Http\Response
     */
    private $response;

    private $headers = array();

    private $content='';
    private $code = 200;

    public function __construct(Response $response)
    {
        $this->response = $response;

        $this->setContentType("text/html");
    }
    /**
     * set Content for header
     *
     * @param $content
     * @return $this
     */
    function setContent( $content ) {
        $this->content=$content;
        return $this;
    }

    /**
     * Set HTTP code and put on header
     *
     * @param $code
     * @return $this
     */
    function setCode( $code ) {
        $this->code = $code;
        $this->addHeader($this->getDefaultHeaderFromCode( $code ));
        return $this;
    }

    /**
     * Get HTTP code;
     *
     * @return code
     */

    function getCode() {
        return $this->code;
    }

    /**
     * Get registered Header
     *
     * @return array of header information
     */
    function getHeaders() {
        return $this->headers;
    }

    /**
     * Get Content
     *
     * @return string content
     */
    function getContent() {
        return $this->content;
    }
    /**
     * Add Header
     *
     * @param $header
     * @return $this
     */
    function addHeader($header) {
        array_push( $this->headers, $header);
        return $this;
    }

    /**
     * Set Content Type
     * @param $content_type
     * @return $this
     */
    function setContentType( $content_type ) {
        $this->addHeader('Content-type: ' . $content_type);
        return $this;
    }

    /**
     * Set Display
     */

    function display( ) {
        $size = strlen($this->content);
        $this->addHeader('Content-length: ' . $size);

        foreach ( $this->headers as $header ) {
            header($header);
        }
        echo $this->content;
    }

    private function getDefaultHeaderFromCode( $code ) {
        if ( $code == 200 ) return 'HTTP/1.0 200 OK';
        if ( $code == 301 ) return 'HTTP/1.0 301 Moved Permanently';
        if ( $code == 302 ) return 'HTTP/1.0 302 Found';
        if ( $code == 400 ) return 'HTTP/1.0 400 Bad Request';
        if ( $code == 401 ) return 'HTTP/1.0 401 Unauthorised';
        if ( $code == 403 ) return 'HTTP/1.0 403 Forbidden';
        if ( $code == 404 ) return 'HTTP/1.0 404 Not Found';
        if ( $code == 500 ) return 'HTTP/1.0 500 Internal Server Error';

        return 'HTTP/1.0 500 Internal Server Error';
    }

    public static function json($response,$data,$code)
    {
        (new HttpResponse($response))
            ->setCode($code)
            ->setContent(json_encode($data))
            ->setContentType('application/json')
            ->send();
    }
    public static function html($response,$data,$code) {
        (new HttpResponse($response))
            ->setCode($code)
            ->setContent($data)
            ->setContentType('text/html')
            ->send();
    }

    function send() {
        $this->response->writeHead($this->code, $this->headers);
        $this->response->end($this->content);
    }

}