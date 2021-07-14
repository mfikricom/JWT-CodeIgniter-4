<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use Firebase\JWT\JWT;

class Login extends ResourceController
{
	/**
	 * Return an array of resource objects, themselves in array format
	 *
	 * @return mixed
	 */
	use ResponseTrait;
	public function index()
	{
		helper(['form']);
		$rules = [
			'email' => 'required|valid_email',
			'password' => 'required|min_length[6]'
		];
		if(!$this->validate($rules)) return $this->fail($this->validator->getErrors());
		$model = new UserModel();
		$user = $model->where("email", $this->request->getVar('email'))->first();
		if(!$user) return $this->failNotFound('Email Tidak Ditemukan');

		$verify = password_verify($this->request->getVar('password'), $user['password']);
		if(!$verify) return $this->fail('Wrong Password');

		$key = getenv('TOKEN_SECRET');
		$payload = array(
			"iat" => 1356999524,
			"nbf" => 1357000000,
			"uid" => $user['id'],
			"email" => $user['email']
		);

		$token = JWT::encode($payload, $key);

		return $this->respond($token);
	}
}
