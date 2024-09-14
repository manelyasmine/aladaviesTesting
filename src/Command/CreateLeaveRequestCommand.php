<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\LeaveRequestService;

class CreateLeaveRequestCommand extends Command
{
    protected static $defaultName = 'app:create-leave-request';

    private $leaveRequestService;

    public function __construct(LeaveRequestService $leaveRequestService)
    {
        parent::__construct();
        $this->leaveRequestService = $leaveRequestService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create a new leave request')
            ->addArgument('startDate', InputArgument::REQUIRED, 'Start date of the leave request')
            ->addArgument('endDate', InputArgument::REQUIRED, 'End date of the leave request')
            ->addArgument('employeeId', InputArgument::REQUIRED, 'Employee ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startDate = new \DateTimeImmutable($input->getArgument('startDate'));
        $endDate = new \DateTimeImmutable($input->getArgument('endDate'));
        $employeeId = $input->getArgument('employeeId');

        $this->leaveRequestService->createLeaveRequest($startDate, $endDate, $employeeId);

        $output->writeln('Leave request created successfully.');

        return Command::SUCCESS;
    }
}




 