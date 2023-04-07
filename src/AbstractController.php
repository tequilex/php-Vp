<?php
namespace Base;

use App\Model\User;

class AbstractController
{
  public $view;
  public $session;

  public function setView(View $view)
  {
    $this->view = $view;
  }

  public function setSession(Session $session)
  {
    $this->session = $session;
  }

  public function getUser(): ?User
  {
    $userId = $this->session->getUserId();
    if (!$userId) {
      return null;
    }

    $user = User::getById($userId);
    if (!$user) {
      return null;
    }

    return $user;
  }

  public function getUserId()
  {
    if ($user = $this->getUser()) {
      return $user->getId();
    }

    return false;
  }

  public function redirect(string $url)
  {
    throw new RedirectException($url);
  }

  public function preDispatch()
  {
    
  }
}