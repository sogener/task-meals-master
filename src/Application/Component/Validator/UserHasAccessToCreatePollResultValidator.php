<?php

declare(strict_types=1);

namespace Meals\Application\Component\Validator;

use DateTime;
use Meals\Application\Component\Validator\Exception\AccessDeniedException;
use Meals\Domain\Poll\Service\DateService;

class UserHasAccessToCreatePollResultValidator
{
    public function __construct(private DateService $dateService)
    {
    }

    public function validate(DateTime $currentDay): void
    {
        $currentDay = $this->dateService->now($currentDay);

        $validDates = $this->dateService->makeValidDaysInMonth('Monday');
        $currentDayIsValid = false;

        foreach ($validDates as $start => $end) {
//            If the current date is between a valid date.
            if ($currentDay->getTimestamp() > $start && $currentDay->getTimestamp() < $end) {
                $currentDayIsValid = true;
            }
        }

        if (!$currentDayIsValid) {
            throw new AccessDeniedException('Cannot create PollRequest at this moment.');
        }
    }
}
