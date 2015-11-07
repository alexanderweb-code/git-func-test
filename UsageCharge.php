<?php

namespace App\Console\Commands;

use App\Traits\ShopifyTrait;
use App\User;
use Illuminate\Console\Command;
use Osiset\ShopifyApp\Storage\Models\Charge;

class UsageCharge extends Command
{
    use ShopifyTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usage:charge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create usage charge';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        logger('=============== START:: usage charge cron ==============');
        try {
            // $user = User::where('name', 'simplee-test-4.myshopify.com')->first();
            $owedShop = Shop::where('test_store', 0)->where('balance', '>=', 0.1);
            $user = $owedShop->user;

            $balance = $owedShop->balance;

            $price = floor($balance * 100) / 100.0;
            $remain = $balance - $price;

            $result = $this->createUsageCharge($user->id, $price);
            if( !$result['errors'] ){
                $owedShop->update([
                    'balance' => $remain
                ]);
            }

            // $this->createUsageCharge(1);
            //            $orders = SsOrder::where('tx_fee_status', 'pending')->get();
            //            if( count($orders) > 0 ){
            //                foreach ( $orders as $key=>$val ){
            //                    $this->createUsageCharge($val->user_id, $val->id);
            //                }
            //            }
        } catch (\Exception $e) {
            logger('=============== ERROR:: usage charge cron ==============');
            logger($e);
        }
        return 0;
    }

    /**
     * @param $user_id
     */
    public function createUsageCharge($user_id, $price)
    {
        try {
            logger('=============== START:: createUsageCharge ==============');
            $charge = Charge::where('user_id', $user_id)->where('status', 'ACTIVE')->first();
            $user = User::find($user_id);
            $endPoint = '/admin/api/' . env('SHOPIFY_API_VERSION') . '/recurring_application_charges/' . $charge->charge_id . '/usage_charges.json';
            $now = Carbon\Carbon::now()->format('M d Y');


            $charge = [
                "usage_charge" => [
                    "description" => "Simplee Subscriptions Fee for " . $now,
                    "price" => number_format($price, 2),
                ],
            ];
            return $user->api()->rest('POST', $endPoint, $charge);
            
//            $order = Order::where('id', $order_id)->first();
            //            if( $result['errors'] ){
            //                $order->tx_fee_status = $result['body']['usage_charge'];
            //            }else{
            //                $order->tx_fee_status = 'paid';
            //            }
            //            $order->save();
        } catch (\Exception $e) {
            logger('=============== ERROR:: createUsageCharge ==============');
            logger($e);
        }
    }
}
