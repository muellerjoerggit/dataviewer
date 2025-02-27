<?php

namespace App\Services;

class Environment {

  public function getDatabaseUser(): string {
    return $_ENV['DATABASE_USER'];
  }

  public function getDatabasePassword(): string {
    return $_ENV['DATABASE_PASSWORD'];
  }

  public function getDatabaseHost(): string {
    return $_ENV['DATABASE_HOST'];
  }

  public function getDatabasePort(): string {
    return $_ENV['DATABASE_PORT'];
  }

}