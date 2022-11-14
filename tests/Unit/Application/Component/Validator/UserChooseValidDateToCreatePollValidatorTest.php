<?php

declare(strict_types=1);

namespace tests\Meals\Unit\Application\Component\Validator;

use DateTime;
use Meals\Application\Component\Validator\Exception\AccessDeniedException;
use Meals\Application\Component\Validator\UserHasAccessToCreatePollResultValidator;
use Meals\Domain\Poll\Service\DateService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class UserChooseValidDateToCreatePollValidatorTest extends TestCase
{
    use ProphecyTrait;

    public function testSuccessful()
    {
        $dateService = new DateService();

        $validCurrentDate = (new DateTime('2022-11-21'))->setTime(07, 0);

        $validator = new UserHasAccessToCreatePollResultValidator($dateService);
        verify($validator->validate($validCurrentDate))->null();
    }

    public function testFail()
    {
        $this->expectException(AccessDeniedException::class);

        $dateService = new DateService();

        $invalidCurrentDate = (new DateTime('2022-11-16'))->setTime(07, 0);

        $validator = new UserHasAccessToCreatePollResultValidator($dateService);
        $validator->validate($invalidCurrentDate);
    }
}
