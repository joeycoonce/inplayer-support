<?php

namespace SocialPiranha\InPlayerSupport\Tests\Feature;

use SocialPiranha\InPlayerSupport\Services\InPlayerService;
use SocialPiranha\InPlayerSupport\Services\Resources\AssetResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\Request;
use SocialPiranha\InPlayerSupport\Services\DataObjects\Asset;
use SocialPiranha\InPlayerSupport\Services\DataObjects\AccessFee;
use SocialPiranha\InPlayerSupport\Services\Requests\CreateAssetRequest;
use SocialPiranha\InPlayerSupport\Services\Requests\AccessFeeRequest;
use Carbon\Carbon;
use Tests\TestCase;

class InPlayerServiceTest extends TestCase
{
    public function test_can_build_new_inplayer_service()
    {
        $this->assertInstanceOf(
            expected: InPlayerService::class, 
            actual: new InPlayerService(
                url: Str::random(),
                client_id: Str::random(),
                client_secret: Str::random(),
                merchant_uuid: Str::random(),
                merchant_password: Str::random(),
            ),
        );
    }

    public function test_can_create_pending_request()
    {
        $service = new InPlayerService(
            url: Str::random(),
            client_id: Str::random(),
            client_secret: Str::random(),
            merchant_uuid: Str::random(),
            merchant_password: Str::random(),
        );

        $this->assertInstanceOf(
            expected: PendingRequest::class,
            actual: $service->makeRequest(),
        );
    }

    public function test_can_resolve_inplayer_service_from_the_container()
    {
        $this->assertInstanceOf(
            expected: InPlayerService::class,
            actual: resolve(InPlayerService::class),
        );
    }
 
    public function test_can_create_pending_request_from_resolved_service()
    {    
        $this->assertInstanceOf(
            expected: PendingRequest::class,
            actual: resolve(InPlayerService::class)->makeRequest(),
        );
    }

    public function test_resolves_as_singleton_only()
    {
        $service = resolve(InPlayerService::class);
    
        $this->assertEquals(resolve(InPlayerService::class), $service);
    }

    public function test_can_get_access_token()
    {
        $this->fakeAuth();

        $inPlayer = resolve(InPlayerService::class);

        $accessToken = $inPlayer->accessToken();

        $this->assertIsString(
            actual: $accessToken,
        );
        $this->assertEquals(
            expected: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c',
            actual: $accessToken,
        );
    }

    public function test_can_get_cached_access_token()
    {
        $this->fakeAuth();

        $inPlayer = resolve(InPlayerService::class);

        $this->assertEmpty(Cache::get('inplayer.access_token'));

        $accessToken = $inPlayer->accessToken();
        $cachedAccessToken = Cache::get('inplayer.access_token');

        $this->assertIsString(
            actual: $cachedAccessToken,
        );
        $this->assertEquals(
            expected: $accessToken,
            actual: $cachedAccessToken,
        );
    }

    public function test_can_get_asset_resource()
    {
        $inPlayer = resolve(InPlayerService::class);

        $this->assertInstanceOf(
            expected: AssetResource::class,
            actual: $inPlayer->assets(),
        );
    }

    public function test_can_create_asset()
    {
        $this->fakeHttp();
        
        $inPlayer = resolve(InPlayerService::class);
        $asset = $inPlayer->assets()->createAsset(
            requestBody: new CreateAssetRequest(
                item_type: 'inplayer_asset',
                title: 'Foo bar',
                metadata: ['foo' => 'bar'],
                external_asset_id: 'eaeEa521edAx',
                access_control_type_id: 3,
                event_type: 'live',
            ),
        );

        $this->assertInstanceOf(
            expected: Asset::class,
            actual: $asset,
        );
        $this->assertEquals(
            expected: 'Foo bar',
            actual: $asset->title,
        );
        $this->assertEquals(
            expected: 33,
            actual: $asset->id,
        );
    }

