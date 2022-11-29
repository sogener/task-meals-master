<?php

declare(strict_types=1);

namespace Meals\Application\Component\Validator\Employee;

use Meals\Application\Component\Validator\Exception\Menu\DishesIsAboveValidException;
use Meals\Application\Component\Validator\Exception\Menu\EmptyDishesInMenuException;
use Meals\Domain\Dish\Dish;
use Meals\Domain\Poll\Poll;

class EmployeeSelectCorrectDishAmountValidator
{
    public function validate(Poll $poll): Dish
    {
        $dishesInMenu = $poll->getMenu()->getDishes();

        if (empty($dishesInMenu->getDishes())) {
            throw new EmptyDishesInMenuException('Dishes in menu cannot be empty.');
        }

        if ($dishesInMenu->hasNext()) {
            throw new DishesIsAboveValidException('Dishes is more than one');
        }

        return $dishesInMenu->first();
    }
}
