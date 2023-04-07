<?php
namespace App\Model;

use Base\Db;

class Message
{

  private $id;
  private $text;
  private $created_at;
  private $authorId;

  private $author;
  private $image;

  public function __construct(array $data)
  {
    $this->text = $data['text'];
    $this->created_at = $data['created_at'];
    $this->authorId = $data['author_id'];
    $this->image = $data['image'] ?? '';
  }

  public function save()
  {
    $db = Db::getInstance();
    $insert = "INSERT INTO messages (
      `text`, 
      `created_at`,
      `author_id`,
      `image`
      ) VALUES (
        :text, 
        :created_at,
        :author_id,
        :image
        )";
    $res = $db->exec($insert, __METHOD__, [
      ':text' => $this->text,
      ':created_at' => $this->created_at,
      ':author_id' => $this->authorId,
      ':image' => $this->image
    ]);

    return $res;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getCreated_at(): string
  {
    return $this->created_at;
  }

  public function getAuthorId() 
  {
    return $this->authorId;
  }

  public function getText() 
  {
    return $this->text;
  }

  public static function getList(int $limit = 20, $offset = 0): array
  {
    $db = Db::getInstance();
    $query = "SELECT * FROM messages ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    $data = $db->fetchAll($query, __METHOD__);

    if (!$data) {
      return [];
    }

    $messages = [];
    foreach ($data as $elem) {
      $message = new self($elem);
      $message->id = $elem['id'];
      $messages[] = $message;
    }

    return $messages;
  }

  public static function getUserMessages(int $userId, int $limit): array
  {
    $db = Db::getInstance();
    $query = "SELECT * FROM messages WHERE author_id = $userId LIMIT $limit";
    $data = $db->fetchAll($query, __METHOD__);

    if (!$data) {
      return [];
    }

    $messages = [];
    foreach ($data as $elem) {
      $message = new self($elem);
      $message->id = $elem['id'];
      $messages[] = $message;
    }

    return $messages;
  }

  public function getAuthor(): User
  {
    return $this->author;
  }

  public function setAuthor(User $author): void
  {
    $this->author = $author;
  }

  public function loadFile(string $file)
  {
    if (file_exists($file)) {
      $this->image = $this->genFileName();
      move_uploaded_file($file, getcwd() . '/images/' . $this->image);
    }
  }

  private function genFileName()
  {
    return sha1(microtime(1) . mt_rand(1, 10000000)) . '.jpg';
  }

  public function getImage()
  {
    return $this->image;
  }

  public function getData()
  {
    return [
      'text' => $this->text,
      'created_at' => $this->created_at,
      'author_id' => $this->authorId,
      'id' =>$this->id,
      'image' =>$this->image
    ];
  }

  public static function deleteMessage(int $messageId)
  {
    $db = Db::getInstance();
    $query = "DELETE FROM messages WHERE id = $messageId";
    return $db->exec($query, __METHOD__);
  }
}