    public function test_can_get_asset()
    {
        $this->fakeHttp();
        
        $inPlayer = resolve(InPlayerService::class);
        $asset = $inPlayer->assets()->asset(
            id: 33
        );

        $this->assertInstanceOf(
            expected: Asset::class,
            actual: $asset,
        );
        $this->assertEquals(
            expected: 33,
            actual: $asset->id,
        );
    }

    public function test_can_update_asset()
    {
        $this->fakeHttp();
        
        $inPlayer = resolve(InPlayerService::class);
        $asset = $inPlayer->assets()->updateAsset(
            id: 33,
            data: [
                'title' => 'Foo bar too',
            ]
        );

        $this->assertInstanceOf(
            expected: Asset::class,
            actual: $asset,
        );
        $this->assertEquals(
            expected: 'Foo bar too',
            actual: $asset->title,
        );
        $this->assertEquals(
            expected: 33,
            actual: $asset->id,
        );
    }

    public function test_can_create_access_fee()
    {
        $this->fakeHttp();
        
        $inPlayer = resolve(InPlayerService::class);
        $accessFee = $inPlayer->assets()->createAccessFee(
            id: 33,
            requestBody: new AccessFeeRequest(
                access_type_id: 1,
                amount: 2,
                currency: 'EUR',
                description: 'Simple Access Fee',
                starts_at: Carbon::parse('2019-11-12T11:45:26.371Z'),
                expires_at: Carbon::parse('2019-11-12T11:45:26.371Z'),
                trial_period_description: 'Bla',
                trial_period_quantity: 1,
                trial_period_period: 'week',
                setup_fee_amount: 3,
                setup_fee_description: '3.00$ setup fee',
                country_set_id: 2,
            ),
        );

        $this->assertInstanceOf(
            expected: AccessFee::class,
            actual: $accessFee,
        );
        $this->assertEquals(
            expected: 44,
            actual: $accessFee->id,
        );
    }

    public function test_can_get_access_fee()
    {
        $this->fakeHttp();
        
        $inPlayer = resolve(InPlayerService::class);
        $accessFee = $inPlayer->assets()->accessFee(
            id: 33,
            fee_id: 44
        );

        $this->assertInstanceOf(
            expected: AccessFee::class,
            actual: $accessFee,
        );
        $this->assertEquals(
            expected: 44,
            actual: $accessFee->id,
        );
    }

    public function test_can_get_access_fees()
    {
        $this->fakeHttp();
        
        $inPlayer = resolve(InPlayerService::class);
        $accessFees = $inPlayer->assets()->accessFees(
            id: 33,
        );

        $this->assertInstanceOf(
            expected: Collection::class,
            actual: $accessFees,
        );
        $this->assertInstanceOf(
            expected: AccessFee::class,
            actual: $accessFees->first(),
        );
        $this->assertEquals(
            expected: 44,
            actual: $accessFees->first()->id,
        );
    }

    public function test_can_update_access_fee()
    {
        $this->fakeHttp();
        
        $inPlayer = resolve(InPlayerService::class);
        $accessFee = $inPlayer->assets()->updateAccessFee(
            id: 33,
            fee_id: 44,
            requestBody: new AccessFeeRequest(
                access_type_id: 1,
                amount: 2,
                currency: 'EUR',
                description: 'Simple Access Fee',
                starts_at: Carbon::parse('2019-11-12T11:45:26.371Z'),
                expires_at: Carbon::parse('2019-11-12T11:45:26.371Z'),
                trial_period_description: 'Bla',
                trial_period_quantity: 1,
                trial_period_period: 'week',
                setup_fee_amount: 3,
                setup_fee_description: '3.00$ setup fee',
                country_set_id: 2,
            ),
        );

        $this->assertInstanceOf(
            expected: AccessFee::class,
            actual: $accessFee,
        );
        $this->assertEquals(
            expected: 44,
            actual: $accessFee->id,
        );
    }

    private function fakeHttp()
    {
        $this->fakeAuth();
        $this->fakeAssets();
    }

