<?php
namespace App\Controller;

use App\Entity\DemandesConges;

use App\Repository\EmployesRepository;
use App\Form\DemandeCongeType;
use App\Repository\DemandesCongesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeController extends AbstractController
{
    #[Route('/leave-requests', name: 'app_employe_demandes', methods: ['GET'])]
    public function getDemandesConge(Request $request, DemandesCongesRepository $demandesCongesRepository): Response
    {
        // Get employee ID from query parameters
        
        $employeId = $request->query->get('employeId');
         

        if (!$employeId) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Employee ID is required.'
            ], 400);
        }

        // Fetch leave requests specific to the provided employee ID
        $demandes = $demandesCongesRepository->findBy(['employe_id' => $employeId]);

        // Transform data to include relevant fields
        $formattedDemandes = array_map(function ($demande) {
            return [
                'id' => $demande->getId(),
                'startDate' => $demande->getDateDebut()->format('Y-m-d'),
                'endDate' => $demande->getEndDate()->format('Y-m-d'),
                'comment' => $demande->getCommentaire(),
                'status' => $demande->getStatut(),
            ];
        }, $demandes);

        return $this->json($formattedDemandes, 200);
    }

    #[Route('/leave-requests', name: 'app_employe_create', methods: ['POST'])]
    public function createLeaveRequest(
        Request $request, 
        EntityManagerInterface $em, 
        DemandesCongesRepository $demandesCongesRepository, 
        EmployesRepository $employeRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $employeId = $data['employeId'] ?? null;
        $startDate = isset($data['startDate']) ? new \DateTime($data['startDate']) : null;
        $endDate = isset($data['endDate']) ? new \DateTime($data['endDate']) : null;
        $comment = $data['comment'] ?? '';

        // Check if the employee exists in the employe table
        $employe = $employeRepository->find($employeId);
        if (!$employe) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Employee not found.'
            ], 404);
        }
          // Ensure the comment is provided
     if (empty($comment)) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Comment is required.'
        ], 400);
    }
       // Check that startDate is before endDate
       if ($startDate >= $endDate) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Start date must be before end date.'
        ], 400);
    }

        // Validate if the employee already has a leave request during the same period
        $existingRequests = $demandesCongesRepository->findOverlapRequests($employeId, $startDate, $endDate);

        if (!empty($existingRequests)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'You already have a leave request during this period.'
            ], 400);
        }

        // Create a new LeaveRequest entity
        $leaveRequest = new DemandesConges();
        $leaveRequest->setEmployeId($employeId);
        $leaveRequest->setDateDebut($startDate);
        $leaveRequest->setEndDate($endDate);
        $leaveRequest->setCommentaire($comment);
        $leaveRequest->setStatut('pending');
 
        // Save to database
        $em->persist($leaveRequest);
        $em->flush();

        return new JsonResponse([
            'status' => 'Leave Request created',
            'id' => $leaveRequest->getId()
        ], 201);
    }
}
