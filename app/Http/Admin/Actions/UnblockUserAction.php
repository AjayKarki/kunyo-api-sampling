<?php

namespace App\Http\Controllers\Admin\Actions;

use Illuminate\Http\Request;
use Foundation\Resolvers\NotifyResolver;

/**
 * Class UnblockUserAction
 * @package App\Http\Controllers\Admin\Actions
 */
class UnblockUserAction
{

    public function __invoke(Request $request)
    {
        app('db')
            ->table('users')
            ->whereIn('id', explode(',', $request->get('ids')))
            ->where('is_deactivated', 1)
            ->update([
                'is_deactivated' => 0,
            ]);

        app('db')
            ->table('user_verifications')
            ->whereIn('user_id', explode(',', $request->get('ids')))
            ->where('tries', 3)
            ->update([
                'tries' => 0,
            ]);

        app('db')
            ->table('user_sms_verifications')
            ->whereIn('user_id', explode(',', $request->get('ids')))
            ->where('tries', 3)
            ->update([
                'tries' => 0,
            ]);

//        $users = app('db')
//            ->table('users')
//            ->whereIn('id', explode(',', $request->get('ids')))->get();

//        if ($users->isNotEmpty()) {
//            foreach ($users as $player) {
//                if ($player && $player->phone_is_verified && $player->phone_number) {
//                    NotifyResolver::sentVerifiedSms(
//                        $player->phone_number
//                    );
//                }
//            }
//        }

        flash('success', 'Records are unblocked successfully !');
        return redirect()->back();
    }

}
