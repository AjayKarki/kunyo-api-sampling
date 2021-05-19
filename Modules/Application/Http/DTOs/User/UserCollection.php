<?php

declare(strict_types=1);

namespace Modules\Application\Http\DTOs\User;

use Foundation\Models\User;
use Spatie\DataTransferObject\DataTransferObjectCollection;

final class UserCollection extends DataTransferObjectCollection
{

    public function current(): UserData
    {
        return parent::current();
    }

    /**
     * @param  User[]  $data
     * @return UserCollection
     */
    public static function fromArray(array $data): UserCollection
    {
        return new UserCollection(
            array_map(fn (User $item) => UserData::fromModel($item), $data)
        );
    }

}
