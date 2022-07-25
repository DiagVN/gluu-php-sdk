<?php

namespace Gluu\App;

use GuzzleHttp\Client;

class GluuClient
{
    private Client $gluuHttpClient;

    public function __construct($gluuBaseUrl, $gluuClientId, $gluuClientSecret)
    {
        $this->gluuHttpClient = new Client([
            'verify' => false,
            'base_uri' => $gluuBaseUrl,
            'auth' => [$gluuClientId, $gluuClientSecret],
            'http_errors' => true
        ]);
    }

    public function setGluuHttpClient(Client $client): void
    {
        $this->gluuHttpClient = $client;
    }

    public function authenticate(
        ?string $username,
        ?string $password,
        string $grantType,
        ?string $scope = null
    ) : string|null {
        $response = $this->gluuHttpClient->post(config('gluu.authentication_uri'), [
            'form_params' =>
                [
                    [
                        "grant_type" => $grantType,
                        "username" => $username,
                        "password" => $password,
                        "scope"   => $scope
                    ]
                ]
        ]);
        if ($response->getStatusCode() == 200) {
            $object = json_decode((string) $response->getBody());
            if (is_object($object)) {
                return (array) $object;
            }
        }
        return null;
    }

    public function inspectToken(string $accessToken): mixed
    {
        $response = $this->gluuHttpClient->get(config('gluu.inspect_token_uri'), ['query' => ['token' => $accessToken]]);
        if ($response->getStatusCode() == 200) {
            $object = json_decode((string) $response->getBody());
            if (is_object($object)) {
                return (array) $object;
            }
        }
        return null;
    }

    public function register(string $username, string $email, string $phoneNumber, string $password): bool
    {
        $registerToken = $this->authenticate(null, null, 'client_credentials', "https://gluu.org/scim/users.write");
        if (is_null($registerToken)) {
            return false;
        }
        $userData = [
            "schemas"=> [
                "urn:ietf:params:scim:schemas:core:2.0:User"
            ],
            "userName" => $username,
            "displayName" => $username,
            "name"=> [
                "familyName"=> "string",
                "givenName"=> "string",
                "middleName"=> "string",
                "honorificPrefix"=> "string",
                "honorificSuffix"=> "string",
                "formatted"=> "string"
            ],
            "password" => $password,
            "active"=> true,
            "emails" => [
                [
                    "value" =>  $email,
                    "display" =>  "string",
                    "type" =>  "work",
                    "primary" => true
                ]
            ],
            "phoneNumbers" => [
                [
                    "value" =>  $phoneNumber,
                    "display" =>  "string",
                    "type" =>  "work",
                ]
            ],
        ];
        $response = $this->gluuHttpClient->post(config('gluu.create_user_uri'), [
            'auth' => null,
            'headers' => [ 'Authorization' => 'Bearer ' . $registerToken ],
            'json' =>
                $userData
        ]);

        if ($response->getStatusCode()==201) {
            $object = json_decode((string) $response->getBody());
            if (is_object($object)) {
                return (array) $object;
            }
        }
        return false;
    }
}