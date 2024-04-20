<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Entity\Event;
use App\Entity\Participant;
use App\Entity\GiftQuantity;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiUserDashboardController extends AbstractController
{
    #[Route('/api/user-dashboard', name: 'app_api_user_dashboard')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'JWT authentication is successful! Welcome to dashboard!',
            'path' => 'src/Controller/Api/ApiUserDashboardController.php',
        ]);
    }

    #[Route('/api/data', name: 'api_data')]
    public function receiveMobileData(
    Request $request,
    SerializerInterface $serializer,
    EntityManagerInterface $entityManager,
    ValidatorInterface $validator,
    Security $security,
    GameRepository $gameRepository
    )
    {
        // Récupérer l'utilisateur authentifié
        $user = $security->getUser();

        // Vérifier si l'utilisateur est authentifié
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié.'], 401);
        }

        // Vérifier si les données sont de type JSON
        if ($request->getContentType() !== 'json' || !$this->isJson($request->getContent())) {
            return new JsonResponse(['error' => 'Les données doivent être au format JSON.'], 400);
        }

        // Récupérer les données JSON de la requête
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        // Vérifier si l'utilisateur dans les données correspond à l'utilisateur authentifié
        if (isset($data['user']) && $data['user'] !== $user->getId()) {
            return new JsonResponse(['error' => 'Utilisateur non autorisé à soumettre ces données.'], 403);
        }
        // Désérialiser les entités
        $game = $serializer->deserialize($jsonData, Game::class, 'json');

        $gameId = isset($data['gameId']) ? $data['gameId'] : null;
        $gameData = $gameRepository->findById($gameId);
        
        if ($gameData) 
           {
            foreach ($gameData[0]->getGifts() as $giftQuantityData) {
                $entityManager->remove($giftQuantityData); 
            }
            $gameData[0]->getGifts()->clear();

            foreach ($data['giftQuantities'] as $giftQuantityData) {
                $giftQuantity = $serializer->deserialize(json_encode($giftQuantityData), GiftQuantity::class, 'json');
                $gameData[0]->addGift($giftQuantity);
            }

            foreach ($gameData[0]->getEvents() as $eventData) {
                $entityManager->remove($eventData);
            }
            $gameData[0]->getEvents()->clear();

            foreach ($data['events'] as $eventData) {
                $event = $serializer->deserialize(json_encode($eventData), Event::class, 'json');
                $gameData[0]->addEvent($event);
                
            }
            foreach ($gameData[0]->getParticipant() as $participantData) {
                $entityManager->remove($participantData);
            }
            $gameData[0]->getParticipant()->clear();

            foreach ($data['participant'] as $participantData) {
                $participant = $serializer->deserialize(json_encode($participantData), Participant::class, 'json');
                $gameData[0]->addparticipant($participant);
                
            }
            $tombolaWinner = $serializer->deserialize(json_encode($data['tombolaWinner']), Participant::class, 'json');
            if ($tombolaWinner) {
                $gameData[0]->setTombolaWinner($tombolaWinner);
                $tombolaWinner->setGame($gameData[0]);

            }
            $entityManager->persist($gameData[0]);
            $entityManager->flush();
            return new JsonResponse(['message' => 'Données enregistrées avec succès',
                    'gameId'=>$gameData[0]->getId(),
        ]);
           
         }
        else 
        {

        // Associer le jeu à l'utilisateur authentifié
        $game->setUser($user);
        // Ajouter les instances de GiftQuantity à la collection gifts
        foreach ($data['giftQuantities'] as $giftQuantityData) {
            $giftQuantity = $serializer->deserialize(json_encode($giftQuantityData), GiftQuantity::class, 'json');
            $game->addGift($giftQuantity);
        }

        // Valider les entités avec le Validator
        $errors = $validator->validate($game);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['error' => 'Données invalides.', 'details' => $errorMessages], 400);
        }

        // Enregistrer l'entité Game dans la base de données
        $entityManager->persist($game);
        $entityManager->flush();
    }
        // Répondre avec un message de réussite
        return new JsonResponse(['message' => 'Données enregistrées avec succès',
                    'gameId'=>$game->getId(),
    ]);
    }


    /**
     * Vérifie si data est un JSON valide.
     */
    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}