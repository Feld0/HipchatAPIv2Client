<?php

namespace GorkaLaucirica\HipchatAPIv2Client;

use Buzz\Browser;
use Buzz\Client\Curl;
use GorkaLaucirica\HipchatAPIv2Client\Auth\AuthInterface;
use GorkaLaucirica\HipchatAPIv2Client\Exception\RequestException;

class Client
{
    protected $baseUrl = 'https://api.hipchat.com';

    /** @var AuthInterface */
    protected $auth;

    /** @var Browser */
    protected $browser;

    /**
     * Client constructor
     *
     * @param AuthInterface $auth Authentication you want to use to access the api
     * @param Browser $browser Client you want to use, by default browser with curl will be used
     *
     * @return self
     */
    public function __construct(AuthInterface $auth, Browser $browser = null)
    {
        $this->auth = $auth;
        if ($browser === null) {
            $client = new Curl();
            $client->setTimeout(300);
            $this->browser = new Browser($client);
        }
        else {
            $this->browser = $browser;
        }
    }

    /**
     * Common get request for all API calls
     *
     * @param string $resource The path to the resource wanted. For example v2/room
     * @param array $query Parameters to filter the response for example array('max-results' => 50)
     *
     * @return array Decoded array containing response
     * @throws Exception\RequestException
     */
    public function get($resource, $query = array())
    {
        $url = $this->baseUrl . $resource;
        if (count($query) > 0) {
            $url .= "?";
        }

        foreach ($query as $key => $value) {
            $url .= "$key=$value&";
        }

        $headers = array("Authorization" => $this->auth->getCredential());

        $response = $this->browser->get($url, $headers);

        if ($this->browser->getLastResponse()->getStatusCode() > 299) {
            throw new RequestException(json_decode($this->browser->getLastResponse()->getContent(), true));
        }

        return json_decode($response->getContent(), true);
    }

    /**
     * Common post request for all API calls
     *
     * @param string $resource The path to the resource wanted. For example v2/room
     * @param array $content Parameters be posted for example:
     *                              array(
     *                                'name'                => 'Example name',
     *                                'privacy'             => 'private',
     *                                'is_archived'         => 'false',
     *                                'is_guest_accessible' => 'false',
     *                                'topic'               => 'New topic',
     *                              )
     *
     * @return array Decoded array containing response
     * @throws Exception\RequestException
     */
    public function post($resource, $content)
    {
        $url = $this->baseUrl . $resource;

        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => $this->auth->getCredential()
        );

        $response = $this->browser->post($url, $headers, json_encode($content));

        if ($this->browser->getLastResponse()->getStatusCode() > 299) {
            throw new RequestException(json_decode($this->browser->getLastResponse()->getContent(), true));
        }

        return json_decode($response->getContent(), true);
    }

    /**
     * Common put request for all API calls
     *
     * @param string $resource The path to the resource wanted. For example v2/room
     * @param array $content Parameters be putted for example:
     *                              array(
     *                                'name'                => 'Example name',
     *                                'privacy'             => 'private',
     *                                'is_archived'         => 'false',
     *                                'is_guest_accessible' => 'false',
     *                                'topic'               => 'New topic',
     *                              )
     *
     * @return array Decoded array containing response
     * @throws Exception\RequestException
     */
    public function put($resource, $content = array())
    {
        $url = $this->baseUrl . $resource;
        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => $this->auth->getCredential()
        );

        $response = $this->browser->put($url, $headers, json_encode($content));

        if ($this->browser->getLastResponse()->getStatusCode() > 299) {
            throw new RequestException(json_decode($this->browser->getLastResponse()->getContent(), true));
        }

        return json_decode($response->getContent(), true);
    }

    /**
     * Common delete request for all API calls
     *
     * @param string $resource The path to the resource wanted. For example v2/room
     *
     * @return array Decoded array containing response
     * @throws Exception\RequestException
     */
    public function delete($resource)
    {
        $url = $this->baseUrl . $resource;

        $headers = array(
            'Authorization' => $this->auth->getCredential()
        );

        $response = $this->browser->delete($url, $headers);

        if ($this->browser->getLastResponse()->getStatusCode() > 299) {
            throw new RequestException(json_decode($this->browser->getLastResponse()->getContent(), true));
        }

        return json_decode($response->getContent(), true);
    }
}
