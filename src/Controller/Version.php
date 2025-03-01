<?php

namespace App\Controller;

use App\Form\VersionType;
use App\SymfonyEntity\Version as VersionEntity;
use App\SymfonyRepository\VersionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/version')]
class Version extends AbstractController {

	#[Route(path: '/', name: 'app_versions')]
	public function clients(VersionRepository $versionRepository): Response {

		$versions = $versionRepository->findAll();
		$versionData = [];

		foreach ($versions as $version) {
			$versionId = $version->getId();
			$versionData[] = [
				'versionId' => $versionId,
				'name' => $version->getLabel(),
				'updateUrl' => $this->generateUrl('app_version_update', ['version' => $versionId]),
				'deleteUrl' => $this->generateUrl('app_version_delete', ['version' => $versionId])
			];
		}

		return $this->render('admin/versions.html.twig', [
			'versionAddUrl' => $this->generateUrl('app_version_add'),
			'versionsData' => $versionData
		]);
	}

	#[Route(path: '/add', name: 'app_version_add')]
	public function addVersion(Request $request, EntityManagerInterface $entityManager): Response {

		$version = new VersionEntity;

		$form = $this->createForm(VersionType::class, $version);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			$entityManager->persist($version);
			$entityManager->flush();
			return $this->redirectToRoute('app_versions');
		}

		return $this->render('admin/versionUpdate.html.twig', [
			'versionForm' => $form->createView()
		]);
	}

	#[Route(path: '/update/{version}', name: 'app_version_update')]
	public function updateVersion(Request $request, EntityManagerInterface $entityManager, string $version): Response {

		$version = $entityManager->find(VersionEntity::class, $version);

		$form = $this->createForm(VersionType::class, $version);
		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			$entityManager->persist($version);
			$entityManager->flush();
			return $this->redirectToRoute('app_versions');
		}

		return $this->render('admin/versionUpdate.html.twig', [
			'versionForm' => $form->createView()
		]);
	}

	#[Route(path: '/delete/{version}', name: 'app_version_delete')]
	public function removeVersion(EntityManagerInterface $entityManager, string $version): Response {

		$version = $entityManager->find(VersionEntity::class, $version);

		$entityManager->remove($version);
		$entityManager->flush();

		return $this->redirectToRoute('app_versions');
	}

}
