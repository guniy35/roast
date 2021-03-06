<?php

namespace GuoJiangClub\Component\Discount\Test;

use Carbon\Carbon;
use Faker\Factory;
use GuoJiangClub\Component\Discount\Actions\OrderFixedDiscountAction;
use GuoJiangClub\Component\Discount\Actions\OrderPercentageDiscountAction;
use GuoJiangClub\Component\Discount\Checkers\CartQuantityRuleChecker;
use GuoJiangClub\Component\Discount\Checkers\ItemTotalRuleChecker;
use GuoJiangClub\Component\Discount\Contracts\AdjustmentContract;
use GuoJiangClub\Component\Discount\Models\Action;
use GuoJiangClub\Component\Discount\Models\Coupon;
use GuoJiangClub\Component\Discount\Models\Discount;
use GuoJiangClub\Component\Discount\Models\Rule;
use GuoJiangClub\Component\Discount\Repositories\DiscountRepository;
use GuoJiangClub\Component\Discount\Test\Models\Adjustment;
use GuoJiangClub\Component\Discount\Test\Models\User;
use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

/**
 * Class BaseTest
 * @package GuoJiangClub\Component\Discount\Test
 */
abstract class BaseTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->loadMigrationsFrom(__DIR__ . '/database');

        $this->seedData();

        $this->app->bind(AdjustmentContract::class,Adjustment::class);
    }

    /**
     * @param $app
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
        $app['config']->set('repository.cache.enabled', true);

    }

    /**
     * @param $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Prettus\Repository\Providers\RepositoryServiceProvider::class,
            \Orchestra\Database\ConsoleServiceProvider::class,
            \GuoJiangClub\Component\Discount\Providers\DiscountServiceProvider::class
        ];
    }

    /**
     *
     */
    protected function seedData()
    {
        $faker = Factory::create('zh_CN');
        $this->seedDiscount($faker);
        $this->seedCoupon($faker);

        $this->user = new User([
            'id' => 1,
            'name' => 'ibrand'
        ]);

        $this->seedUserCoupon($faker);
    }


    /**
     * @param $faker
     */
    protected function seedDiscount($faker)
    {
//??????????????????????????????
        Discount::create([
            'title' => $faker->word,
            'label' => $faker->word,
            'usage_limit' => $faker->numberBetween(3, 100),
            'used' => $faker->randomDigitNotNull,
            'starts_at' => Carbon::now()->addDay(1),
            'ends_at' => Carbon::now()->addDay(2),
        ]);

        //????????????????????????????????????
        Discount::create([
            'title' => $faker->word,
            'label' => $faker->word,
            'usage_limit' => $faker->numberBetween(3, 100),
            'used' => $faker->randomDigitNotNull,
            'starts_at' => Carbon::now()->addDay(-1),
            'ends_at' => Carbon::now()->addDay(2),
            'status' => 0,
        ]);

        //????????????????????????????????????
        Discount::create([
            'title' => $faker->word,
            'label' => $faker->word,
            'usage_limit' => 100,
            'used' => 100,
            'starts_at' => Carbon::now()->addDay(-1),
            'ends_at' => Carbon::now()->addDay(2),
            'status' => 0,
        ]);

        //?????????????????????????????????,??????????????????
        $discount = Discount::create([
            'title' => $faker->word,
            'label' => $faker->word,
            'usage_limit' => $faker->numberBetween(80, 100),
            'used' => 20,
            'starts_at' => Carbon::now()->addDay(-1),
            'ends_at' => Carbon::now()->addDay(2),
        ]);
        //??????????????????2,?????????10???
        Rule::create(['discount_id' => $discount->id, 'type' => CartQuantityRuleChecker::TYPE, 'configuration' => json_encode(['count' => 2])]);
        Action::create(['discount_id' => $discount->id, 'type' => OrderFixedDiscountAction::TYPE, 'configuration' => json_encode(['amount' => 10])]);

        //?????????????????????????????????,??????????????????
        $discount = Discount::create([
            'title' => $faker->word,
            'label' => $faker->word,
            'usage_limit' => $faker->numberBetween(80, 100),
            'used' => 20,
            'starts_at' => Carbon::now()->addDay(-1),
            'ends_at' => Carbon::now()->addDay(2),
        ]);
        //?????????100-10
        Rule::create(['discount_id' => $discount->id, 'type' => ItemTotalRuleChecker::TYPE, 'configuration' => json_encode(['amount' => 100])]);
        Action::create(['discount_id' => $discount->id, 'type' => OrderFixedDiscountAction::TYPE, 'configuration' => json_encode(['amount' => 10])]);


        //?????????????????????????????????,?????????????????????
        $discount = Discount::create([
            'title' => $faker->word,
            'label' => $faker->word,
            'usage_limit' => $faker->numberBetween(80, 100),
            'used' => 20,
            'starts_at' => Carbon::now()->addDay(-1),
            'ends_at' => Carbon::now()->addDay(2),
        ]);
        //?????????120???8???
        Rule::create(['discount_id' => $discount->id, 'type' => ItemTotalRuleChecker::TYPE, 'configuration' => json_encode(['amount' => 120])]);
        Action::create(['discount_id' => $discount->id, 'type' => OrderPercentageDiscountAction::TYPE, 'configuration' => json_encode(['percentage' => 80])]);
    }

    /**
     * @param $faker
     */
    protected function seedCoupon($faker)
    {
        //?????????????????????????????????
        Discount::create([
            'title' => $faker->word,
            'label' => $faker->word,
            'usage_limit' => $faker->numberBetween(3, 100),
            'used' => $faker->randomDigitNotNull,
            'starts_at' => Carbon::now()->addDay(1),
            'ends_at' => Carbon::now()->addDay(2),
            'coupon_based' => 1,
        ]);

        //????????????????????????????????????
        Discount::create([
            'title' => $faker->word,
            'label' => $faker->word,
            'usage_limit' => $faker->numberBetween(3, 100),
            'used' => $faker->randomDigitNotNull,
            'starts_at' => Carbon::now()->addDay(-1),
            'ends_at' => Carbon::now()->addDay(2),
            'status' => 0,
            'coupon_based' => 1,
        ]);

        //???????????????????????????????????????
        Discount::create([
            'title' => $faker->word,
            'label' => $faker->word,
            'usage_limit' => 100,
            'used' => 100,
            'starts_at' => Carbon::now()->addDay(-1),
            'ends_at' => Carbon::now()->addDay(2),
            'status' => 0,
            'coupon_based' => 1,
        ]);

        //????????????????????????????????????,??????????????????
        $discount = Discount::create([
            'title' => $faker->word,
            'label' => $faker->word,
            'usage_limit' => $faker->numberBetween(80, 100),
            'used' => 20,
            'starts_at' => Carbon::now()->addDay(-1),
            'ends_at' => Carbon::now()->addDay(2),
            'coupon_based' => 1,
        ]);
        //??????????????????2,?????????10???
        Rule::create(['discount_id' => $discount->id, 'type' => CartQuantityRuleChecker::TYPE, 'configuration' => json_encode(['count' => 2])]);
        Action::create(['discount_id' => $discount->id, 'type' => OrderFixedDiscountAction::TYPE, 'configuration' => json_encode(['amount' => 10])]);

        //????????????????????????????????????,??????????????????
        $discount = Discount::create([
            'title' => $faker->word,
            'label' => $faker->word,
            'usage_limit' => $faker->numberBetween(80, 100),
            'used' => 20,
            'starts_at' => Carbon::now()->addDay(-1),
            'ends_at' => Carbon::now()->addDay(2),
            'coupon_based' => 1,
        ]);
        //?????????100-10
        Rule::create(['discount_id' => $discount->id, 'type' => ItemTotalRuleChecker::TYPE, 'configuration' => json_encode(['amount' => 100])]);
        Action::create(['discount_id' => $discount->id, 'type' => OrderFixedDiscountAction::TYPE, 'configuration' => json_encode(['amount' => 10])]);

        //????????????????????????????????????,?????????????????????
        $discount = Discount::create([
            'title' => $faker->word,
            'label' => $faker->word,
            'usage_limit' => $faker->numberBetween(80, 100),
            'used' => 20,
            'starts_at' => Carbon::now()->addDay(-1),
            'ends_at' => Carbon::now()->addDay(2),
            'coupon_based' => 1,
        ]);
        //?????????100???8???
        Rule::create(['discount_id' => $discount->id, 'type' => ItemTotalRuleChecker::TYPE, 'configuration' => json_encode(['amount' => 120])]);
        Action::create(['discount_id' => $discount->id, 'type' => OrderFixedDiscountAction::TYPE, 'configuration' => json_encode(['percentage' => 80])]);


        //??????????????????????????????,?????????????????????
        $discount = Discount::create([
            'title' => $faker->word,
            'label' => $faker->word,
            'usage_limit' => $faker->numberBetween(80, 100),
            'used' => 20,
            'starts_at' => Carbon::now()->addDay(-1),
            'ends_at' => Carbon::now()->addDay(2),
            'coupon_based' => 1,
        ]);
        //???????????????3??????9???
        Rule::create(['discount_id' => $discount->id, 'type' => CartQuantityRuleChecker::TYPE, 'configuration' => json_encode(['count' => 3])]);
        Action::create(['discount_id' => $discount->id, 'type' => OrderFixedDiscountAction::TYPE, 'configuration' => json_encode(['percentage' => 90])]);
    }

    protected function seedUserCoupon($faker)
    {
        $repository =$this->app->make(DiscountRepository::class);

        //get active discount coupons
        $discounts = $repository->findActive(1);

        //??????20????????????
        for ($i=0;$i<20;$i++){
            Coupon::create(['discount_id'=>$discounts->random()->id,'user_id'=>$this->user->id,]);
        }

    }

}
