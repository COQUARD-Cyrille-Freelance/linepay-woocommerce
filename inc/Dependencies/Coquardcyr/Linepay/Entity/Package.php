<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Entity;

use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\Price;

/**
 * Package of products.
 */
class Package extends Entity
{
    /**
     * Id from the package.
     *
     * @var string
     */
    protected $id;
    /**
     * Name from the package.
     *
     * @var string
     */
    protected $name = '';
    /**
     * Products from the package.
     *
     * @var Product[]
     */
    protected $products = [];
    /**
     * Price from the product.
     * @var Price
     */
    protected $amount = 0;

    /**
     * Initialize the class.
     *
     * @param string $id Id from the package.
     * @param string $name Name from the package.
     * @param Product[] $products Price from the product.
     */
    public function __construct(string $id, string $name, array $products)
    {
        $this->id = $id;
        $this->name = $name;
        $this->products = $products;

        $amount = array_reduce($products, static function (float $amount, Product $product) {
            return $amount + $product->get_price()->getValue();
        }, 0);
        $currency = array_reduce($products, static function($currency, Product $product) {
            if($currency) {
                return $currency;
            }
            return $product->get_price()->getCurrency();
        }, null);

        $this->amount = new Price($amount, $currency);
    }

    /**
     * Get the amount from the package.
     * @return Price
     */
    public function get_amount() {
        return $this->amount;
    }
}
