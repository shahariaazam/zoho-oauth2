<?php


namespace ShahariaAzam\ZohoOAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

/**
 * Class ZohoUser
 * @package ShahariaAzam\ZohoOAuth2\Client\Provider
 */
class ZohoUser implements ResourceOwnerInterface
{
    use ArrayAccessorTrait;

    /**
     * @var array
     */
    protected $response = [];

    /**
     * ZohoUser constructor.
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * Returns the identifier of the authorized resource owner.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->getResponseData('ZUID');
    }

    /**
     * @param $path
     * @param null $default
     * @return mixed
     */
    protected function getResponseData($path, $default = null)
    {
        return $this->getValueByKey($this->response, $path, $default);
    }

    /**
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->getResponseData('First_Name');
    }

    /**
     * @return string|null
     */
    public function getLastName()
    {
        return $this->getResponseData('Last_Name');
    }

    /**
     * @return string|null
     */
    public function getEmail()
    {
        return $this->getResponseData('Email');
    }

    /**
     * @return string|null
     */
    public function getDisplayName()
    {
        return $this->getResponseData('Display_Name');
    }

    /**
     * @return string|null
     */
    public function getPicture()
    {
        return null;
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'first_name' => $this->getResponseData("First_Name"),
            'last_name' => $this->getResponseData("First_Name"),
            'display_name' => $this->getResponseData("Display_Name"),
            'email' => $this->getResponseData("Email"),
            'id' => $this->getResponseData("ZUID"),
        ];
    }
}
