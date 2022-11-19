<?php

namespace Mitango\LinepayWoocommerce\Engine\Checkout;

use Mitango\LinepayWoocommerce\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    public function provides(string $id): bool
    {
        return in_array($id, [
            'gateway_subscriber',
            'checkout_subscriber',
        ]);
    }

    public function register(): void
    {
        $this->getContainer()->add('linepay_gateway', Gateway::class);

        $this->getContainer()->add('gateway_subscriber', GatewaySubscriber::class)
            ->addArgument($this->getContainer()->get('linepay_gateway'));

        $this->getContainer()->add('checkout_subscriber', Subscriber::class);

    }
}