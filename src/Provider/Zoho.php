<?php


namespace ShahariaAzam\ZohoOAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use ShahariaAzam\ZohoOAuth2\Client\Exceptions\IdentifyProviderException;

class Zoho extends AbstractProvider
{
    /**
     * @var string
     */
    protected $endpoint = "https://accounts.zoho.com";
    protected $accessType = "online";

    /**
     * Zoho constructor.
     * @param array $options
     * @param array $collaborators
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);

        foreach (['clientId', 'clientSecret', 'redirectUri'] as $key) {
            if (!isset($options[$key])) {
                throw new \InvalidArgumentException($key . " is missing");
            }
        }
    }

    /**
     * Returns the base URL for authorizing a client.
     *
     * Eg. https://oauth.service.com/authorize
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->endpoint . "/oauth/v2/auth";
    }

    /**
     * Returns the base URL for requesting an access token.
     *
     * Eg. https://oauth.service.com/token
     *
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->endpoint . "/oauth/v2/token";
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->endpoint . "/oauth/user/info";
    }

    /**
     * @param mixed|null $token
     * @return array|void
     */
    protected function getAuthorizationHeaders($token = null)
    {
        return ['Authorization' => "Bearer " . $token];
    }

    /**
     * @param array $options
     * @return array
     */
    protected function getAuthorizationParameters(array $options)
    {
        if (empty($options['state'])) {
            $options['state'] = $this->getRandomState();
        }

        if (empty($options['scope'])) {
            $options['scope'] = $this->getDefaultScopes();
        }

        $options += [
            'response_type' => 'code',
            'approval_prompt' => 'auto'
        ];

        if (is_array($options['scope'])) {
            $separator = $this->getScopeSeparator();
            $options['scope'] = implode($separator, $options['scope']);
        }

        // Store the state as it may need to be accessed later on.
        $this->state = $options['state'];

        // Business code layer might set a different redirect_uri parameter
        // depending on the context, leave it as-is
        if (!isset($options['redirect_uri'])) {
            $options['redirect_uri'] = $this->redirectUri;
        }

        $options['client_id'] = $this->clientId;

        if (isset($options['access_type']) && in_array($options['access_type'], ['offline', 'online'])) {
            $options['access_type'] = $this->accessType;
        }

        return $options;
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return ['aaaserver.profile.READ', 'ZohoProfile.userinfo.read', 'ZohoProfile.userphoto.read'];
    }

    /**
     * Checking response to see if provider returned any error
     *
     * @param ResponseInterface $response
     * @param array|string $data Parsed response data
     * @return void
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        $responseData = $this->parseResponse($response);
        if (array_key_exists("error", $responseData)) {
            throw new IdentifyProviderException($responseData['error'], $response->getStatusCode(), $response);
        }
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return ZohoUser
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new ZohoUser($response);
    }
}
