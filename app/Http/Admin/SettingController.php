<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Foundation\Lib\Cache;
use Foundation\Lib\Category as CategoryType;
use Foundation\Lib\Product;
use Foundation\Services\CategoryService;
use Foundation\Services\CollectionService;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\Setting;
use Modules\Application\Libs\Api;
use Neputer\Supports\BaseController;
use Neputer\Supports\Mixins\Image;
use Foundation\Requests\Setting\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\SettingService;

/**
 * Class SettingController
 * @package App\Http\Controllers\Admin
 */
class SettingController extends BaseController
{
    use Image;

    /**
     * The SettingService instance
     *
     * @var $settingService
     */
    private $settingService;

    private $folder = 'setting';

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }


    /**
     * Get the Settings Page with the data in the edit form
     *
     * @return Factory|View
     */
    public function edit()
    {
        $data = [];
        $data['settings'] = $this->settingService->getSettings();
        $data['categories-collection'] = $this->resolveCategoriesCollection(Arr::get( $data['settings'], 'home-api'));
        $data['dropdown-categories'] = $this->resolveDropDownCategories(Arr::get( $data['settings'], 'home-api.search_drop_down'));
        $data['products'] = Product::getTypes();
        $data['patterns'] = Product::getPatterns();
        return view('admin.setting.edit', compact('data'));
    }

    /**
     * Update the website settings
     *
     * @param UpdateRequest $request
     * @return \Illuminate\Http\JsonResponse|RedirectResponse
     * @throws Exception
     */
    public function update(UpdateRequest $request)
    {
        $imageName = $this->settingService->pluckToArray();
        $image = $imageName['logo'] ?? null;
        $menuImage = $imageName['menu_image'] ?? null;
        $paymentImage = $imageName['whole_payment_image'] ?? null;
        $imePayImage = $imageName['ime_pay_image'] ?? null;
        $khaltiImage = $imageName['khalti_image'] ?? null;
        $prabhuPayImage = $imageName['prabhupay_image'] ?? null;
        $esewaImage = $imageName['esewa_image'] ?? null;
        $bannerSideImage = $imageName['home-api']['offers'][Api::OFFER_SLIDER_BANNER] ?? null;
        $nicAsiaImage = $imageName['nicasia_image'] ?? null;

        if ($request->hasFile('photo')) {
            $image = $this->uploadImage($request->file('photo'), $this->folder, $image);
        }
        if ($request->hasFile('image')) {
            $menuImage = $this->uploadImage($request->file('image'), $this->folder, $menuImage);
        }
        if ($request->hasFile('image_payment')) {
            $paymentImage = $this->uploadImage($request->file('image_payment'), $this->folder, $paymentImage);
        }
        if ($request->hasFile('image_ime_pay')) {
            $imePayImage = $this->uploadImage($request->file('image_ime_pay'), $this->folder, $imePayImage);
        }
        if ($request->hasFile('image_khalti')) {
            $khaltiImage = $this->uploadImage($request->file('image_khalti'), $this->folder, $khaltiImage);
        }
        if ($request->hasFile('image_prabhupay')) {
            $prabhuPayImage = $this->uploadImage($request->file('image_prabhupay'), $this->folder, $prabhuPayImage);
        }
        if ($request->hasFile('image_esewa')) {
            $esewaImage = $this->uploadImage($request->file('image_esewa'), $this->folder, $esewaImage);
        }

        if ($request->hasFile('offer_photo')) {
            $bannerSideImage = $this->uploadImage($request->file('offer_photo'), $this->folder, $bannerSideImage);
        }

        if ($request->hasFile('image_nicasia')) {
            $nicAsiaImage = $this->uploadImage($request->file('image_nicasia'), $this->folder, $nicAsiaImage);
        }

        $this->settingService->update($request->merge([
            'logo'                  => $image,
            'menu_image'            => $menuImage,
            'whole_payment_image'   => $paymentImage,
            'ime_pay_image'         => $imePayImage,
            'khalti_image'          => $khaltiImage,
            'prabhupay_image'       => $prabhuPayImage,
            'esewa_image'           => $esewaImage,
            'nicasia_image'         => $nicAsiaImage,
            'home-api' => json_encode($this->resolveHomeApiData($request, $bannerSideImage)),
        ])->except(
            '_token', 'photo', 'image', 'image_ime_pay',
            'image_khalti', 'image_prabhupay', 'image_esewa', 'image_nicasia'
        ));

        Cache::clear();

        if($request->ajax()) {
            return response()->json(['msg' => 'Setting Updated']);
        }

        flash('success', 'Record successfully updated.');
        return redirect()->route('admin.setting.edit');
    }

    private function resolveCategoriesCollection($data)
    {
        $type = Arr::get($data, Api::HOME_API_SECOND_CONTAINER_TYPE);
        $selectedIDs = Arr::get($data, Api::HOME_API_SECOND_CONTAINER_SELECTED_IDS, []);

        if ($type == Api::HOME_API_CONTENT_TYPE_COLLECTION) {
           return app(CollectionService::class)
                ->query()
                ->select('name', 'id')
                ->whereIntegerInRaw('id', $selectedIDs)
                ->limit(10)
               ->pluck('name', 'id');
        } else {
            return app(CategoryService::class)
                ->query()
                ->select('category_name as name', 'id')
                ->where('type', $type ? CategoryType::TYPE_GIFT_CARD_CATEGORY : CategoryType::TYPE_IN_GAME_TOP_UP_CATEGORY)
                ->whereIntegerInRaw('id', $selectedIDs)
                ->limit(10)
                ->pluck('name', 'id');
        }
    }

    private function resolveHomeApiData($request, $image): array
    {
        $data = $request->only(
            Api::HOME_API_SEARCH_DROPDOWN,
            Api::HOME_API_SECOND_CONTAINER_TITLE,
            Api::HOME_API_SECOND_CONTAINER_DESC,
            Api::HOME_API_SECOND_CONTAINER_TYPE,
            Api::HOME_API_SECOND_CONTAINER_SELECTED_IDS,
            Api::HOME_API_SECOND_CONTAINER_LIMIT,

            Api::HOME_API_THIRD_CONTAINER_TITLE,
            Api::HOME_API_THIRD_CONTAINER_LIMIT,
            Api::HOME_API_THIRD_CONTAINER_PATTERN,
            Api::HOME_API_THIRD_CONTAINER_PRODUCT_TYPES,

            Api::HOME_API_FOURTH_CONTAINER_LEFT_TITLE,
            Api::HOME_API_FOURTH_CONTAINER_LEFT_LIMIT,
            Api::HOME_API_FOURTH_CONTAINER_LEFT_PATTERN,
            Api::HOME_API_FOURTH_CONTAINER_LEFT_PRODUCT_TYPES,
            Api::HOME_API_FOURTH_CONTAINER_RIGHT_TITLE,
            Api::HOME_API_FOURTH_CONTAINER_RIGHT_LIMIT,
            Api::HOME_API_FOURTH_CONTAINER_RIGHT_PATTERN,
            Api::HOME_API_FOURTH_CONTAINER_RIGHT_PRODUCT_TYPES,

            Api::HOME_API_FIFTH_CONTAINER_TITLE,
            Api::HOME_API_FIFTH_CONTAINER_DESC,
            Api::HOME_API_FIFTH_CONTAINER_YOUTUBE_URI,

            Api::HOME_API_SIXTH_CONTAINER_LEFT_TITLE,
            Api::HOME_API_SIXTH_CONTAINER_LEFT_LIMIT,
            Api::HOME_API_SIXTH_CONTAINER_LEFT_PATTERN,
            Api::HOME_API_SIXTH_CONTAINER_LEFT_PRODUCT_TYPES,
            Api::HOME_API_SIXTH_CONTAINER_RIGHT_TITLE,
            Api::HOME_API_SIXTH_CONTAINER_RIGHT_LIMIT,
            Api::HOME_API_SIXTH_CONTAINER_RIGHT_PATTERN,
            Api::HOME_API_SIXTH_CONTAINER_RIGHT_PRODUCT_TYPES,
        );

        $offers = $request->only(
            Api::OFFER_HEADER_TITLE,
            Api::OFFER_HEADER_SUB_TITLE,
            Api::OFFER_HEADER_PRICE,
            Api::OFFER_HEADER_REDIRECT_TO,

            Api::OFFER_SLIDER_TITLE,
            Api::OFFER_SLIDER_SUB_TITLE,
            Api::OFFER_SLIDER_PRICE,
            Api::OFFER_SLIDER_REDIRECT_TO,

            Api::OFFER_SLIDER_AFTER_TITLE,
            Api::OFFER_SLIDER_AFTER_SUB_TITLE,
            Api::OFFER_SLIDER_AFTER_PRICE,
            Api::OFFER_SLIDER_AFTER_REDIRECT_TO,
            Api::OFFER_SLIDER_AFTER_BUTTON_LEVEL,
        );

        $offers[Api::OFFER_SLIDER_BANNER] = $image;

        return array_merge($data, [
            'offers' => $offers,
        ]);
    }

    private function resolveDropDownCategories( $selectedIDs = [])
    {
        return app(CategoryService::class)
            ->query()
            ->select('category_name as name', 'id')
            ->whereIntegerInRaw('id', $selectedIDs ?? [])
            ->limit(10)
            ->pluck('name', 'id');
    }
}
