<?php

namespace Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay;

use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\CountryCode;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\ObjectValue\LogoType;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Proxy\HTTPClient;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Request\AbstractRequest;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Response\ResponseFactory;

class Linepay
{
    /**
     * @var string
     */
    protected $id_channel;
    /**
     * @var string
     */
    protected $secret_channel;
    /**
     * @var string
     */
    protected $base_url;

    /**
     * @var HTTPClient
     */
    protected $client;

    /**
     * @var ResponseFactory
     */
    protected $response_factory;

    /**
     * @param string $id_channel
     * @param string $secret_channel
     */
    public function __construct(string $id_channel, string $secret_channel, bool $dev = false, HTTPClient $client = null, ResponseFactory $response_factory = null)
    {
        $this->id_channel = $id_channel;
        $this->secret_channel = $secret_channel;
        $this->base_url = $dev ? 'https://sandbox-api-pay.line.me': 'https://api-pay.line.me';
        $this->client = $client;
        $this->response_factory = $response_factory ?: new ResponseFactory();
    }

    public function prepare(AbstractRequest $request) {
        $request->setChannelId($this->id_channel);
        $request->setChannelSecret($this->secret_channel);
        $request->setBaseUrl($this->base_url);
        return $request;
    }

    public function run(AbstractRequest $request) {
        if(! $this->client) {
            return null;
        }
        $response = $this->client->send($request);
        return $this->response_factory->make($request, $response);
    }

    public static function get_logo(CountryCode $code, LogoType $type, int $width) {
        $country = strtolower($code->getValue());
        $filename =  __DIR__ . "/assets/logo/{$country}/logo/logo_{$type->getValue()}.png";
        // Get new sizes
        list($image_width, $image_height) = getimagesize($filename);
        $ratio = $image_width / $width;
        $newheight = $image_height * $ratio;
        $thumb = imagecreatetruecolor($width, $newheight);
        $source = imagecreatefromjpeg($filename);
        imagecopyresized($thumb, $source, 0, 0, 0, 0, $width, $newheight, $image_width, $image_height);
        ob_start();
        imagepng($thumb);
        $image_data = ob_get_contents();
        ob_end_clean();
        return $image_data;
    }
}
