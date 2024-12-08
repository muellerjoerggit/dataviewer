<?php

namespace App\SymfonyEntity;

use App\SymfonyRepository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client {

	#[ORM\Id]
	#[ORM\Column(length: 100)]
	private ?string $client_id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $database_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

	public function getClientId(): ?string {
		return $this->client_id;
	}

	public function setClientId(string $client_id): static {
		$this->client_id = $client_id;

		return $this;
	}

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;

        return $this;
    }

    public function getDatabaseName(): ?string {
        return $this->database_name;
    }

    public function setDatabaseName(string $database_name): static {
        $this->database_name = $database_name;

        return $this;
    }

    public function getUrl(): ?string {
        return $this->url;
    }

    public function setUrl(?string $url): static {
        $this->url = $url;

        return $this;
    }
}
