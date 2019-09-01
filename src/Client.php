<?php

namespace Rdj\Client;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class Client
{
    /**
     * The GuzzleHttp Client instance.
     *
     * @var \GuzzleHttp\Client
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    protected $http;

    /**
     * The Endpoint instance.
     *
     * @var string
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    protected $endpoint;

    /**
     * The Endpoint instance.
     *
     * @var string
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    protected $uri;

    /**
     * The Endpoint instance.
     *
     * @var string
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    
    protected $base='';

    /**
     * The headers that will be sent when call the API.
     *
     * @var array
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    protected $headers = [];

    /**
     * The body that will be sent when call the API.
     *
     * @var mixed
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    protected $body;

    /**
     * The query that will be sent when call the API.
     *
     * @var array
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    protected $query = [];

     /**
     * Create a new Class instance.
     *
     * @param  \GuzzleHttp\Client  $http
     * @return void
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;

        $this->headers = $this->headers();

        $this->uri = config('client_restapi.uri');
    }

    /**
     * The headers that will be sent when call the API.
     *
     * @var array
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     * @modify Sulaeman Rasyid add default headers <sulaemanr46@gmail.com>
     */
    public function headers()
    {
        $headersDefault = [
            'Accept'   =>'application/json',
        ];
        if (session('authenticate') ) { 
            $headersDefault['Authorization']=session('authenticate.token');
        }
        return $this->headers = $headersDefault;
    }

    /**
     * The headers that will be sent when call the API.
     *
     * @var array
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    public function uri()
    {
        $this->base = env('BASE');
        return $this->uri . $this->base .$this->endpoint;
    }

    /**
     * The headers that will be sent when call the API.
     *
     * @var string
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    public function setBase($base)
    {
        $this->base = "{$base}/";

        return $this;
    }

    /**
     * Set request endpoint.
     *
     * @param  \GuzzleHttp\Client  $http
     * @return App\RestMiddleware\Client
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    public function setEndpoint($endpoint = '')
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Set header for request.
     *
     * @param  array  $headers
     * @return App\RestMiddleware\Client
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers(), $headers);

        return $this;
    }

    /**
     * Set body for request.
     *
     * @param  mixed  $body
     * @return App\RestMiddleware\Client
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Set body for request.
     *
     * @param  array  $query
     * @return App\RestMiddleware\Client
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    public function setQuery(array $query)
    {
        $this->query = http_build_query($query);

        return $this;
    }

    /**
     * Get for method.
     *
     * @param  string  $query
     * @return App\RestMiddleware\Client
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    public function setMethod($method)
    {
        switch ($method) {
            case 'multipart':
                $methods = ['method' => 'POST', 'more_content' => [['name' => '_method', 'contents' => 'put']]];
                break;
            default:
                $methods = ['method' => 'PUT'];
                break;
        }

        return $methods;
    }

    /**
     * Get request from middleware.
     *
     * @return \Illuminate\Http\Response
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    public function get()
    { 
        try {
            $request  = $this->http->request('GET', $this->uri(), [
                'headers'  => $this->headers,
                'query'    => $this->query
            ]);
            $response = json_decode($request->getBody(), true);
        } catch (ClientException $e) {
            $body = $e->getResponse()->getBody();
            $response = json_decode($body->getContents(), true);
        } catch (ServerException $e) {
            abort(500);
        }

        return $response;
    }

    /**
     * Post request to middleware.
     *
     * @return \Illuminate\Http\Response
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    public function post($type = 'json')
    { 
        try {
            $request  = $this->http->request('POST', $this->uri(), [
                'headers'  => $this->headers,
                'query'    => $this->query,
                $type      => $this->body
            ]);
            $response = json_decode($request->getBody(), true);
        } catch (ClientException $e) {
            $body = $e->getResponse()->getBody();
            $response = json_decode($body->getContents(), true);
        } catch (ServerException $e) {
            \Log::info($e->getRequest()->getBody());
            abort(500);
        }

        return $response;
    }

    /**
     * Post request to middleware.
     *
     * @return \Illuminate\Http\Response
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    public function put($type = 'json')
    {
        $method = $this->setMethod($type);
        $body = array_key_exists('more_content', $method) ? array_merge($this->body, $method['more_content']) : $this->body;

        try {
            $request  = $this->http->request($method['method'], $this->uri(), [
                'headers'  => $this->headers,
                'query'    => $this->query,
                $type      => $body
            ]);
            $response = json_decode($request->getBody(), true);
        } catch (ClientException $e) {
            $body = $e->getResponse()->getBody();
            $response = json_decode($body->getContents(), true);
        } catch (ServerException $e) {
            abort(500);
        }

        return $response;
    }

    /**
     * Delete request to middleware.
     *
     * @return \Illuminate\Http\Response
     * @author Rangga Darmajati <rangga.android69@gmail.com>
     */
    public function deleted()
    {
        try {
            $request  = $this->http->request('DELETE', $this->uri(), [
                'headers'  => $this->headers,
                'query'    => $this->query,
                'json'     => $this->body
            ]);
            $response = json_decode($request->getBody(), true);
        } catch (ClientException $e) {
            $body = $e->getResponse()->getBody();
            $response = json_decode($body->getContents(), true);
        } catch (ServerException $e) {
            abort(500);
        }

        return $response;
    }
}
