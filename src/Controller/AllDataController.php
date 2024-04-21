<?php

namespace App\Controller;

use App\Repository\GameRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AllDataController extends AbstractController
{
    #[Route('/dashboard/all/data', name: 'app_all_data')]
    public function index(GameRepository $gameRepository): Response
    {
        $allData = $gameRepository->findAll();
        return $this->render('all_data/index.html.twig', [
            'allData' => $allData,
        ]);
    }

    #[Route('/export-all-data-excel', name: 'export_all_data_excel')]
    public function exportAllDataToExcel(GameRepository $gameRepository): Response
    {
        $allData = $gameRepository->findAll();

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
        $sheet->setCellValue('F1', 'Quantity Used');
        // Ajoutez d'autres colonnes selon vos besoins pour GiftQuantity
    
        // Ajouter les en-têtes pour Event
        $sheet->setCellValue('G1', 'Event ID');
        $sheet->setCellValue('H1', 'Message');
        $sheet->setCellValue('I1', 'Created At');
        // Ajoutez d'autres colonnes selon vos besoins pour Event
    
        // Remplir les données
        $row = 2;
        foreach ($allData as $data) {
            // Game data
            $sheet->setCellValue('A' . $row, $data->getUser());
            $sheet->setCellValue('B' . $row, $data->getNumber());
            $sheet->setCellValue('C' . $row, $data->getCityName());
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
