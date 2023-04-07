<?php
namespace App\Model;

use Base\Db;

class User
{

  private $id;
  private $name;
  private $password;
  private $created_at;
  private $email;

  public function __construct(array $data)
  {
    $this->name = $data['name'];
    $this->password = $data['password'];
    $this->created_at = $data['created_at'];
    $this->email = $data['email'];
  }

  public function save()
  {
    $db = Db::getInstance();
    $insert = "INSERT INTO users (
      `name`, 
      `password`, 
      `created_at`,
      `email`
      ) VALUES (
        :name, 
        :password, 
        :created_at,
        :email
        )";
    $res = $db->exec($insert, __METHOD__, [
      ':name' => $this->name,
      ':password' => self::getPasswordHash($this->password),
      ':created_at' => $this->created_at,
      ':email' => $this->email
    ]);

    $this->id = $db->lastInsertId();

    return $res;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function setName(string $name)
  {
    $this->name = $name;
    return $this;
  }
  
  public function getId()
  {
    return $this->id;
  }

  public function setId(int $id): self
  {
    $this->id = $id;
    return $this;
  }
  
  public function getPassword()
  {
    return $this->password;
  }

  public function setPassword($password): self
  {
    $this->password = $password;
    return $this;
  }
  

  public function getCreated_at(): string
  {
    return $this->created_at;
  }

  public function setCreated_at(string $created_at): self
  {
    $this->created_at = $created_at;
    return $this;
  }


  public static function getById(int $id): ?self
  {
    $db = Db::getInstance();
    $select = "SELECT * FROM users WHERE id = $id";
    $data = $db->fetchOne($select, __METHOD__);
    
    if (!$data) {
      return null;
    }

    $user = new self($data);
    $user->id = $id;
    return $user;
  }

  public static function getByIds(array $userIds) 
  {
    $db = Db::getInstance();
    $idsString = implode(',', $userIds);
    $query = "SELECT * FROM users WHERE id IN($idsString)";
    $data = $db->fetchAll($query, __METHOD__);

    if (!$data) {
      return [];
    }

    $users = [];
    foreach ($data as $elem) {
      $user = new self($elem);
      $user->id = $elem['id'];
      $users[$user->id] = $user;
    }

    return $users; 
  }

  public static function getByemail(string $email)
  {
    $db = Db::getInstance();
    $select = "SELECT * FROM users WHERE email = :email";
    $data = $db->fetchOne(
      $select, 
      __METHOD__, 
      [':email' => $email]
    );

    if (!$data) {
      return null;
    }

    $user = new self($data);
    $user->id = $data['id'];
    return $user;
  }

  public static function getPasswordHash(string $password)
  {
    return sha1(',vsd.3' . $password);
  }

  public function isAdmin(): bool
  {
    return in_array($this->id, ADMIN_IDS);
  }
}