<?php

namespace Modules\Application\Http\Controllers\Product;

use Foundation\Lib\Meta;
use Foundation\Lib\Product;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Modules\Application\Http\DTOs\Product\GiftCard\GiftCardCollection;
use Modules\Application\Http\DTOs\Product\TopUp\TopUpCollection;
use Neputer\Supports\Utility;
use Foundation\Services\TopUpService;
use Foundation\Services\GiftCardService;
use Modules\Application\Http\Controllers\BaseController;

final class ProductListByPattern extends BaseController
{

    /**
     * @var GiftCardService
     */
    private GiftCardService $giftCardService;

    /**
     * @var TopUpService
     */
    private TopUpService $topUpService;

    const CONTAINER_THIRD = 'third';

    const CONTAINER_FOURTH_LEFT = 'fourth-left';

    const CONTAINER_FOURTH_RIGHT = 'fourth-right';

    const CONTAINER_SIXTH_LEFT = 'sixth-left';

    const CONTAINER_SIXTH_RIGHT = 'sixth-right';

    public function __construct(
        GiftCardService $giftCardService,
        TopUpService $topUpService
    )
    {
        $this->giftCardService = $giftCardService;
        $this->topUpService = $topUpService;
    }

    public function __invoke(Request $request)
    {
        $data = [];

        $filter = $this->resolvePattern($request->get('container'));

        // @TODO
        if (isset(Arr::get($filter, 'types')[0])) {
            if (Arr::get($filter, 'types')[0] == Product::PRODUCT_TOP_UP_INDEX) {
                $data = TopUpCollection::fromArray($this->getTopUp(
                    Arr::get($filter, 'pattern'),
                    Arr::get($filter, 'limit')
                )->all())->items();
            } else {
                $data = GiftCardCollection::fromArray($this->getGiftCard(
                    Arr::get($filter, 'pattern'),
                    Arr::get($filter, 'limit')
                )->all())->items();
            }
        }

        return $this->responseOk(
            $data
        );
    }

    private function resolvePattern($container): array
    {
        $resolvedPatterns = [];

        $homeSetting = Meta::get('home-api');
        if (Utility::isJson($homeSetting)) {
            $setting = json_decode($homeSetting, 1);

            switch ($container) {
                case ProductListByPattern::CONTAINER_THIRD:
                    $resolvedPatterns = [
                        'limit'       => Arr::get($setting, 'third_container_limit'),
                        'pattern'     => Arr::get($setting, 'third_container_pattern'),
                        'types'       => Arr::get($setting, 'third_container_product_types'),
                    ];
                    break;
                case ProductListByPattern::CONTAINER_FOURTH_LEFT:
                    $resolvedPatterns = [
                        'limit'       => Arr::get($setting, 'fourth_left_container_limit'),
                        'pattern'     => Arr::get($setting, 'fourth_left_container_pattern'),
                        'types'       => Arr::get($setting, 'fourth_left_container_product_types'),
                    ];
                    break;
                case ProductListByPattern::CONTAINER_FOURTH_RIGHT:
                    $resolvedPatterns = [
                        'limit'       => Arr::get($setting, 'fourth_right_container_limit'),
                        'pattern'     => Arr::get($setting, 'fourth_right_container_pattern'),
                        'types'       => Arr::get($setting, 'fourth_right_container_product_types'),
                    ];
                    break;
                case ProductListByPattern::CONTAINER_SIXTH_LEFT:
                    $resolvedPatterns = [
                        'limit'       => Arr::get($setting, 'sixth_left_container_limit'),
                        'pattern'     => Arr::get($setting, 'sixth_left_container_pattern'),
                        'types'       => Arr::get($setting, 'sixth_left_container_product_types'),
                    ];
                    break;
                case ProductListByPattern::CONTAINER_SIXTH_RIGHT:
                    $resolvedPatterns = [
                        'limit'       => Arr::get($setting, 'sixth_right_container_limit'),
                        'pattern'     => Arr::get($setting, 'sixth_right_container_pattern'),
                        'types'       => Arr::get($setting, 'sixth_right_container_product_types'),
                    ];
                    break;
            }

            return $resolvedPatterns;
        }

        return $resolvedPatterns;
    }

    private function getTopUp($pattern, $limit)
    {
        return $this->topUpService->items($limit);
    }

    private function getGiftCard($pattern, $limit)
    {
        return $this->giftCardService->items($limit);
    }

}
