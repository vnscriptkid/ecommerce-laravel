<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProductVariationStockViewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // dropView is required, because Laravel only drops tables when doing a fresh migration
        dump('up view');
        DB::statement($this->dropView());
        DB::statement($this->createView());
    }

    public function down()
    {
        dump('down view');
        DB::statement($this->dropView());
    }

    public function createView()
    {
        return "
        CREATE VIEW product_variation_stock_view AS
        SELECT
            product_variations.product_id,
            product_variations.id AS product_variation_id,
            IFNULL(stocks.quantity, 0),
            IFNULL(order_lines_grouped.ordered_quantity, 0),
            COALESCE(IFNULL(stocks.quantity, 0) - IFNULL(order_lines_grouped.ordered_quantity, 0)) AS stock,
            case when COALESCE(IFNULL(stocks.quantity, 0) - IFNULL(order_lines_grouped.ordered_quantity, 0)) > 0
                THEN true
                ELSE false
            END in_stock
        FROM product_variations
        LEFT JOIN (
            SELECT
                IFNULL(SUM(stocks.quantity), 0) AS quantity,
                stocks.product_variation_id AS product_variation_id
            FROM stocks
            GROUP BY stocks.product_variation_id
        ) AS stocks
        ON stocks.product_variation_id = product_variations.id
        LEFT JOIN (
            SELECT
                IFNULL(SUM(order_lines.quantity), 0) AS ordered_quantity,
                order_lines.product_variation_id AS id
            FROM order_lines
            GROUP BY order_lines.product_variation_id
        ) AS order_lines_grouped USING (id);
        ";
    }

    public function dropView()
    {
        return "DROP VIEW IF EXISTS product_variation_stock_view;";
    }
}
