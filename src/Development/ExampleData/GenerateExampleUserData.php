<?php

namespace App\Development\ExampleData;

use App\Database\DaViDatabaseOne;
use App\Database\SymfonyDatabase;
use App\Services\DateTimeConverter;
use App\SymfonyEntity\Client;
use App\SymfonyEntity\Version;
use App\SymfonyRepository\VersionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

class GenerateExampleUserData extends AbstractMaker {

  private const array ROLES = [
    1 => 'Administrator',
    2 => 'User',
    3 => 'Employee',
    4 => 'Manager',
    5 => 'Trainer',
    6 => 'Customer',
    7 => 'Learner',
    8 => 'HR',
    9 => 'HR Manager',
    10 => 'Developer',
    11 => 'Customer Manager',
    12 => 'Customer Support',
  ];

  private const array FIRSTNAMES = [
    'Noah',
    'Leon',
    'Liam',
    'Milan',
    'Leo',
    'Theo',
    'David',
    'Emil',
    'Luca',
    'Felix',
    'Lio',
    'Adam',
    'Henry',
    'Emilio',
    'Paul',
    'Max',
    'Louis',
    'Jakob',
    'Matteo',
    'Maximilian',
    'Emilia',
    'Mia',
    'Emma',
    'Mila',
    'Lina',
    'Malia',
    'Leonie',
    'Ella',
    'Sophia',
    'Sofia',
    'Amelie',
    'Leni',
    'Sophie',
    'Anna',
    'Amalia',
    'Lara',
    'Luna',
    'Hanna',
    'Emily',
  ];

  private const array LASTNAMES = [
    "Williams",
    "Brown",
    "Taylor",
    "Davies",
    "Evans",
    "Thomas",
    "Roberts",
    "O´Neill",
    "O´Connor",
    "O´Ryan",
    "Byrne",
    "O´Brie",
    "Smith",
    "Walsh",
    "O´Sullivan",
    "O´Kelly",
    "Murphy",
    "Johnson",
    "Jones",
    "Miller",
    "Garcia",
    "Rodriguez",
    "Wilson",
    "Gelbero",
    "Anderson",
    "Wang",
  ];

  private const array CLIENTS = [
    'umbrella' => 'Umbrella',
    'hanka' => 'Hanka Robotics',
    'tyrell' => 'Tyrell Corporation',
    'terrasave' => 'Terra Save',
    'locussolus' => 'LOCUS SOLUS',
  ];

  private const array VERSIONS = [
    '1.0', '1.1', '1.2', '1.3'
  ];

  public function __construct(
    private readonly DaViDatabaseOne $database,
    private readonly EntityManagerInterface $entityManager,
    private readonly DateTimeConverter $timeConverter,
    private readonly SymfonyDatabase $symfonyDatabase,
    private readonly VersionRepository $versionRepository,
  ) {}

  public static function getCommandName(): string {
    return 'davi:generate:example-data:user';
  }

  public function configureCommand(Command $command, InputConfiguration $inputConfig) {}

  public function configureDependencies(DependencyBuilder $dependencies) {}

