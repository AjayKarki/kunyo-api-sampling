<?php

namespace Modules\Application\Libs;

/**
 * Class Api
 * @package Modules\Application\Libs
 */
final class Api
{

    const CATEGORY = 'category';

    const CATEGORY_CHILDREN = 'children';

    const HOME_BANNER = 'banners';

    const HOME_NEW_ARRIVAL = 'new_arrival';

    const HOME_NEW_ARRIVAL_LIST = 'new_arrival_list';

    const HOME_SELLER_LIST = 'seller_list';

    const HOME_LATEST_TREND = 'latest_trend';

    const HOME_LATEST_TREND_LIST = 'latest_trend_list';

    const HOME_DEALS = 'deals';

    const HOME_BEST_SELLER = 'best_seller';

    const HOME_BEST_SELLER_LIST = 'best_seller_list';

    # Home Api Setting
    const HOME_API_CONTENT_TYPE_TOP_UP = 0; // TopUp Category
    const HOME_API_CONTENT_TYPE_GIFT_CARD = 1; // Gift Card Category
    const HOME_API_CONTENT_TYPE_COLLECTION = 2; // Collection of selected category

    public static function homeApiContentTypes(): array
    {
        return [
            Api::HOME_API_CONTENT_TYPE_TOP_UP       => 'Top Up Category',
            Api::HOME_API_CONTENT_TYPE_GIFT_CARD    => 'Gift Card Category',
            Api::HOME_API_CONTENT_TYPE_COLLECTION   => 'Collection',
        ];
    }
    # Home Api Setting - Search Container

    const HOME_API_SEARCH_DROPDOWN = 'search_drop_down';

    # Home Api Setting - Second Container
    const HOME_API_SECOND_CONTAINER_TITLE = 'second_container_title';
    const HOME_API_SECOND_CONTAINER_TITLE_VALUE = 'Game Top Up';

    const HOME_API_SECOND_CONTAINER_DESC = 'second_container_desc';
    const HOME_API_SECOND_CONTAINER_DESC_VALUE = 'You can find the latest Game Top Up as follow';

    const HOME_API_SECOND_CONTAINER_TYPE = 'second_container_type';
    const HOME_API_SECOND_CONTAINER_TYPE_VALUE = 0;

    const HOME_API_SECOND_CONTAINER_SELECTED_IDS = 'second_container_selected_ids';
    const HOME_API_SECOND_CONTAINER_SELECTED_IDS_VALUE = [];

    const HOME_API_SECOND_CONTAINER_LIMIT = 'second_container_limit';
    const HOME_API_SECOND_CONTAINER_LIMIT_VALUE = 8;

    # Home Api Setting - Third Container
    const HOME_API_THIRD_CONTAINER_TITLE = 'third_container_title';
    const HOME_API_THIRD_CONTAINER_TITLE_VALUE = 'Best Seller';

    const HOME_API_THIRD_CONTAINER_LIMIT = 'third_container_limit';
    const HOME_API_THIRD_CONTAINER_LIMIT_VALUE = 8;

    const HOME_API_THIRD_CONTAINER_PATTERN = 'third_container_pattern';
    const HOME_API_THIRD_CONTAINER_PATTERN_VALUE = 0;

    const HOME_API_THIRD_CONTAINER_PRODUCT_TYPES = 'third_container_product_types';
    const HOME_API_THIRD_CONTAINER_PRODUCT_TYPES_VALUE = [];

    # Home Api Setting - Fourth Container - LEFT
    const HOME_API_FOURTH_CONTAINER_LEFT_TITLE = 'fourth_left_container_title';
    const HOME_API_FOURTH_CONTAINER_LEFT_TITLE_VALUE = 'Best Seller';

    const HOME_API_FOURTH_CONTAINER_LEFT_LIMIT = 'fourth_left_container_limit';
    const HOME_API_FOURTH_CONTAINER_LEFT_LIMIT_VALUE = 5;

    const HOME_API_FOURTH_CONTAINER_LEFT_PATTERN = 'fourth_left_container_pattern';
    const HOME_API_FOURTH_CONTAINER_LEFT_PATTERN_VALUE = 0;

    const HOME_API_FOURTH_CONTAINER_LEFT_PRODUCT_TYPES = 'fourth_left_container_product_types';
    const HOME_API_FOURTH_CONTAINER_LEFT_PRODUCT_TYPES_VALUE = [];

    # Home Api Setting - Fourth Container - RIGHT

    const HOME_API_FOURTH_CONTAINER_RIGHT_TITLE = 'fourth_right_container_title';
    const HOME_API_FOURTH_CONTAINER_RIGHT_TITLE_VALUE = 'Recommended';

