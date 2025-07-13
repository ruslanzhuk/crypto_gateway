<?php

namespace App\Controller\Api\Admin;

use App\Entity\Wallet;
use App\Repository\WalletRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/adminapi/wallet')]
final class WalletController extends AbstractController
{
    #[Route(name: 'app_wallet_index', methods: ['GET'])]
    public function index(WalletRepository $walletRepository): Response
    {
        return $this->render('api/admin/wallet/index.html.twig', [
            'wallets' => $walletRepository->findAll(),
        ]);
    }

//    #[Route('/new', name: 'app_wallet_new', methods: ['GET', 'POST'])]
//    public function new(Request $request, EntityManagerInterface $entityManager): Response
//    {
//        $wallet = new Wallet();
//        $form = $this->createForm(WalletType::class, $wallet);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->persist($wallet);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_wallet_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('wallet/new.html.twig', [
//            'wallet' => $wallet,
//            'form' => $form,
//        ]);
//    }

    #[Route('/show/{id}', name: 'app_wallet_show', methods: ['GET'])]
    public function show(Wallet $wallet): Response
    {
        return $this->render('api/admin/wallet/show.html.twig', [
            'wallet' => $wallet,
        ]);
    }

//    #[Route('/edit/{id}', name: 'app_wallet_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Wallet $wallet, EntityManagerInterface $entityManager): Response
//    {
//        $form = $this->createForm(WalletType::class, $wallet);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_wallet_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('wallet/edit.html.twig', [
//            'wallet' => $wallet,
//            'form' => $form,
//        ]);
//    }

//    #[Route('/{id}', name: 'app_wallet_delete', methods: ['POST'])]
//    public function delete(Request $request, Wallet $wallet, EntityManagerInterface $entityManager): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$wallet->getId(), $request->getPayload()->getString('_token'))) {
//            $entityManager->remove($wallet);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('app_wallet_index', [], Response::HTTP_SEE_OTHER);
//    }
}
