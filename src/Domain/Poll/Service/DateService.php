<?php

namespace Meals\Domain\Poll\Service;

use DateInterval;
use DateTime;

class DateService
{
    /**
     * Opening time in selected days.
     *
     * @var int
     */
    private int $startHour = 6;
    /**
     * Closing time in selected days.
     *
     * @var int
     */
    private int $endHour = 22;

    /**
     * Current date of this day.
     *
     * @var DateTime
     */
    private DateTime $currentDate;

    /**
     * Selected days.
     *
     * @var array
     */
    private array $chooseDate = [];


    /**
     * Returns current date.
     *
     * @param DateTime $date
     * @return DateTime
     */
    public function now(DateTime $date): DateTime
    {
        $this->currentDate = $date;

        return $this->currentDate;
    }

    /**
     * Returns days when we can create PollResult.
     * In scope one month.
     *
     * @param string $day
     * @return array
     */
    public function makeValidDaysInMonth(string $day): array
    {
        $date = new DateTime("first {$day} of this month");
        $thisMonth = $date->format('m');

        while ($date->format('m') === $thisMonth) {
            $startDate = (clone $date)->add(new DateInterval("PT{$this->startHour}H"));
            $endDate = (clone $date)->add(new DateInterval("PT{$this->endHour}H"));

            $this->chooseDate[$startDate->getTimestamp()] = $endDate->getTimestamp();

            $date->modify("next {$day}");
        }

        return $this->chooseDate;
    }
}