    const HOME_API_FOURTH_CONTAINER_RIGHT_LIMIT = 'fourth_right_container_limit';
    const HOME_API_FOURTH_CONTAINER_RIGHT_LIMIT_VALUE = 3;

    const HOME_API_FOURTH_CONTAINER_RIGHT_PATTERN = 'fourth_right_container_pattern';
    const HOME_API_FOURTH_CONTAINER_RIGHT_PATTERN_VALUE = 0;

    const HOME_API_FOURTH_CONTAINER_RIGHT_PRODUCT_TYPES = 'fourth_right_container_product_types';
    const HOME_API_FOURTH_CONTAINER_RIGHT_PRODUCT_TYPES_VALUE = [];

    # Home Api Setting - Fifth Container
    const HOME_API_FIFTH_CONTAINER_TITLE = 'fifth_container_title';
    const HOME_API_FIFTH_CONTAINER_TITLE_VALUE = 'Watch a Video';

    const HOME_API_FIFTH_CONTAINER_DESC = 'fifth_container_desc';
    const HOME_API_FIFTH_CONTAINER_DESC_VALUE = 'You can find the latest Game Top Up as follow';

    const HOME_API_FIFTH_CONTAINER_YOUTUBE_URI = 'fifth_container_youtube_url';
    const HOME_API_FIFTH_CONTAINER_YOUTUBE_URI_VALUE = 'https://www.youtube.com/embed/pOGyrqKvphc';

    # Home Api Setting - Sixth Container - LEFT
    const HOME_API_SIXTH_CONTAINER_LEFT_TITLE = 'sixth_left_container_title';
    const HOME_API_SIXTH_CONTAINER_LEFT_TITLE_VALUE = 'Hot Offer';

    const HOME_API_SIXTH_CONTAINER_LEFT_LIMIT = 'sixth_left_container_limit';
    const HOME_API_SIXTH_CONTAINER_LEFT_LIMIT_VALUE = 5;

    const HOME_API_SIXTH_CONTAINER_LEFT_PATTERN = 'sixth_left_container_pattern';
    const HOME_API_SIXTH_CONTAINER_LEFT_PATTERN_VALUE = 0;

    const HOME_API_SIXTH_CONTAINER_LEFT_PRODUCT_TYPES = 'sixth_left_container_product_types';
    const HOME_API_SIXTH_CONTAINER_LEFT_PRODUCT_TYPES_VALUE = [];

    # Home Api Setting - Sixth Container - RIGHT

    const HOME_API_SIXTH_CONTAINER_RIGHT_TITLE = 'sixth_right_container_title';
    const HOME_API_SIXTH_CONTAINER_RIGHT_TITLE_VALUE = 'Most Wishlisted';

    const HOME_API_SIXTH_CONTAINER_RIGHT_LIMIT = 'sixth_right_container_limit';
    const HOME_API_SIXTH_CONTAINER_RIGHT_LIMIT_VALUE = 3;

    const HOME_API_SIXTH_CONTAINER_RIGHT_PATTERN = 'sixth_right_container_pattern';
    const HOME_API_SIXTH_CONTAINER_RIGHT_PATTERN_VALUE = 0;

    const HOME_API_SIXTH_CONTAINER_RIGHT_PRODUCT_TYPES = 'sixth_right_container_product_types';
    const HOME_API_SIXTH_CONTAINER_RIGHT_PRODUCT_TYPES_VALUE = [];

    # OFFERS

    ## HEADER
    const OFFER_HEADER_TITLE                = 'offer_header_title';
    const OFFER_HEADER_SUB_TITLE            = 'offer_header_sub_title';
    const OFFER_HEADER_PRICE                = 'offer_header_price';
    const OFFER_HEADER_REDIRECT_TO          = 'offer_header_redirect_to';

    # SLIDER
    const OFFER_SLIDER_TITLE                = 'offer_slider_title';
    const OFFER_SLIDER_SUB_TITLE            = 'offer_slider_sub_title';
    const OFFER_SLIDER_BANNER               = 'offer_slider_banner';
    const OFFER_SLIDER_PRICE                = 'offer_slider_price';
    const OFFER_SLIDER_REDIRECT_TO          = 'offer_slider_redirect_to';

    # HEADER TOP
    const OFFER_SLIDER_AFTER_TITLE            = 'offer_slider_after_title';
    const OFFER_SLIDER_AFTER_SUB_TITLE        = 'offer_slider_after_sub_title';
    const OFFER_SLIDER_AFTER_PRICE            = 'offer_slider_after_price';
    const OFFER_SLIDER_AFTER_REDIRECT_TO      = 'offer_slider_after_redirect_to';
    const OFFER_SLIDER_AFTER_BUTTON_LEVEL     = 'offer_slider_after_button_level';

}
