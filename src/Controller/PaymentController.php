<?php


//namespace App\Controller;
//
//use App\Dtos\CreatePaymentRequestDTO;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\HttpFoundation\Response;

//#[Route('/api/payment')]
//class PaymentController extends AbstractController
//{
//    #[Route('/', name: 'api_payment_create', methods: ['POST'])]
//    public function create(CreatePaymentRequestDTO $requestDto): Response
//    {
//        // $requestDto — вже провалідований DTO, можна працювати
//        return $this->json(['status' => 'success']);
//    }
//}

namespace App\Controller;

use App\Dtos\CreatePaymentRequestDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;

#[Route('/api/payment', name: 'api_payment')]
final class PaymentController extends AbstractController
{
    public function __construct(private LoggerInterface $logger) {}

    #[Route('/', name: 'gateway', methods: ['POST'])]
    public function create(CreatePaymentRequestDTO $request): Response
    {
        return $this->json(['status' => 'success']);

        return $this->forward(
            controller: 'App\\Controller\\Internal\\TransactionInternalController::create',
            path: [
                'payload' => $request->getContent(),
            ]
        );
    }
}

//
//namespace App\Controller;
//
//use App\Dtos\CreatePaymentRequestDTO;
//use App\Repository\CryptoCurrencyRepository;
//use App\Repository\FiatCurrencyRepository;
//use App\Repository\NetworkRepository;
//use App\Repository\UserRepository;
//use App\Service\TransactionService;
//use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Attribute\Route;
//use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\Serializer\SerializerInterface;
//use Symfony\Component\Validator\Validator\ValidatorInterface;
//
//#[Route('/payment', name: 'api_payment', methods: ['POST'])]
//final class PaymentController extends AbstractController
//{
//    public function __construct(
//        private UserRepository $userRepo,
//        private FiatCurrencyRepository $fiatRepo,
//        private CryptoCurrencyRepository $cryptoRepo,
//        private NetworkRepository $networkRepo,
//        private EntityManagerInterface $em,
//        private SerializerInterface $serializer,
//        private ValidatorInterface $validator
//    )
//    {
//    }
//
//    #[Route('/', name: 'create')]
//    public function create(Request $request, TransactionService $processor): Response
//    {
//
//        try {
//            $dto = $this->serializer->deserialize(
//                $request->getContent(),
//                CreatePaymentRequestDTO::class,
//                'json'
//            );
//        } catch (\Throwable $e) {
//            return $this->json(['error' => 'Invalid JSON'], 400);
//        }
//
//        $errors = $this->validator->validate($dto);
//        if (count($errors) > 0) {
//            $errorMessages = [];
//            foreach ($errors as $violation) {
//                $errorMessages[$violation->getPropertyPath()] = $violation->getMessage();
//            }
//
//            return $this->json([
//                'status' => 'validation_error',
//                'errors' => $errorMessages
//            ], 422);
//        }
//
//        $data = json_decode($request->getContent(), true);
//
//        $shop = $this->userRepo->findOneBy(['name' => $data['name']]);
//        if (!$shop) {
//            return new JsonResponse(['error' => 'Shop not found'], 404);
//        }
//
//        $fiatcurrency = $this->fiatRepo->findOneBy(['code' => strtoupper($data['fiatcurrency'])]);
//        $cryptocurrency = $this->cryptoRepo->findOneBy(['code' => strtoupper($data['cryptocurrency'])]);
//        $network = $this->networkRepo->findOneBy(['name' => ucfirst(strtolower($data['network']))]);
//
//        if (!$fiatcurrency || !$cryptocurrency || !$network) {
//            return new JsonResponse(['error' => 'Currency or network not found'], 400);
//        }
//
//        $walletAddress = $processor->getWalletAddress($cryptocurrency->getCode(), $network->getCode());
//
//        $transaction = $processor->createTransaction($shop, $fiatcurrency, $cryptocurrency, $network, $data['amount'], $walletAddress);
//
//        $this->em->persist($transaction);
//        $this->em->flush();
//
//        return new JsonResponse([
//            'status' => 'ok',
//            'wallet_address' => $walletAddress,
//            'tx_id' => $transaction->getId(),
//        ]);
//    }
//}
