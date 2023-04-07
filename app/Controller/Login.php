<?php
namespace App\Controller;

use App\Model\User;
use Base\AbstractController;

class Login extends AbstractController 
{
  public function index()
  {
    if ($this->getUser()) {
      $this->redirect('/blog');
    }
    return $this->view->render(
      'login.phtml', 
      [
        'title' => 'Главная',
        'user' => $this->getUser(),
        ]
    );
  }

  public function auth()
  {
    $password = (string) $_POST['password'];
    $email = (string) $_POST['email'];

    $user = User::getByEmail($email);
    if (!$user) {
      return 'Неверный логин или пароль'; 
    }

    if ($user->getPassword() !== User::getPasswordHash($password)) {
      return 'Неверный логин или пароль';
    } 

    $this->session->authUser($user->getId());

    $this->redirect('/blog');
  }

  public function register()
  {
    $name = (string) $_POST['name'];
    $password = (string) $_POST['password'];
    $passwordRepeat = (string) $_POST['password_2'];
    $email = (string) $_POST['email'];

    if (!$name || !$password) {
      return 'Не заданы имя и пароль';
    }

    if (!$email) {
      return 'Не задан email';
    }

    if ($password !== $passwordRepeat) {
      return 'Пароли не совпадают';
    }

    if (mb_strlen($password) < 5) {
      return 'Пароль должен быть больше 5 символов';
    }

    $userData = [
      'name' => $name,
      'password' => $password,
      'created_at' => date('Y-m-d H:i:s'),
      'email' => $email
    ];

    $user = new User($userData);
    $user->save();

    $this->session->authUser($user->getId());
    $this->redirect('/blog');
  }
}