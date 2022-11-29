<?php

declare(strict_types=1);

namespace Meals\Application\Feature\Poll\UseCase\EmployeeCreatePollResult;

use DateTime;
use Meals\Application\Component\Provider\EmployeeProviderInterface;
use Meals\Application\Component\Provider\PollProviderInterface;
use Meals\Application\Component\Validator\Employee\EmployeeSelectCorrectDishAmountValidator;
use Meals\Application\Component\Validator\PollIsActiveValidator;
use Meals\Application\Component\Validator\UserHasAccessToCreatePollResultValidator;
use Meals\Application\Component\Validator\UserHasAccessToViewPollsValidator;
use Meals\Domain\Poll\PollResult;

class Interactor
{
    public function __construct(
        private EmployeeProviderInterface $employeeProvider,
        private PollProviderInterface $pollProvider,
        private UserHasAccessToViewPollsValidator $userHasAccessToPollsValidator,
        private PollIsActiveValidator $pollIsActiveValidator,
        private EmployeeSelectCorrectDishAmountValidator $employeeSelectCorrectDishAmountValidator,
        private UserHasAccessToCreatePollResultValidator $userHasAccessToCreatePollResult,
    )
    {
    }

    public function createPollResult(int $employeeId, int $pollId, DateTime $currentDay): PollResult
    {
        $this->userHasAccessToCreatePollResult->validate($currentDay);

        $employee = $this->employeeProvider->getEmployee($employeeId);
        $poll = $this->pollProvider->getPoll($pollId);

        $this->userHasAccessToPollsValidator->validate($employee->getUser());
        $this->pollIsActiveValidator->validate($poll);

        $dish = $this->employeeSelectCorrectDishAmountValidator->validate($poll);

        return new PollResult(
            (int)uniqid(),
            $poll,
            $employee,
            $dish,
            $employee->getFloor()
        );
    }
}
