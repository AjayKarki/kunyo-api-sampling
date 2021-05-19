<?php

namespace App\Http\Controllers\Admin;

use Foundation\Lib\GiftCard;
use Foundation\Resolvers\Product\GiftCardPatternResolver;
use Foundation\Services\GiftCardService;
use Illuminate\Http\Request;
use Neputer\Supports\BaseController;

/**
 * Class EasyGiftCardCodeAssign
 * @package App\Http\Controllers\Admin
 */
final class EasyGiftCardCodeAssign extends BaseController
{

    /**
     * @var GiftCardService
     */
    private $giftCardService;

    /**
     * EasyGiftCardCodeAssign constructor.
     *
     * @param GiftCardService $giftCardService
     */
    public function __construct( GiftCardService  $giftCardService)
    {
        $this->giftCardService = $giftCardService;
    }

    public function view()
    {
        $data['patterns'] = GiftCard::patterns();
        return view('admin.gift-cards.utility.view', compact('data'));
    }
 
    public function assign(Request $request)
    {
        // check code duplication
        $duplicateCodes = [];

        $giftCardId = $request->get('gift_card');
        $codes = GiftCardPatternResolver::resolve($request->get('pattern'), $request->get('codes'));

        if (! empty($codes)) {

            foreach ($codes as $code) {
                if ($code) {

                    $checkCode = app('db')
                        ->table('gift_cards_codes')
                        ->where('codes', $code)
                        ->value('codes');

                    if (is_null($checkCode)) {
                        $this->giftCardService->insertCodes([
                            'codes' => $code,
                        ], $giftCardId);
                    } else {
                        $duplicateCodes[] = $code;  
                    }
                    
                }
            }
        }
        flash('success', 'Codes assigned successfully!');

        $giftCard = app('db')
                ->table('gift_cards')->where('id', $giftCardId)->first();
        $data['patterns'] = GiftCard::patterns();

        return view('admin.gift-cards.utility.view', compact('data', 'duplicateCodes', 'giftCard'));
    }

}
