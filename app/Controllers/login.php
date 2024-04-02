<?php

namespace App\Controllers;

use App\Models\UsersModel;

class Login extends BaseController
{
    protected $users;

    public function __construct()
    {
        $this->users = new UsersModel();
    }

    public function index()
    {
        return view('view_login');
    }

    public function process()
    {
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $dataUser = $this->users->where('username', $username)->first();

        if (!$dataUser || !password_verify($password, $dataUser->password)) {
            session()->setFlashdata('error', 'Username & Password Salah');
            return redirect()->back();
        }

        $this->setUserSession($dataUser);

        $this->sendTelegramMessage("FRISKA SASTI");

        return redirect()->to(base_url('/dosen'));
    }

    protected function setUserSession($user)
    {
        session()->set([
            'username' => $user->username,
            'name' => $user->name,
            'logged_in' => true
        ]);
    }

    protected function sendTelegramMessage($message)
    {
        $token = getenv('TELEGRAM_BOT_TOKEN');
        $chatId = getenv('TELEGRAM_CHAT_ID');

        $data = [
            'text' => $message,
            'chat_id' => $chatId
        ];

        file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query($data));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
