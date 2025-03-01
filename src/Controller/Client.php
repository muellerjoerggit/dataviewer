<?php

namespace App\Controller;

use App\SymfonyEntity\Client as ClientEntity;
use App\Form\ClientType;
use App\SymfonyRepository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/client')]
class Client extends AbstractController {

	#[Route(path: '/', name: 'app_clients')]
	public function clients(ClientRepository $clientRepository): Response {

		$clients = $clientRepository->findAll();
		$clientData = [];

		foreach ($clients as $client) {
			$clientId = $client->getClientId();
			$clientData[] = [
				'clientId' => $clientId,
				'name' => $client->getName(),
				'updateUrl' => $this->generateUrl('app_client_update', ['client' => $clientId]),
				'deleteUrl' => $this->generateUrl('app_client_delete', ['client' => $clientId])
			];
		}

		return $this->render('admin/clients.html.twig', [
			'clientAddUrl' => $this->generateUrl('app_client_add'),
			'clientsData' => $clientData
		]);
	}

	#[Route(path: '/add', name: 'app_client_add')]
	public function addClient(Request $request, EntityManagerInterface $entityManager): Response {

		$client = new ClientEntity();

		$form = $this->createForm(ClientType::class, $client);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			$entityManager->persist($client);
			$entityManager->flush();
			return $this->redirectToRoute('app_clients');
		}

		return $this->render('admin/clientUpdate.html.twig', [
			'clientForm' => $form->createView()
		]);
	}

	#[Route(path: '/update/{client}', name: 'app_client_update')]
	public function updateClient(Request $request, EntityManagerInterface $entityManager, string $client): Response {

		$client = $entityManager->find(ClientEntity::class, $client);

		$form = $this->createForm(ClientType::class, $client);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			$entityManager->persist($client);
			$entityManager->flush();
			return $this->redirectToRoute('app_clients');
		}

		return $this->render('admin/clientUpdate.html.twig', [
			'clientForm' => $form->createView()
		]);
	}

	#[Route(path: '/delete/{client}', name: 'app_client_delete')]
	public function removeClient(Request $request, EntityManagerInterface $entityManager, string $client): Response {

		$client = $entityManager->find(ClientEntity::class, $client);

		$entityManager->remove($client);
		$entityManager->flush();

		return $this->redirectToRoute('app_clients');
	}

}