  public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator) {
    $connectionDDL = $this->database->createConnectionWithoutDbName();

    try {

    if($this->symfonyDatabase->tableExists('client') && $this->symfonyDatabase->tableExists('version')) {
      $this->symfonyDatabase->getConnection()->executeQuery('TRUNCATE client, version;');
    }

    $predecessor = null;
    foreach (self::VERSIONS as $versionString) {
      $version = $this->entityManager->find(Version::class, $versionString);

      if(!$version) {
        $version =
          (new Version())
            ->setId($versionString)
            ->setLabel($versionString)
        ;
      }

      if($predecessor instanceof Version) {
        $version->setPredecessor($predecessor);
        $predecessor->setSuccessor($version);
        $this->entityManager->persist($predecessor);
      }

      $this->entityManager->persist($version);
      $this->entityManager->flush();

      $predecessor = $version;
    }

    $versions = $this->versionRepository->findAll();

    foreach (self::CLIENTS as $clientId => $clientName) {
      $client = $this->entityManager->find(Client::class, $clientId);


        if (!$client) {
          $client =
            (new Client())
              ->setName($clientName)
              ->setClientId($clientId)
              ->setDatabaseName($clientId);

          if (!empty($versions)) {
            $clientVersion = $versions[array_rand($versions)] ?? NULL;
            $client->setVersion($clientVersion);
          }

          $this->entityManager->persist($client);
          $this->entityManager->flush();
        }
    }

    foreach (self::CLIENTS as $clientId => $clientName) {
      $client = $this->entityManager->find(Client::class, $clientId);

      if (!$client) {
        continue;
      }

      try {
        $stmt = $connectionDDL->prepare("DROP DATABASE IF EXISTS $clientId;");
        $stmt->executeStatement();

        $stmt = $connectionDDL->prepare("CREATE DATABASE IF NOT EXISTS $clientId;");
        $stmt->executeStatement();

        $connection = $this->database->getConnection($clientId);

        $version = $client->getVersion();
        $versionId = '';
        if($version instanceof Version) {
          $versionId = $version->getId();
        }

        $stmt = $connection->prepare("
        CREATE TABLE usr_data (
            usr_id INT AUTO_INCREMENT PRIMARY KEY,
            firstname VARCHAR(150),
            lastname VARCHAR(150),
            email VARCHAR(150),
            active TINYINT,
            inactivation_date DATETIME)");
        $stmt->executeStatement();

        if($versionId === '1.3') {
          $stmt = $connection->prepare("ALTER TABLE usr_data ADD COLUMN second_email VARCHAR(150) DEFAULT NULL;");
          $stmt->executeStatement();
        }

        $stmt = $connection->prepare("
        CREATE TABLE role (
            rol_id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(150),
            description VARCHAR(250))");
        $stmt->executeStatement();

        $stmt = $connection->prepare("
        CREATE TABLE role_user_map (
            rol_id INT,
            usr_id INT)");
        $stmt->executeStatement();
      } catch (\Exception $exception) {dd($exception, $connection);}

      $rolesSql = 'INSERT INTO role (rol_id, title) VALUE ';

      foreach (self::ROLES as $id => $title) {
        $rolesSql .= "($id, '$title'),";
      }

      $userSql = 'INSERT INTO usr_data (usr_id, firstname, lastname, email, active, inactivation_date) VALUES ';
      $userRolesSql = 'INSERT INTO role_user_map (rol_id, usr_id) VALUES ';

      if($versionId === '1.3') {
        $userSql = 'INSERT INTO usr_data (usr_id, firstname, lastname, email, active, inactivation_date, second_email) VALUES ';
      }

      for ($usrId = 1; $usrId <= 1000; $usrId++) {
        $firstname = self::FIRSTNAMES[array_rand(self::FIRSTNAMES)];
        $lastname = self::LASTNAMES[array_rand(self::LASTNAMES)];
        $email = "$firstname.$lastname@example.com";
        $active = rand(1, 10) > 2 ? 1 : 0;
        $begin = new DateTime('2020-01-01');
        $end = new DateTime('2023-12-31');
        $inactiveDate = $active ? 'NULL' : '"' . $this->timeConverter->randomDateTime($begin, $end) . '"';

        if($versionId === '1.3') {
          $secondEmail = "$lastname.$firstname@example.com";
          $userSql .= "($usrId, '$firstname', '$lastname', '$email', $active, $inactiveDate, '$secondEmail'),";
        } else {
          $userSql .= "($usrId, '$firstname', '$lastname', '$email', $active, $inactiveDate),";
        }

        $numberRoles = rand(2, 12);
        $roles = array_rand(self::ROLES, $numberRoles);

        foreach ($roles as $roleId) {
          $userRolesSql .= "($roleId, $usrId),";
        }
      }

      $rolesSql = rtrim($rolesSql, ',');
      $userSql = rtrim($userSql, ',');
      $userRolesSql = rtrim($userRolesSql, ',');

      $stmt = $connection->prepare($rolesSql);
      $stmt->executeStatement();

      $stmt = $connection->prepare($userSql);
      $stmt->executeStatement();

      $stmt = $connection->prepare($userRolesSql);
      $stmt->executeStatement();
    }

    } catch (\Exception $e) {dd($e);}
  }

  public function __call(string $name, array $arguments) {}

}