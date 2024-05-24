<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(GameRepository $gameRepository, Request $request,PaginatorInterface $paginator): Response
    {
        // Récupérer les dernières données reçues du mobile pour l'utilisateur authentifié
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        $placeFilter = $request->query->get('place'); 
        
        $latestData = $gameRepository->findLatestDataLast24Hours($placeFilter);
    
        $page = $request->query->get('page', 1);
        $perPage = 3;
        $pagination = $paginator->paginate($latestData, $page,$perPage);
        
        if (!$pagination) {
            $this->addFlash('error', 'Le lieu spécifié n\'existe pas dans la base de données.');
        }
    
        return $this->render('dashboard/index.html.twig', [
            'latestData' => $pagination,
        ]);
    }
    #[Route('/dashboard/game/{id}', name: 'game_show')]
    public function show(Game $game): Response
    {
        return $this->render('dashboard/show.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/export-dashboard-excel', name: 'export_dashboard_excel')]
    public function exportToExcel(Request $request, GameRepository $gameRepository): Response
    {
        $user = $this->getUser();
        $placeFilter = $request->query->get('place'); // Récupérer le paramètre 'place' depuis l'URL
        $latestData = $gameRepository->findLatestDataLast24Hours($placeFilter);
        if (!$latestData) {
            $this->addFlash('error', 'Le lieu spécifié n\'existe pas.');
            return $this->redirectToRoute('app_dashboard');
        }
    
        // Créer une nouvelle feuille de calcul
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Ajouter les en-têtes pour Game
        $sheet->setCellValue('A1', 'User');
        $sheet->setCellValue('B1', 'Number');
        $sheet->setCellValue('C1', 'cityName');
        // Ajoutez d'autres colonnes selon vos besoins pour Game
    
        // Ajouter les en-têtes pour GiftQuantity
        $sheet->setCellValue('D1', 'Gift Name');
        $sheet->setCellValue('E1', 'Initial Quantity');
        $sheet->setCellValue('F1', 'Quantity Left');
        // Ajoutez d'autres colonnes selon vos besoins pour GiftQuantity
    
        // Ajouter les en-têtes pour Event
        $sheet->setCellValue('G1', 'Event ID');
        $sheet->setCellValue('H1', 'Message');
        $sheet->setCellValue('I1', 'Created At');
        // Ajoutez d'autres colonnes selon vos besoins pour Event
    
        // Remplir les données
        $row = 2;
        foreach ($latestData as $data) {
            // Game data
            $sheet->setCellValue('A' . $row, $data->getUser());
            $sheet->setCellValue('B' . $row, $data->getNumber());
            $sheet->setCellValue('C' . $row, $data->getcityName());
            // Ajoutez d'autres colonnes selon vos besoins pour Game
    
            // GiftQuantity data
            foreach ($data->getGifts() as $giftQuantity) {
                $sheet->setCellValue('D' . $row, $giftQuantity->getName()); // Utilisez getGiftLabel() au lieu de getId()
                $sheet->setCellValue('E' . $row, $giftQuantity->getInitialQuantity());
                $sheet->setCellValue('F' . $row, $giftQuantity->getQuantityUsed());
                // Ajoutez d'autres colonnes selon vos besoins pour GiftQuantity
            }
    
            // Event data
            foreach ($data->getEvents() as $event) {
                $sheet->setCellValue('G' . $row, $event->getId());
                $sheet->setCellValue('H' . $row, $event->getText());
                $sheet->setCellValue('I' . $row, $event->getCreatedAt()->format('Y-m-d H:i:s'));
                // Ajoutez d'autres colonnes selon vos besoins pour Event
                $row++; // assurez-vous d'incrémenter le compteur de ligne à l'intérieur de la boucle des événements
            }
        }
    
        // Enregistrez le fichier Excel sur le serveur
        $excelFilename = 'export_dashboard_' . date('Ymd_His') . '.xlsx';
        $excelFilePath = $this->getParameter('kernel.project_dir') . '/public/excel_exports/' . $excelFilename;
    
        if (!is_dir(dirname($excelFilePath))) {
            mkdir(dirname($excelFilePath), 0777, true);
        }
    
        $writer = new Xlsx($spreadsheet);
        $writer->save($excelFilePath);
    
        // Créer une réponse pour le fichier Excel
        $response = new BinaryFileResponse($excelFilePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $excelFilename);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    
        return $response;
    }   
}
