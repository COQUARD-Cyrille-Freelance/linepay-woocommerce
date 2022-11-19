<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Entity;

use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\Price;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\Quantity;

class Product extends Entity
{
    /**
     * Name from the product.
     *
     * @var string
     */
    protected $name;
    /**
     * Quantity from the product.
     *
     * @var Quantity
     */
    protected $quantity;
    /**
     * Price from the product.
     *
     * @var Price
     */
    protected $price;
    /**
     * Url from the image.
     *
     * @var string
     */
    protected $imageUrl;

    /**
     * Initialize the class.
     *
     * @param string $name Name from the product.
     * @param Quantity $quantity Quantity from the product.
     * @param Price $price Price from the product.
     * @param string $imageUrl Url from the image.
     */
    public function __construct(string $name, Quantity $quantity, Price $price, string $imageUrl)
    {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->imageUrl = $imageUrl;
    }

    public function get_price()
    {
        return $this->price;
    }
}
