<?php

declare(strict_types=1);

namespace Modules\Application\Http\DTOs\User;

use Carbon\Carbon;
use Foundation\Lib\Verify;
use Foundation\Models\User;
use Foundation\Resolvers\Otp\VerifyUser;
use Spatie\DataTransferObject\DataTransferObject;

final class UserData extends DataTransferObject
{

//    public int $id;

    public string $character_id;

    public ?string $image;

    public int $is_sms_enabled;

    public int $is_email_enabled;

    public string $email;

    public string $first_name;

    public ?string $middle_name;

    public string $last_name;

    public ?string $full_name;

    public ?string $phone_number;

    public int $status;

    public ?Carbon $last_login;

    public int $is_deactivated;

    public int $views;

    // public ?Carbon $email_verified_at;

    public int $is_verified;

    // public ?Carbon $phone_verified_at;

    public int $phone_is_verified;

//    public int $can_see_pickers;

//    public ?string $password;

//    public ?string $remember_token;

//    public ?Carbon $deleted_at;

    public ?Carbon $created_at;

    public ?Carbon $updated_at;

    public ?int $email_tries;
    public ?int $phone_tries;

    public static function fromModel(User $user): DataTransferObject
    {
        return new self([
            'character_id'          => $user->character_id,
            'image'                 => $user->getProfilePicture(),
            'is_sms_enabled'        => $user->is_sms_enabled,
            'is_email_enabled'      => $user->is_email_enabled,
            'email'                 => $user->email,
            'first_name'            => $user->first_name,
            'middle_name'           => $user->middle_name,
            'last_name'             => $user->last_name,
            'full_name'             => $user->getFullName(),
            'phone_number'          => $user->phone_number,
            'status'                => $user->status,
            'last_login'            => $user->last_login,
            'is_deactivated'        => $user->is_deactivated,
            'views'                 => $user->views,
            'is_verified'           => $user->is_verified,
            'phone_is_verified'     => $user->phone_is_verified,
            'email_tries'           => Verify::MAX_TRIES - VerifyUser::getEmailTried(auth()->id()),
            'phone_tries'           => Verify::MAX_TRIES - VerifyUser::getSmsTried(auth()->id()),
        ]);
    }

}
