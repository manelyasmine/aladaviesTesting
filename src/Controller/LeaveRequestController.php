<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\LeaveRequestService;

class LeaveRequestController extends AbstractController
{
    private $leaveRequestService;

    public function __construct(LeaveRequestService $leaveRequestService)
    {
        $this->leaveRequestService = $leaveRequestService;
    }

    #[Route('/leave-requests', name: 'app_leave_request_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $employeeId = 1;  
        $leaveRequests = $this->leaveRequestService->getLeaveRequestsByUser($employeeId);
        $leaves=[];
        foreach ($leaveRequests as $leaveRequest) {
            
           $leaves[]= [$leaveRequest->getId(),
           $leaveRequest->getDateDebut()];
        }
    
return new JsonResponse(['count' => count($leaveRequests), 'leaveRequests' => $leaves]);

    }
}
