<?php

namespace Mitango\LinepayWoocommerce\Tests\Fixtures;

class WC_Payment_Gateway
{
    public $id;

    public $icon;

    public $has_fields;

    public $method_title;

    public $method_description;

    public $supports;

    public $options = [];

    public function init_settings() {

    }

    public function get_option(string $name) {
        if(! key_exists($name, $this->options)) {
            return false;
        }
        return $this->options[$name];
    }
}