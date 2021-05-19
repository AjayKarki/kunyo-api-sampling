<?php

namespace App\Http\Controllers\Admin\Actions;

use Foundation\Models\User;
use Foundation\Resolvers\NotifyResolver;

/**
 * Class VerifyCustomerAction
 * @package App\Http\Controllers\Admin\Actions
 */
class VerifyCustomerAction
{

    public function __invoke(User $customer)
    {
        if ($customer) {
            $verified = $customer->update([
                'phone_verified_at' => now(),
                'phone_is_verified' => 1,
            ]);

            if ($verified) {
                NotifyResolver::sentVerifiedSms(
                    $customer->phone_number
                );
            }

            flash('success', $customer->getFullName().' is successfully verified.');
        } else {
            abort(404);
        }
        return back();
    }

}
