<?php 
namespace App\Controller;
use App\Entity\DemandesConges;
use App\Repository\DemandesCongesRepository;
use App\Repository\EmployesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class EmployeController extends AbstractController
{
    #[Route('/employes/leave-requests', name: 'app_employee_create_leave_request', methods: ['POST'])]
    public function createLeaveRequest(
        Request $request,
        EntityManagerInterface $em,
        DemandesCongesRepository $demandesCongesRepository,
        EmployesRepository $employesRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Extract employee ID from the request data
        $employeeId = $data['employeeId'];

        // Extract request data
        $startDate = isset($data['startDate']) ? new \DateTime($data['startDate']) : null;
        $endDate = isset($data['endDate']) ? new \DateTime($data['endDate']) : null;
        $comment = $data['comment'] ?? '';

        if (!$startDate || !$endDate || $startDate > $endDate) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid dates. Ensure that start date is before end date.'
            ], 400);
        }

        // Check if the employee exists
        $employee = $employesRepository->find($employeeId);
        if (!$employee) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Employee not found.'
            ], 404);
        }

        // Check for overlapping requests
        $existingRequests = $demandesCongesRepository->findOverlapRequests($employeeId, $startDate, $endDate);
        if (!empty($existingRequests)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'You already have a leave request during this period.'
            ], 400);
        }

        // Create and save the leave request
        $leaveRequest = new DemandesConges();
        $leaveRequest->setEmployeId($employeeId);
        $leaveRequest->setDateDebut($startDate);
        $leaveRequest->setEndDate($endDate);
        $leaveRequest->setCommentaire($comment);
        $leaveRequest->setStatut('pending'); // Default status

        $em->persist($leaveRequest);
        $em->flush();

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Leave request created successfully.',
            'id' => $leaveRequest->getId()
        ], 201);
    }

    #[Route('/employes/{id}/leave-requests',  
    name: 'app_employee_leave_requests', methods: ['GET'])]
    public function getLeaveRequests(Request $request, DemandesCongesRepository $demandesCongesRepository): JsonResponse
{
       // Extract the employee ID from the request path
       $employeeIdRequest = $request->get('id');
       $data = json_decode($request->getContent(), true);

       
       // Get the authenticated user's employee ID assuming is the one from request body
       $employeeIdUser = $data['employeeId'];
 
       // Validate the employee ID
       if (!$employeeIdRequest || !is_numeric($employeeIdRequest)) {
           return new JsonResponse(['error' => 'Invalid employee ID'], 400);
       }
   
       // Check if the employee IDs match
       if (intval($employeeIdRequest) !== $employeeIdUser) {
           return new JsonResponse(['error' => 'You are not authorized to view this employee leave requests'], 403);
       }
   
       // Retrieve the leave requests for the specified employee
       $leaveRequests = $demandesCongesRepository->findBy(['employe_id' => $employeeIdRequest]);
   

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
