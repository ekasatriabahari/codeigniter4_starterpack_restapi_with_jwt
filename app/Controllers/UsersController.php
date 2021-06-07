<?php

namespace App\Controllers;

use App\Models\TokensModel;
use App\Models\UsersModel;
use CodeIgniter\RESTful\ResourceController;
use Exception;
use \Firebase\JWT\JWT;

class UsersController extends ResourceController
{
    public function register()
    {
        $rules = [
            "name" => "required",
            "email" => "required|valid_email|is_unique[users.email]|min_length[6]",
            "password" => "required",
        ];

        $messages = [
            "name" => [
                "required" => "Name is required",
            ],
            "email" => [
                "required" => "Email required",
                "valid_email" => "Email address is not in format",
            ],
            "password" => [
                "required" => "password is required",
            ],
        ];

        if (!$this->validate($rules, $messages)) {

            $response = [
                'status' => 500,
                'error' => true,
                'message' => $this->validator->getErrors(),
                'data' => [],
            ];
        } else {

            $UsersModel = new UsersModel();

            $data = [
                "name" => $this->request->getVar("name"),
                "email" => $this->request->getVar("email"),
                "phone_no" => $this->request->getVar("phone_no"),
                "password" => password_hash($this->request->getVar("password"), PASSWORD_DEFAULT),
            ];

            if ($UsersModel->insert($data)) {

                $response = [
                    'status' => 200,
                    "error" => false,
                    'messages' => 'Successfully, user has been registered',
                    'data' => [],
                ];
            } else {

                $response = [
                    'status' => 500,
                    "error" => true,
                    'messages' => 'Failed to create user',
                    'data' => [],
                ];
            }
        }

        return $this->respondCreated($response);
    }

    private function getKey()
    {
        // use your special secret chars
        return "my_application_secret";
    }

    public function login()
    {
        $rules = [
            "email" => "required|valid_email|min_length[6]",
            "password" => "required",
        ];

        $messages = [
            "email" => [
                "required" => "Email required",
                "valid_email" => "Email address is not in format",
            ],
            "password" => [
                "required" => "password is required",
            ],
        ];

        if (!$this->validate($rules, $messages)) {

            $response = [
                'status' => 500,
                'error' => true,
                'message' => $this->validator->getErrors(),
                'data' => [],
            ];

            return $this->respondCreated($response);

        } else {
            $UsersModel = new UsersModel();
            $TokensModel = new TokensModel();

            $userdata = $UsersModel->where("email", $this->request->getVar("email"))->first();

            if (!empty($userdata)) {

                if (password_verify($this->request->getVar("password"), $userdata['password'])) {

                    $key = $this->getKey();

                    $iat = time(); // current timestamp value
                    $nbf = $iat + 10;
                    $exp = $iat + 3600;

                    $payload = array(
                        "iss" => "The_claim",
                        "aud" => "The_Aud",
                        "iat" => $iat, // issued at
                        "nbf" => $nbf, //not before in seconds
                        "exp" => $exp, // expire time in seconds
                        "data" => $userdata,
                    );

                    $token = JWT::encode($payload, $key);

                    $TokensData = [
                        'users_id' => $userdata['id'],
                        'token' => $token,
                        'created_at' => date('Y-m-d H:i:s'),
                    ];

                    $TokensModel->save($TokensData);

                    $response = [
                        'status' => 200,
                        'error' => false,
                        'messages' => 'User logged In successfully',
                        'data' => [
                            'token' => $token,
                            'userdata' => $userdata,
                        ],
                    ];
                    return $this->respondCreated($response);
                } else {

                    $response = [
                        'status' => 500,
                        'error' => true,
                        'messages' => 'Incorrect details',
                        'data' => [],
                    ];
                    return $this->respondCreated($response);
                }
            } else {
                $response = [
                    'status' => 500,
                    'error' => true,
                    'messages' => 'User not found',
                    'data' => [],
                ];
                return $this->respondCreated($response);
            }
        }
    }

    public function details()
    {
        $key = $this->getKey();
        $authHeader = $this->request->getHeader("Authorization");
        $authHeader = $authHeader->getValue();
        $token = $authHeader;

        try {
            $decoded = JWT::decode($token, $key, array("HS256"));

            if ($decoded) {

                $response = [
                    'status' => 200,
                    'error' => false,
                    'messages' => 'User details',
                    'data' => [
                        'profile' => $decoded,
                    ],
                ];
                return $this->respondCreated($response);
            }
        } catch (Exception $ex) {

            $response = [
                'status' => 401,
                'error' => true,
                'messages' => 'Access denied',
                'data' => [],
            ];
            return $this->respondCreated($response);
        }
    }
}
