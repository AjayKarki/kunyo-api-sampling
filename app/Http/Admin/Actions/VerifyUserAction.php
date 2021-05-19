<?php

namespace App\Http\Controllers\Admin\Actions;

use Foundation\Resolvers\NotifyResolver;
use Illuminate\Http\Request;

/**
 * Class VerifyUserAction
 * @package App\Http\Controllers\Admin\Actions
 */
final class VerifyUserAction
{

    public function __invoke(Request $request)
    {
        app('db')
            ->table('users')
            ->whereIn('id', explode(',', $request->get('ids')))
            ->whereNull('email_verified_at')
            ->update([
                'phone_verified_at' => now(),
                'phone_is_verified' => 1,
            ]);

        $users = app('db')
            ->table('users')
            ->whereIn('id', explode(',', $request->get('ids')))->get();

        if ($users->isNotEmpty()) {
            foreach ($users as $player) {
                if ($player && $player->phone_is_verified && $player->phone_number) {
                    NotifyResolver::sentVerifiedSms(
                        $player->phone_number
                    );
                }
            }
        }

        flash('success', 'Records are verified successfully !');
        return redirect()->back();
    }

}
