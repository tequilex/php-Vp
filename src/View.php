<?php

namespace Base;

class View
{
  private $templatePath;
  private $data;

  public function setTemplatePath(string $path)
  {
    $this->templatePath = $path;
  }


  public function render(string $tpl, $data = [])
  {
    foreach ($data as $key => $value) {
      $this->data[$key] = $value;
    }

    ob_start();
    include $this->templatePath . '/' . $tpl;
    $data = ob_get_clean();
    return $data;
  }


  public function __get($varName)
  {
    return $this->data[$varName] ?? null;
  }
}
