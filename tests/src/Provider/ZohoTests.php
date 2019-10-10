<?php


namespace ShahariaAzam\ZohoOAuth2\Client\Test\Provider;

use Eloquent\Phony\Phony;
use GuzzleHttp\ClientInterface;
use League\OAuth2\Client\Token\AccessToken;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_Error_Notice;
use PHPUnit_Framework_Error_Warning;
use Psr\Http\Message\ResponseInterface;
use ShahariaAzam\ZohoOAuth2\Client\Provider\Zoho;

class ZohoTests extends TestCase
{
    protected $provider;

    public function setUp()
    {
        PHPUnit_Framework_Error_Warning::$enabled = FALSE;

        PHPUnit_Framework_Error_Notice::$enabled = FALSE;

        $this->provider = new \ShahariaAzam\ZohoOAuth2\Client\Provider\Zoho([
            'clientId'          => '{zoho-client-id}',
            'clientSecret'      => '{zoho-client-secret}',
            'redirectUri'       => 'https://example.com/callback-url'
        ]);
    }

    public function tearDown()
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowInvalidArgumentExceptIfClientIDMissing()
    {
        $provider = new \ShahariaAzam\ZohoOAuth2\Client\Provider\Zoho([
            'clientId'          => '{zoho-client-id}'
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowInvalidArgumentExceptIfRedirectUriMissing()
    {
        $provider = new \ShahariaAzam\ZohoOAuth2\Client\Provider\Zoho([
            'clientId'          => '{zoho-client-id}',
            'clientSecret'          => '{zoho-client-id}'
        ]);
    }

    public function testGetBaseAuthorizationUrl()
    {
        $this->assertEquals("https://accounts.zoho.com/oauth/v2/auth", $this->provider->getBaseAuthorizationUrl());
    }

    public function testGetBaseAccessTokenUrl()
    {
        $this->assertEquals("https://accounts.zoho.com/oauth/v2/token", $this->provider->getBaseAccessTokenUrl([]));
    }

    public function testGetResourceOwnerDetailsUrl()
    {
        $this->assertEquals("https://accounts.zoho.com/oauth/user/info", $this->provider->getResourceOwnerDetailsUrl(\Mockery::mock(AccessToken::class)));
    }

    /**
     * @param $body
     * @return \Eloquent\Phony\Mock\Handle\InstanceHandle
     */
    protected function mockResponse($body)
    {
        $response = Phony::mock(ResponseInterface::class);
        $response->getHeader->with('content-type')->returns('application/json');
        $response->getBody->returns(json_encode($body));
        return $response;
    }
    /**
     * @param ResponseInterface $response
     * @return \Eloquent\Phony\Mock\Handle\InstanceHandle
     */
    protected function mockClient(ResponseInterface $response)
    {
        $client = Phony::mock(ClientInterface::class);
        $client->send->returns($response);
        return $client;
    }
    /**
     *
     */
    public function testAuthorizationUrl()
    {
        // Run
        $url = $this->provider->getAuthorizationUrl();
        $path = \parse_url($url, PHP_URL_PATH);
        // Verify
        $this->assertSame('/oauth/v2/auth', $path);
    }
    /**
     *
     */
    public function testBaseAccessTokenUrl()
    {
        $params = [];
        // Run
        $url = $this->provider->getBaseAccessTokenUrl($params);
        $path = \parse_url($url, PHP_URL_PATH);
        // Verify
        $this->assertSame('/oauth/v2/token', $path);
    }

    /**
     * @throws \ReflectionException
     */
    public function testDefaultScopes()
    {
        $reflection = new \ReflectionClass(get_class($this->provider));
        $getDefaultScopesMethod = $reflection->getMethod('getDefaultScopes');
        $getDefaultScopesMethod->setAccessible(true);
        // Run
        $scope = $getDefaultScopesMethod->invoke($this->provider);
        // Verify
        $this->assertEquals(['aaaserver.profile.READ','ZohoProfile.userinfo.read', 'ZohoProfile.userphoto.read'], $scope);
    }
    /**
     *
     */
    public function testGetAccessToken()
    {
        // https://github.com/hhru/api/blob/master/docs/authorization.md
        $body = [
            'access_token' => 'mock_access_token',
            'token_type' => 'bearer',
            'expires_in' => \time() * 3600,
            'refresh_token' => 'mock_refresh_token',
        ];
        $response = $this->mockResponse($body);
        $client = $this->mockClient($response->get());
        // Run
        $this->provider->setHttpClient($client->get());
        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        // Verify
        $this->assertNull($token->getResourceOwnerId());
        $this->assertEquals($body['access_token'], $token->getToken());
        $this->assertEquals($body['refresh_token'], $token->getRefreshToken());
        $this->assertGreaterThanOrEqual($body['expires_in'], $token->getExpires());
    }
    /**
     * @expectedException League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function testErrorResponses()
    {
        $code = 401;
        $body = [
            'error' => 'Foo error',
            'message' => 'Error Message',
        ];
        $response = $this->mockResponse($body);
        $response->getStatusCode->returns($code);
        $client = $this->mockClient($response->get());
        // Run
        $this->provider->setHttpClient($client->get());
        $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
    }
}