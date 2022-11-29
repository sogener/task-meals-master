<?php

declare(strict_types=1);

namespace Meals\Application\Component\Provider;

use Meals\Domain\Poll\Poll;
use Meals\Domain\Poll\PollList;
use Meals\Domain\Poll\PollResult;

interface PollProviderInterface
{
    public function getActivePolls(): PollList;

    public function getPoll(int $pollId): Poll;

    public function setPollResult(PollResult $pollResult): void;
}
