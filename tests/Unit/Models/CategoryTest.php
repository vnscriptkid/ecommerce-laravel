<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_its_relationship()
    {
        // Arrange
        $parent = factory(Category::class)->create();
        $children = factory(Category::class, 2)->create([
            'parent_id' => $parent->id
        ]);

        // Assert
        $this->assertEquals(Category::count(), 3);
        $this->assertEquals($parent->subCategories->count(), 2);
        $this->assertEquals($children->first()->parentCategory->name, $parent->name);
    }

    public function test_its_relationship_2()
    {
        // Arrange
        $parent = factory(Category::class)->create();
        $parent->subCategories()->save(factory(Category::class)->create());

        // Assert
        $this->assertEquals(Category::count(), 2);
        $this->assertEquals($parent->subCategories->count(), 1);
    }

    public function test_scope_only_root_parents()
    {
        // Arrange
        $parent = factory(Category::class)->create();
        $parent->subCategories()->save(factory(Category::class)->create());

        // Assert
        $this->assertEquals(Category::rootParents()->get()->count(), 1);
        $this->assertEquals(Category::rootParents()->get()->first()->name, $parent->name);
    }

    public function test_scope_not_parents_categories()
    {
        // Arrange
        $parent = factory(Category::class)->create();
        $child = $parent->subCategories()->save(factory(Category::class)->create());

        // Assert
        $this->assertEquals(Category::notRootParents()->get()->count(), 1);
        $this->assertEquals(Category::notRootParents()->get()->first()->name, $child->name);
    }

    public function test_scope_ordered()
    {
        // Arrange
        $high = factory(Category::class)->create(['order' => 30]);
        $low = factory(Category::class)->create(['order' => 10]);
        // Act 1
        $orderedList = Category::ordered()->get();
        $this->assertEquals($orderedList->count(), 2);
        $this->assertEquals($orderedList->first()->order, $low->order);
        // Act 2
        $orderedList = Category::ordered('desc')->get();
        $this->assertEquals($orderedList->count(), 2);
        $this->assertEquals($orderedList->first()->order, $high->order);
    }
}
