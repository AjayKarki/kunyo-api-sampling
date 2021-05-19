<?php

namespace Modules\Application\Http\Controllers\Profile;

use Exception;
use Illuminate\Http\Response;
use Neputer\Supports\Mixins\Image;
use Modules\Application\Http\Controllers\BaseController;
use Illuminate\Database\DatabaseManager as DatabaseService;
use Modules\Application\Http\Requests\Profile\UpdateRequest;

/**
 * Class UpdateProfile
 * @package Modules\Application\Http\Controllers\Profile
 */
final class UpdateProfile extends BaseController
{

    use Image;

    /**
     * @var DatabaseService
     */
    private DatabaseService $database;

    /**
     * UpdateProfile constructor.
     *
     * @param DatabaseService $databaseService
     */
    public function __construct( DatabaseService $databaseService )
    {
        $this->database = $databaseService;
    }

    /**
     * @param UpdateRequest $request
     * @return mixed
     * @throws Exception|\Throwable
     */
    public function __invoke(UpdateRequest $request)
    {
        try {
            $this->database->beginTransaction();

            if ($user = $request->user()) {

                $data = array_filter($request->only([
                    'first_name',
                    'last_name',
                    'email',
                    'phone_number',
                    'password',
                ]));

                if ($request->hasFile('display_picture')) {
                    $image = $request->hasFile('display_picture') ?
                        $this->uploadImage($request->file('display_picture'), 'user', $user->image)
                        : $user->image;

                    $data = [
                        'image' => $image,
                    ];
                }

                $user->update($data);
            }

            $this->database->commit();

            return $this->responseOk(
                null,
                'You have updated the profile successfully.'
            );
        } catch (Exception $exception) {
            $this->database->rollBack();
            return $this->responseError(
                $exception->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'Internal Server Error !');
        }
    }

}