    private function fakeAuth()
    {
        InPlayerService::fake([
            // fake auth
            'https://staging-v2.inplayer.com/accounts/authenticate' => function (Request $request) {
                if ($request->method() == 'POST')
                {
                    return Http::response(
                        body: [
                            'access_token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c',
                            'refresh_token' => 'swKbldciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adWAdzCa',
                                'account' => [
                                'id' => 21,
                                'uuid' => '528b1b80-5868-4abc-a9b6-4d3455d719c8',
                                'merchant_uuid' => '528b1b80-5868-4abc-a9b6-4d3455d719c8',
                                'email' => 'example-email@inplayer.com',
                                'full_name' => 'John Doe',
                                'referrer' => 'https://inplayer.com',
                                'metadata' => [],
                                'social_apps_metadata' => [],
                                'roles' => [],
                                'completed' => true,
                                'date_of_birth' => 1531482438,
                                'created_at' => 1531482438,
                                'updated_at' => 1531482438,
                                'expired_at' => 1531482438,
                            ],
                        ],
                        status: 200,
                    );
                }
                else
                {
                    return Http::response(
                        body: [
                            'code' => 403,
                            'message' => 'Invalid credentials',
                        ],
                        status: 403,
                    );
                }
            },
        ]);
    }

    private function fakeAssets()
    {
        InPlayerService::fake([
            // fake create asset
            'https://staging-v2.inplayer.com/v2/items' => function (Request $request) {
                if ($request->method() == 'POST')
                {
                    return Http::response(
                        body: [
                            'id' => 33,
                            'merchant_id' => 21,
                            'merchant_uuid' => 'e5ac2013-8d10-42ba-abb5-291c5201cea8',
                            'active' => true,
                            'title' => 'Foo bar',
                            'access_control_type' => [
                                'id' => 4,
                                'name' => 'Paid',
                                'auth' => true,
                            ],
                            'item_type' => [
                                'id' => 44,
                                'name' => 'brightcove_asset',
                                'content_type' => 'ovp',
                                'host' => 'inplayer',
                                'description' => 'OVP asset',
                            ],
                            'age_restriction' => [
                                'min_age' => 18,
                            ],
                            'metahash' => [
                                'property1' => 'string',
                                'property2' => 'string',
                            ],
                            'created_at' => 1531482438,
                            'updated_at' => 1531482438,
                            'template_id' => 1,
                            'is_giftable' => true,
                            'gift_metadata' => [
                                'id' => 4,
                                'item_id' => 4,
                                'description' => 'Paid',
                                'created_at' => 1543238500,
                                'updated_at' => 1543238500,
                            ],
                        ],
                        status: 201,
                    );
                }
                else
                {
                    return Http::response(
                        body: [
                            'code' => 403,
                            'message' => 'Invalid privileges',
                        ],
                        status: 403,
                    );
                }
            },
            // fake get access fees, create access fee
            'https://staging-v2.inplayer.com/v2/items/*/access-fees' => function (Request $request) {
                if ($request->method() == 'GET')
                {
                    return Http::response(
                        body: [
                            [
                            'voucher_rule' => [
                                    'id' => 8926,
                                    'rule_type' => 'access-fees',
                                    'value' => 8115,
                                    'voucher' => [
                                        'id' => 14,
                                        'name' => '50% discount',
                                        'discount' => 50,
                                        'rebill_discount' => 50,
                                        'start_date' => '2018-08-23T00:00:00.000Z',
                                        'end_date' => '2018-09-30T00:00:00.000Z',
                                        'code' => 'F00B4R!@',
                                        'usage_limit' => 10,
                                        'usage_counter' => 10,
                                        'discount_period' => 'once',
                                        'discount_duration' => 5,
                                    ],
                                ],
                                'id' => 44,
                                'merchant_id' => 21,
                                'amount' => 5,
                                'currency' => 'USD',
                                'description' => '5.00$ for 1 hour access',
                                'item' => [
                                    'content' => 'The asset\'s content as stringified JSON object',
                                    'id' => 33,
                                    'merchant_id' => 21,
                                    'merchant_uuid' => 'e5ac2013-8d10-42ba-abb5-291c5201cea8',
                                    'active' => true,
                                    'title' => 'Foo bar',
                                    'access_control_type' => [
                                        'id' => 4,
                                        'name' => 'Paid',
                                        'auth' => true,
                                    ],
                                    'item_type' => [
                                        'id' => 44,
                                        'name' => 'brightcove_asset',
                                        'content_type' => 'ovp',
                                        'host' => 'inplayer',
                                        'description' => 'OVP asset',
                                    ],
                                    'age_restriction' => [
                                        'min_age' => 18,
                                    ],
                                    'metahash' => [
                                        'property1' => 'string',
                                        'property2' => 'string',
                                    ],
                                    'created_at' => 1531482438,
                                    'updated_at' => 1531482438,
                                    'template_id' => 1,
                                    'is_giftable' => true,
                                    'gift_metadata' => [
                                        'id' => 4,
                                        'item_id' => 4,
                                        'description' => 'Paid',
                                        'created_at' => 1543238500,
                                        'updated_at' => 1543238500,
                                    ],
                                ],
                                'access_type' => [
                                    'account_id' => 44,
                                    'id' => 44,
                                    'name' => 'ppv',
                                    'quantity' => 2,
                                    'period' => 'hour',
                                    'created_at' => 1531482438,
                                    'updated_at' => 1531482438,
                                ],
                                'trial_period' => [
                                    'id' => 44,
                                    'quantity' => 2,
                                    'period' => 'hour',
                                    'description' => '2 hour access',
                                    'created_at' => 1531482438,
                                    'updated_at' => 1531482438,
                                ],
                                'setup_fee' => [
                                    'id' => 44,
                                    'fee_amount' => 3,
                                    'description' => '3.00$ setup fee',
                                    'created_at' => 1531482438,
                                    'updated_at' => 1531482438,
                                ],
                                'geo_restriction' => [
                                    'id' => 4,
                                    'country_iso' => 'US',
                                    'country_set_id' => 4,
                                    'type' => 'blacklist',
                                ],
                                'seasonal_fee' => [
                                    'off_season_access' => true,
                                    'current_price_amount' => 4.2,
                                    'anchor_date' => 1531482438,
                                ],
                                'expires_at' => 1531482438,
                                'starts_at' => 1531482438,
                                'created_at' => 1531482438,
                                'updated_at' => 1531482438,
                                'external_fees' => [
                                    'id' => 44,
                                    'payment_provider_id' => 12,
                                    'access_fee_id' => 17,
                                    'external_id' => 55,
                                    'merchant_id' => 13,
                                ],
                            ],
                        ],
                        status: 200,
                    );
                }
                else if ($request->method() == 'POST')
                {
                    return Http::response(
                        body: [
                            'id' => 44,
                            'merchant_id' => 21,
                            'amount' => 5,
                            'currency' => 'USD',
                            'description' => '5.00$ for 1 hour access',
                            'item' => [
                                'content' => 'The asset\'s content as stringified JSON object',
                                'id' => 33,
                                'merchant_id' => 21,
                                'merchant_uuid' => 'e5ac2013-8d10-42ba-abb5-291c5201cea8',
                                'active' => true,
                                'title' => 'Foo bar',
                                'access_control_type' => [
                                    'id' => 4,
                                    'name' => 'Paid',
                                    'auth' => true,
                                ],
                                'item_type' => [
                                    'id' => 44,
                                    'name' => 'brightcove_asset',
                                    'content_type' => 'ovp',
                                    'host' => 'inplayer',
                                    'description' => 'OVP asset',
                                ],
                                'age_restriction' => [
                                    'min_age' => 18,
                                ],
                                'metahash' => [
                                    'property1' => 'string',
                                    'property2' => 'string',
                                ],
                                'created_at' => 1531482438,
                                'updated_at' => 1531482438,
                                'template_id' => 1,
                                'is_giftable' => true,
                                'gift_metadata' => [
                                    'id' => 4,
                                    'item_id' => 4,
                                    'description' => 'Paid',
                                    'created_at' => 1543238500,
                                    'updated_at' => 1543238500,
                                ],
                            ],
                            'access_type' => [
                                'account_id' => 44,
                                'id' => 44,
                                'name' => 'ppv',
                                'quantity' => 2,
                                'period' => 'hour',
                                'created_at' => 1531482438,
                                'updated_at' => 1531482438,
                            ],
                            'trial_period' => [
                                'id' => 44,
                                'quantity' => 2,
                                'period' => 'hour',
                                'description' => '2 hour access',
                                'created_at' => 1531482438,
                                'updated_at' => 1531482438,
                            ],
                            'setup_fee' => [
                                'id' => 44,
                                'fee_amount' => 3,
                                'description' => '3.00$ setup fee',
                                'created_at' => 1531482438,
                                'updated_at' => 1531482438,
                            ],
                            'geo_restriction' => [
                                'id' => 4,
                                'country_iso' => 'US',
                                'country_set_id' => 4,
                                'type' => 'blacklist',
                            ],
                            'seasonal_fee' => [
                                'off_season_access' => true,
                                'current_price_amount' => 4.2,
                                'anchor_date' => 1531482438,
                            ],
                            'expires_at' => 1531482438,
                            'starts_at' => 1531482438,
                            'created_at' => 1531482438,
                            'updated_at' => 1531482438,
                            'external_fees' => [
                                'id' => 44,
                                'payment_provider_id' => 12,
                                'access_fee_id' => 17,
                                'external_id' => 55,
                                'merchant_id' => 13,
                            ],
                        ],
                        status: 201,
                    );
                }
                else
                {
                    return Http::response(
                        body: [
                            'code' => 404,
                            'message' => 'Fee not found',
                        ],
                        status: 404,
                    );
                }
            },
            // fake get single access fee
            'https://staging-v2.inplayer.com/v2/items/*/access-fees/*' => function (Request $request) {
                if ($request->method() == 'GET')
                {
                    return Http::response(
                        body: [
                            'id' => 44,
                            'merchant_id' => 21,
                            'amount' => 5,
                            'currency' => 'USD',
                            'description' => '5.00$ for 1 hour access',
                            'item' => [
                                'content' => 'The asset\'s content as stringified JSON object',
                                'id' => 33,
                                'merchant_id' => 21,
                                'merchant_uuid' => 'e5ac2013-8d10-42ba-abb5-291c5201cea8',
                                'active' => true,
                                'title' => 'Foo bar',
                                'access_control_type' => [
                                    'id' => 4,
                                    'name' => 'Paid',
                                    'auth' => true,
                                ],
                                'item_type' => [
                                    'id' => 44,
                                    'name' => 'brightcove_asset',
                                    'content_type' => 'ovp',
                                    'host' => 'inplayer',
                                    'description' => 'OVP asset',
                                ],
                                'age_restriction' => [
                                    'min_age' => 18,
                                ],
                                'metahash' => [
                                    'property1' => 'string',
                                    'property2' => 'string',
                                ],
                                'created_at' => 1531482438,
                                'updated_at' => 1531482438,
                                'template_id' => 1,
                                'is_giftable' => true,
                                'gift_metadata' => [
                                    'id' => 4,
                                    'item_id' => 4,
                                    'description' => 'Paid',
                                    'created_at' => 1543238500,
                                    'updated_at' => 1543238500,
                                ],
                            ],
                            'access_type' => [
                                'account_id' => 44,
                                'id' => 44,
                                'name' => 'ppv',
                                'quantity' => 2,
                                'period' => 'hour',
                                'created_at' => 1531482438,
                                'updated_at' => 1531482438,
                            ],
                            'trial_period' => [
                                'id' => 44,
                                'quantity' => 2,
                                'period' => 'hour',
                                'description' => '2 hour access',
                                'created_at' => 1531482438,
                                'updated_at' => 1531482438,
                            ],
                            'setup_fee' => [
                                'id' => 44,
                                'fee_amount' => 3,
                                'description' => '3.00$ setup fee',
                                'created_at' => 1531482438,
                                'updated_at' => 1531482438,
                            ],
                            'geo_restriction' => [
                                'id' => 4,
                                'country_iso' => 'US',
                                'country_set_id' => 4,
                                'type' => 'blacklist',
                            ],
                            'seasonal_fee' => [
                                'off_season_access' => true,
                                'current_price_amount' => 4.2,
                                'anchor_date' => 1531482438,
                            ],
                            'expires_at' => 1531482438,
                            'starts_at' => 1531482438,
                            'created_at' => 1531482438,
                            'updated_at' => 1531482438,
                            'external_fees' => [
                                'id' => 44,
                                'payment_provider_id' => 12,
                                'access_fee_id' => 17,
                                'external_id' => 55,
                                'merchant_id' => 13,
                            ],
                        ],
                        status: 200,
                    );
                }
                else if ($request->method() == 'PUT')
                {
                    return Http::response(
                        body: [
                            'id' => 44,
                            'merchant_id' => 21,
                            'amount' => 5,
                            'currency' => 'USD',
                            'description' => '5.00$ for 1 hour access',
                            'item' => [
                                'content' => 'The asset\'s content as stringified JSON object',
                                'id' => 33,
                                'merchant_id' => 21,
                                'merchant_uuid' => 'e5ac2013-8d10-42ba-abb5-291c5201cea8',
                                'active' => true,
                                'title' => 'Foo bar',
                                'access_control_type' => [
                                    'id' => 4,
                                    'name' => 'Paid',
                                    'auth' => true,
                                ],
                                'item_type' => [
                                    'id' => 44,
                                    'name' => 'brightcove_asset',
                                    'content_type' => 'ovp',
                                    'host' => 'inplayer',
                                    'description' => 'OVP asset',
                                ],
                                'age_restriction' => [
                                    'min_age' => 18,
                                ],
                                'metahash' => [
                                    'property1' => 'string',
                                    'property2' => 'string',
                                ],
                                'created_at' => 1531482438,
                                'updated_at' => 1531482438,
                                'template_id' => 1,
                                'is_giftable' => true,
                                'gift_metadata' => [
                                    'id' => 4,
                                    'item_id' => 4,
                                    'description' => 'Paid',
                                    'created_at' => 1543238500,
                                    'updated_at' => 1543238500,
                                ],
                            ],
                            'access_type' => [
                                'account_id' => 44,
                                'id' => 44,
                                'name' => 'ppv',
                                'quantity' => 2,
                                'period' => 'hour',
                                'created_at' => 1531482438,
                                'updated_at' => 1531482438,
                            ],
                            'trial_period' => [
                                'id' => 44,
                                'quantity' => 2,
                                'period' => 'hour',
                                'description' => '2 hour access',
                                'created_at' => 1531482438,
                                'updated_at' => 1531482438,
                            ],
                            'setup_fee' => [
                                'id' => 44,
                                'fee_amount' => 3,
                                'description' => '3.00$ setup fee',
                                'created_at' => 1531482438,
                                'updated_at' => 1531482438,
                            ],
                            'geo_restriction' => [
                                'id' => 4,
                                'country_iso' => 'US',
                                'country_set_id' => 4,
                                'type' => 'blacklist',
                            ],
                            'seasonal_fee' => [
                                'off_season_access' => true,
                                'current_price_amount' => 4.2,
                                'anchor_date' => 1531482438,
                            ],
                            'expires_at' => 1531482438,
                            'starts_at' => 1531482438,
                            'created_at' => 1531482438,
                            'updated_at' => 1531482438,
                            'external_fees' => [
                                'id' => 44,
                                'payment_provider_id' => 12,
                                'access_fee_id' => 17,
                                'external_id' => 55,
                                'merchant_id' => 13,
                            ],
                        ],
                        status: 201,
                    );
                }
                else
                {
                    return Http::response(
                        body: [
                            'code' => 404,
                            'message' => 'Fee not found',
                        ],
                        status: 404,
                    );
                }
            },
            // fake get asset
            'https://staging-v2.inplayer.com/v2/items/*/*' => function (Request $request) {
                if ($request->method() == 'GET')
                {
                    return Http::response(
                        body: [
                            'content' => 'The asset\'s content as stringified JSON object',
                            'id' => 33,
                            'merchant_id' => 21,
                            'merchant_uuid' => 'e5ac2013-8d10-42ba-abb5-291c5201cea8',
                            'active' => true,
                            'title' => 'Foo bar',
                            'access_control_type' => [
                                'id' => 4,
                                'name' => 'Paid',
                                'auth' => true,
                            ],
                            'item_type' => [
                                'id' => 44,
                                'name' => 'brightcove_asset',
                                'content_type' => 'ovp',
                                'host' => 'inplayer',
                                'description' => 'OVP asset',
                            ],
                            'age_restriction' => [
                                'min_age' => 18,
                            ],
                            'metahash' => [
                                'property1' => 'string',
                                'property2' => 'string',
                            ],
                            'created_at' => 1531482438,
                            'updated_at' => 1531482438,
                            'template_id' => 1,
                            'is_giftable' => true,
                            'gift_metadata' => [
                                'id' => 4,
                                'item_id' => 4,
                                'description' => 'Paid',
                                'created_at' => 1543238500,
                                'updated_at' => 1543238500,
                            ],
                        ],
                        status: 200,
                    );
                }
                else
                {
                    return Http::response(
                        body: [
                            'code' => 404,
                            'message' => 'Item not found',
                        ],
                        status: 404,
                    );
                }
            },
            // fake update asset
            'https://staging-v2.inplayer.com/v2/items/*' => function (Request $request) {
                if ($request->method() == 'PATCH')
                {
                    return Http::response(
                        body: [
                            'id' => 33,
                            'merchant_id' => 21,
                            'merchant_uuid' => 'e5ac2013-8d10-42ba-abb5-291c5201cea8',
                            'active' => true,
                            'title' => 'Foo bar too',
                            'access_control_type' => [
                                'id' => 4,
                                'name' => 'Paid',
                                'auth' => true,
                            ],
                            'item_type' => [
                                'id' => 44,
                                'name' => 'brightcove_asset',
                                'content_type' => 'ovp',
                                'host' => 'inplayer',
                                'description' => 'OVP asset',
                            ],
                            'age_restriction' => [
                                'min_age' => 18,
                            ],
                            'metahash' => [
                                'property1' => 'string',
                                'property2' => 'string',
                            ],
                            'created_at' => 1531482438,
                            'updated_at' => 1531482438,
                            'template_id' => 1,
                            'is_giftable' => true,
                            'gift_metadata' => [
                                'id' => 4,
                                'item_id' => 4,
                                'description' => 'Paid',
                                'created_at' => 1543238500,
                                'updated_at' => 1543238500,
                            ],
                        ],
                        status: 201,
                    );
                }
                else
                {
                    return Http::response(
                        body: [
                            'code' => 403,
                            'message' => 'Invalid privileges',
                        ],
                        status: 403,
                    );
                }
            },
            // 'https://staging-v2.inplayer.com/v2/items' => Http::response(
            //     body: [],
            //     status: 200,
            // ),
        ]);
    }

    // private function fakeCreateAsset()
    // {
    //     InPlayerService::fake([
    //         'https://staging-v2.inplayer.com/v2/items' => Http::response(
    //             body: [],
    //             status: 200,
    //         ),
    //     ]);
    // }
}
