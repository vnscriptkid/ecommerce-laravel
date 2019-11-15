<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_1_category_with_no_children()
    {
        // Arrange
        $category = factory(Category::class)->create();

        // Act
        $response = $this->get('/api/categories');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertExactJson([
                'data' => [
                    [
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'subCategories' => []
                    ]
                ]
            ]);
    }

    public function test_returns_1_category_with_1_child()
    {
        // Arrange
        $parent = factory(Category::class)->create();
        $parent->subCategories()->save(
            $child = factory(Category::class)->create()
        );

        // Act
        $response = $this->get('/api/categories');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'name' => $parent->name,
                'slug' => $parent->slug,
                'subCategories' => [
                    [
                        'name' => $child->name,
                        'slug' => $child->slug
                    ]
                ]
            ]);
    }

    public function test_returns_1_category_with_2_children()
    {
        // Arrange
        $parent = factory(Category::class)->create();
        $children = factory(Category::class, 2)->create([
            'parent_id' => $parent->id
        ]);

        // Act
        $response = $this->get('/api/categories');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'name' => $parent->name,
                'slug' => $parent->slug
            ]);

        $this->assertCount(2, $response->json('data')[0]['subCategories']);
    }

    public function test_returns_1_category_with_5_children()
    {
        // Arrange
        $parent = factory(Category::class)->create();
        $count = 10;
        $children = factory(Category::class, 5)->make([
            'parent_id' => $parent->id,
        ])->each(function ($category) use (&$count) {
            $category->order = $count--;
            $category->save();
        });

        // Act
        $response = $this->get('/api/categories');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'name' => $parent->name,
                'slug' => $parent->slug
            ]);

        $children->each(function ($category) use ($response) {
            $this->assertInstanceOf(Category::class, $category);
            $response->assertJsonFragment([
                'name' => $category->name,
                'slug' => $category->slug
            ]);
        });
    }

    public function test_3_root_categories_should_be_ordered_by_order_prop()
    {
        // Arrange
        $middle = factory(Category::class)->create(['order' => 2]);
        $first = factory(Category::class)->create(['order' => 1]);
        $last = factory(Category::class)->create(['order' => 3]);
        $last->subCategories()->save(
            factory(Category::class)->create(['order' => 4])
        );

        // Act
        $response = $this->json('GET', '/api/categories');

        // Assert
        // only parents
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');

        // ordered correctly
        $categories = $response->json('data');
        $this->assertEquals($categories[0]['name'], $first->name);
        $this->assertEquals($categories[1]['name'], $middle->name);
        $this->assertEquals($categories[2]['name'], $last->name);
        // cleaner way
        $response->assertSeeInOrder([
            $first->slug,
            $middle->slug,
            $last->slug
        ]);
    }
}
