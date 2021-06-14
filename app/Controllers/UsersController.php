<?php

namespace App\Controllers;

use App\Models\TokensModel;
use App\Models\UsersModel;
use CodeIgniter\RESTful\ResourceController;

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

        return $this->respond($response, $response['status']);
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

            return $this->respond($response, $response['status']);

        } else {
            $UsersModel = new UsersModel();
            $TokensModel = new TokensModel();
            $email = $this->request->getVar("email");
            $password = $this->request->getVar("password");

            $userdata = $UsersModel->where("email", $email)->first();
            $userdataPublic = $UsersModel->getPublicUserData($email);

            if (!empty($userdata)) {

                if (password_verify($password, $userdata['password'])) {

                    $TokensData = [
                        'users_id' => $userdata['id'],
                        'token_session' => password_hash($userdata['id'] . $created_at, PASSWORD_DEFAULT),
                        'created_at' => $created_at,
                    ];

                    $TokensModel->save($TokensData);

                    $response = [
                        'status' => 200,
                        'error' => false,
                        'messages' => 'User logged In successfully',
                        'data' => [
                            'users_id' => $userdata['id'],
                            'token' => $TokensData['token_session'],
                        ],
                    ];
                    return $this->respond($response);
                } else {

                    $response = [
                        'status' => 500,
                        'error' => true,
                        'messages' => 'Incorrect details',
                        'data' => [],
                    ];
                    return $this->respond($response, $response['status']);
                }
            } else {
                $response = [
                    'status' => 500,
                    'error' => true,
                    'messages' => 'User not found',
                    'data' => [],
                ];
                return $this->respond($response, $response['status']);
            }
        }
    }

    public function details()
    {
        $UsersModel = new UsersModel();
        $userIDHeader = $this->request->getHeader("User-ID");
        $userIDHeader = $userIDHeader->getValue();
        $userID = $userIDHeader;

        $data = $UsersModel->find($userID);

        $response = [
            'status' => 200,
            'error' => false,
            'messages' => 'User details',
            'data' => [
                'profile' => $data,
            ],
        ];
        return $this->respond($response, $response['status']);
    }

    public function logout()
    {
        $TokensModel = new TokensModel();
        $tokenHeader = $request->getHeader("Token");
        $tokenHeader = $tokenHeader->getValue();
        $arr = explode(" ", $authHeader);
        $token = $arr[1];

        $deleteTokenSession = $TokensModel->where('users_id', $decoded->data->id)->delete();
        if ($deleteTokenSession) {
            $response = [
                'status' => 200,
                'error' => false,
                'messages' => 'Logout Successfully.',
                'data' => null,
            ];
        } else {
            $response = [
                'status' => 404,
                'error' => true,
                'messages' => 'User ID not found.',
                'data' => null,
            ];
        }
        return $this->respond($response, $response['status']);
    }

}
