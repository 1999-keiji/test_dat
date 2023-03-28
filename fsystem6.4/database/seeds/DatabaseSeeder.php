<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CurrenciesTableSeeder::class);
        $this->call(CorporationsTableSeeder::class);
        $this->call(FactoriesTableSeeder::class);
        $this->call(DeliveryDestinationsTableSeeder::class);
        $this->call(SpeciesTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(CustomersTableSeeder::class);
        $this->call(FactoryProductsTableSeeder::class);
        $this->call(DeliveryFactoryProductsSeeder::class);
        $this->call(FactoryProductSpecialPricesSeeder::class);
        $this->call(WarehousesTableSeeder::class);
        $this->call(FactoryWarehousesTableSeeder::class);
        $this->call(FactorySpeciesTableSeeder::class);
        $this->call(GrowthSimulationTableSeeder::class);
        $this->call(FactoryPanelsTableSeeder::class);
        $this->call(FactorCyclePatternsTableSeeder::class);
        $this->call(FactorCyclePatternItemsTableSeeder::class);
        $this->call(GrowthSimulationItemTableSeeder::class);
        $this->call(FactoryGrowingStagesTableSeeder::class);
        $this->call(PlannedCultivationStatusWorkTableSeeder::class);
        $this->call(FactoryBedsTableSeeder::class);
        $this->call(MenusTableSeeder::class);
    }
}
