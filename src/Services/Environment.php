<?php

namespace App\Services;

class Environment {

  public function getDatabaseUser(): string {
    return getenv('DATABASE_USER');
  }

  public function getDatabasePassword(): string {
    return getenv('DATABASE_PASSWORD');
  }

  public function getDatabaseHost(): string {
    return getenv('DATABASE_HOST');
  }

  public function getDatabasePort(): string {
    return getenv('DATABASE_PORT');
  }

}