<?php

namespace App\Development\Controller;

use App\SymfonyEntity\BackgroundTask;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BackgroundTasks extends AbstractController {

  public function create(EntityManagerInterface $entityManager): void {
    $task = new BackgroundTask();
    $task
      ->setName('')
      ->setStatus(1)
      ->setTerminate(false);

    $entityManager->persist($task);
    $entityManager->flush();

  }

}