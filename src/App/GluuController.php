<?php

namespace Gluu\App\Controller;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class GluuController extends Controller
{
    private Client $gluuHttpClient;

    public function __construct()
    {
        $this->gluuHttpClient = new Client([
            'verify' => false,
            'base_uri' => env('gluu_base_url'),
            'auth' => [env('gluu_client_id'),env('gluu_client_secret')],
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
        $response = $this->gluuHttpClient->post(env('authentication_uri'), [
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
                return $object;
            }
        }
        return null;
    }

    public function inspectToken(string $accessToken): mixed
    {
        $response = $this->gluuHttpClient->get(env('inspect_token_uri'), ['query' => ['token' => $accessToken]]);
        if ($response->getStatusCode() == 200) {
            $object = json_decode((string) $response->getBody());
            if (is_object($object)) {
                return $object;
            }
        }
        return null;
    }

    public function register(string $username, string $email, string $password): bool
    {
        $registerToken = $this->authenticate(null, null, 'client_credentials', "https://gluu.org/scim/users.write");
        if (is_null($registerToken)) {
            return false;
        }
        $userSample = [
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
        ];
        $response = $this->gluuHttpClient->post(env('create_user_uri', '/identity/restv1/scim/v2/Users'), [
            'auth' => null,
            'headers' => [ 'Authorization' => 'Bearer ' . $registerToken ],
            'json' =>
                $userSample
        ]);

        if ($response->getStatusCode()==201) {
            $object = json_decode((string) $response->getBody());
            if (is_object($object)) {
                return $object;
            }
        }
        return false;
    }
}