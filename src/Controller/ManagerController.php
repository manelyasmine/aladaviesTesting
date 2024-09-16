<?php
namespace App\Controller;

use App\Repository\DemandesCongesRepository;
use App\Repository\EmployesRepository;
use App\Repository\ManagersRepository; // Assuming this repository exists
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
class ManagerController extends AbstractController
{
    #[Route('/manager/leave-requests/{demandeCongesId}', name: 'app_manager_update_leave_request', methods: ['PUT'])]
    public function updateLeaveRequest(
        Request $request,
        EntityManagerInterface $em,
        DemandesCongesRepository $demandesCongesRepository,
        EmployesRepository $employesRepository,
        ManagersRepository $managersRepository,
         
    ): Response
    {
       
       $demandeCongesId = intval($request->get('demandeCongesId'));
       $data = json_decode($request->getContent(), true);
        
        $managerId = $data['managerId'];
   // Check if the authenticated user is a manager
   if ( !$managersRepository->find($managerId)) {
    return new JsonResponse([
        'status' => 'error',
        'message' => 'Unauthorized. You are not a manager.'
    ], 403);
}

// Fetch the leave request by ID
$leaveRequest = $demandesCongesRepository->find($demandeCongesId);
if (!$leaveRequest) {
    return new JsonResponse([
        'status' => 'error',
        'message' => 'Leave request not found.'
    ], 404);
}

// Fetch the employee associated with the leave request
$employeeId = $leaveRequest->getEmployeId();
$employee = $employesRepository->find($employeeId);

if (!$employee) {
    return new JsonResponse([
        'status' => 'error',
        'message' => 'Employee not found.'
    ], 404);
}

// Check if the employee is managed by the current manager
if ($employee->getManagerId() !== $managerId) {
    return new JsonResponse([
        'status' => 'error',
        'message' => 'You are not authorized to modify this leave request.'
    ], 403);
}

// Get the action and comment from the request
$data = json_decode($request->getContent(), true);
$action = $data['action'] ?? null; // 'approve' or 'reject'
$comment = $data['comment'] ?? '';

if (!$action || !in_array($action, ['approve', 'reject'])) {
    return new JsonResponse([
        'status' => 'error',
        'message' => 'Invalid action specified.'
    ], 400);
}

// Update the leave request status
if ($action === 'approve') {
    $leaveRequest->setStatut('approved');
} elseif ($action === 'reject') {
    if (empty($comment)) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'A comment is required to reject a leave request.'
        ], 400);
    }
    $leaveRequest->setStatut('rejected');
    $leaveRequest->setCommentaire($comment);
}

// Save the updated leave request 
$em->persist($leaveRequest);
$em->flush();
       


        return new JsonResponse([
            'status' => 'success',
            'message' => 'Leave request updated successfully.'
        ], 200);
    }





    #[Route('/manager/{id}/leave-requests',  
    name: 'app_manager_leave_requests', methods: ['GET'])]
    public function getLeaveRequests(Request $request, 
    DemandesCongesRepository $demandesCongesRepository): JsonResponse
{
       // Extract the manager ID from the request path
       $managerRequest = $request->get('id');
       $data = json_decode($request->getContent(), true);

       
       // Get the authenticated user's maanger ID assuming is the one from request body
       $managerIdUser = $data['managerId'];
 
       // Validate the employee ID
       if (!$managerRequest || !is_numeric($managerRequest)) {
           return new JsonResponse(['error' => 'Invalid employee ID'], 400);
       }
   
       // Check if the employee IDs match
       if (intval($managerRequest) !== $managerIdUser) {
           return new JsonResponse(['error' => 'You are not authorized to view this employee leave requests'], 403);
       }
   
       // Retrieve the leave requests for the specified employee
       $leaveRequests = $demandesCongesRepository->findBy(['manager_id' => $managerRequest]);
   

    // Prepare the response data
    $data = [];
    foreach ($leaveRequests as $leaveRequest) {
        $data[] = [
            'id' => $leaveRequest->getId(),
            'startDate' => $leaveRequest->getDateDebut()->format('Y-m-d'),
            'endDate' => $leaveRequest->getEndDate()->format('Y-m-d'),
            'status' => $leaveRequest->getStatut(),
            'comment' => $leaveRequest->getCommentaire(),
        ];
    }

    return new JsonResponse($data, 200);
}
}
