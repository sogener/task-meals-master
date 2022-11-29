<?php

declare(strict_types=1);

namespace tests\Meals\Functional\Interactor;

use DateTime;
use Meals\Application\Component\Validator\Exception\AccessDeniedException;
use Meals\Application\Component\Validator\Exception\Menu\DishesIsAboveValidException;
use Meals\Application\Component\Validator\Exception\Menu\EmptyDishesInMenuException;
use Meals\Application\Component\Validator\Exception\PollIsNotActiveException;
use Meals\Application\Feature\Poll\UseCase\EmployeeCreatePollResult\Interactor;
use Meals\Domain\Dish\Dish;
use Meals\Domain\Dish\DishList;
use Meals\Domain\Employee\Employee;
use Meals\Domain\Menu\Menu;
use Meals\Domain\Poll\Poll;
use Meals\Domain\Poll\PollResult;
use Meals\Domain\User\Permission\Permission;
use Meals\Domain\User\Permission\PermissionList;
use Meals\Domain\User\User;
use tests\Meals\Functional\Fake\Provider\FakeEmployeeProvider;
use tests\Meals\Functional\Fake\Provider\FakePollProvider;
use tests\Meals\Functional\FunctionalTestCase;

class EmployeeCreatePollResultTest extends FunctionalTestCase
{
    public function testSuccessful()
    {
        $validCurrentDate = (new DateTime('2022-11-21'))->setTime(07, 0);

        $pollResult = $this->performTestMethod(
            $this->getEmployeeWithPermissions(),
            $this->getPoll(true),
            $validCurrentDate
        );

        verify($pollResult)->equals($pollResult);
    }

    private function performTestMethod(Employee $employee, Poll $poll, DateTime $currentDay): PollResult
    {
        $this->getContainer()->get(FakeEmployeeProvider::class)->setEmployee($employee);
        $this->getContainer()->get(FakePollProvider::class)->setPoll($poll);

        $pollResult = $this->getContainer()->get(Interactor::class)
            ->createPollResult($employee->getId(), $poll->getId(), $currentDay);

        $this->getContainer()->get(FakePollProvider::class)->setPollResult($pollResult);

        return $pollResult;
    }

    private function getEmployeeWithPermissions(): Employee
    {
        return new Employee(
            1,
            $this->getUserWithPermissions(),
            4,
            'Surname111111'
        );
    }

    private function getUserWithPermissions(): User
    {
        return new User(
            1,
            new PermissionList(
                [
                    new Permission(Permission::VIEW_ACTIVE_POLLS),
                ]
            ),
        );
    }

    private function getPoll(bool $active): Poll
    {
        return new Poll(
            1,
            $active,
            new Menu(
                1,
                'title',
                new DishList(
                    [
                        new Dish(
                            1,
                            'dish1',
                            'descr1'
                        )
                    ]
                ),
            )
        );
    }

    public function testEmployeeCreateWhenInvalidDate()
    {
        $this->expectException(AccessDeniedException::class);

        $invalidCurrentDate = (new DateTime('2022-11-22'))->setTime(07, 0);

        $poll = $this->performTestMethod(
            $this->getEmployeeWithPermissions(),
            $this->getPoll(true),
            $invalidCurrentDate
        );

        verify($poll)->equals($poll);
    }

    public function testEmployeeSelectEmptyMenu()
    {
        $this->expectException(EmptyDishesInMenuException::class);

        $validCurrentDate = (new DateTime('2022-11-21'))->setTime(07, 0);

        $poll = $this->performTestMethod(
            $this->getEmployeeWithPermissions(),
            $this->getPollWithEmptyMenu(true),
            $validCurrentDate
        );

        verify($poll)->equals($poll);
    }

    private function getPollWithEmptyMenu(bool $active): Poll
    {
        return new Poll(
            1,
            $active,
            new Menu(
                1,
                'title',
                new DishList([]),
            )
        );
    }

    public function testEmployeeSelectDishesMoreThanOneInMenu()
    {
        $this->expectException(DishesIsAboveValidException::class);

        $validCurrentDate = (new DateTime('2022-11-21'))->setTime(07, 0);

        $poll = $this->performTestMethod(
            $this->getEmployeeWithPermissions(),
            $this->getPollWithSelectedDishesMoreThanOne(true),
            $validCurrentDate
        );

        verify($poll)->equals($poll);
    }

    private function getPollWithSelectedDishesMoreThanOne(bool $active): Poll
    {
        return new Poll(
            1,
            $active,
            new Menu(
                1,
                'title',
                new DishList(
                    [
                        new Dish(
                            1,
                            'dish1',
                            'descr1'
                        ),
                        new Dish(
                            2,
                            'dish2',
                            'descr2'
                        ),
                    ]
                ),
            )
        );
    }

    public function testUserHasNotPermissions()
    {
        $this->expectException(AccessDeniedException::class);

        $validCurrentDate = (new DateTime('2022-11-21'))->setTime(07, 0);

        $poll = $this->performTestMethod(
            $this->getEmployeeWithNoPermissions(),
            $this->getPoll(true),
            $validCurrentDate
        );

        verify($poll)->equals($poll);
    }

    private function getEmployeeWithNoPermissions(): Employee
    {
        return new Employee(
            1,
            $this->getUserWithNoPermissions(),
            4,
            'Surname'
        );
    }

    private function getUserWithNoPermissions(): User
    {
        return new User(
            1,
            new PermissionList([]),
        );
    }

    public function testPollIsNotActive()
    {
        $this->expectException(PollIsNotActiveException::class);

        $validCurrentDate = (new DateTime('2022-11-21'))->setTime(07, 0);

        $poll = $this->performTestMethod(
            $this->getEmployeeWithPermissions(),
            $this->getPoll(false),
            $validCurrentDate
        );

        verify($poll)->equals($poll);
    }
}
