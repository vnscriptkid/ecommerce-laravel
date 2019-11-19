<?php

namespace Tests\Unit\Models;

use App\Models\ProductVariation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserTest extends TestCase
{

    use RefreshDatabase;

    public function test_it_should_create_hash_password()
    {
        $user = User::create([
            'name' => 'thanh',
            'email' => 'thanh@gmail.com',
            'password' => '123456'
        ]);
        $this->assertNotEquals($user->password, '123456');
    }

    public function test_it_should_save_cart_record_to_carts_table()
    {
        $variation = factory(ProductVariation::class)->create();
        $user = factory(User::class)->create();
        $user->cart()->syncWithoutDetaching([
            $variation->id => ['quantity' => 10]
        ]);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_variation_id' => $variation->id,
            'quantity' => 10
        ]);
        $this->assertEquals(DB::table('carts')->count(), 1);
    }

    public function test_user_has_a_cart_of_variation_list()
    {
        $variation = factory(ProductVariation::class)->create();
        $user = factory(User::class)->create();
        $user->cart()->syncWithoutDetaching([
            $variation->id => ['quantity' => 10]
        ]);

        $this->assertInstanceOf(ProductVariation::class, $user->cart->first());
        $this->assertEquals($user->cart->count(), 1);
        $this->assertEquals($user->cart->first()->pivot->quantity, 10);
    }

    public function test_user_should_retrieve_only_his_cart_record()
    {
        // Arrange
        $variation = factory(ProductVariation::class)->create();
        factory(User::class)->create()->cart()->syncWithoutDetaching([
            $variation->id => ['quantity' => 100]
        ]);

        // Act
        $user = factory(User::class)->create();
        $user->cart()->syncWithoutDetaching([
            $variation->id => ['quantity' => 50]
        ]);

        // Assert
        $this->assertEquals($user->cart->count(), 1);
        $this->assertEquals($user->cart->first()->pivot->quantity, 50);
    }
}
