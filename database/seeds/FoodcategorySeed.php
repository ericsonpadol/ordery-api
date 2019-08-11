<?php

use Illuminate\Database\Seeder;
use App\Foodcategory;
use App\Traits\AccountHelper;

class FoodcategorySeed extends Seeder
{
    use AccountHelper;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Food Category Factory
        $foodCategories = [
            'African',
            'American',
            'Asian',
            'Bakery',
            'BBQ',
            'Beverages',
            'Breakfast and Brunch',
            'Burgers',
            'Cafe',
            'Cake',
            'Casual Dining',
            'Chinese',
            'Coffee and Tea',
            'Deli',
            'Dessert',
            'Fast Food',
            'Filipino',
            'Food Court',
            'Italian',
            'Japanese',
            'Juice and Smoothies',
            'Kiosk',
            'Korean',
            'Latin America',
            'Malaysian',
            'Mexican',
            'Middle Eastern',
            'Milk Tea',
            'Nachos',
            'Noodles',
            'Pizza',
            'Pork',
            'Quick Bites',
            'Seafood',
            'Snacks',
            'Sushi',
            'Wings'
        ];

        foreach ($foodCategories as $foodCategory) {
            Foodcategory::create([
                'food_category_id' => AccountHelper::uuidGeneration(),
                'food_category_name' => strtoupper($foodCategory)
            ]);
        }
    }
}
