<?php
namespace App\Service;

use App\Entity\LeaveRequest;
use App\Repository\DemandesCongesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
class LeaveRequestService
{
    private $demandesCongesRepository;

    public function __construct(DemandesCongesRepository $demandesCongesRepository)
    {
        $this->demandesCongesRepository = $demandesCongesRepository;
    }

   /*  public function createLeaveRequest(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate, int $employeeId): void
    {
        
         
 
        $leaveRequest = new DemandeConges();
        $leaveRequest->setStartDate($startDate);
        $leaveRequest->setEndDate($endDate);
        $leaveRequest->setEmployeeId($employeeId);

        $this->demandeCongesRepository->save($leaveRequest, true);
    } */

    public function getLeaveRequestsByUser(int $employeeId): array
    {
        $leaveRequests = $this->demandesCongesRepository->findByEmployeeId($employeeId);
    
        // Return the leave requests directly as an array
        return $leaveRequests;
    }
}