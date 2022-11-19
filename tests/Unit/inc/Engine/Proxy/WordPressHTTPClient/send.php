<?php

namespace Mitango\LinepayWoocommerce\Tests\Unit\inc\Engine\Proxy\WordPressHTTPClient;

use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Proxy\RequestResponse;
use Mitango\LinepayWoocommerce\Dependencies\Coquardcyr\Linepay\Request\AbstractRequest;
use Mitango\LinepayWoocommerce\Engine\Proxy\WordPressHTTPClient;
use Mitango\LinepayWoocommerce\Tests\Unit\TestCase;
use Brain\Monkey\Functions;
use Mockery;

class Test_Send extends TestCase
{
    protected $proxy;
    protected $request;

    public function set_up()
    {
        parent::set_up();
        $this->proxy = new WordPressHTTPClient();
        $this->request = Mockery::mock(AbstractRequest::class);

    }

    /**
     * @dataProvider configTestData
     */
    public function testShouldReturnAsExpected($config, $expected) {
        $this->request->expects()->getUrl()->andReturns($config['url']);
        $this->request->expects()->getHeaders()->andReturns($config['headers']);
        $this->request->expects()->getBody()->andReturns($config['body']);
        Functions\expect('wp_remote_post')->with($expected['url'], $expected['params'])->andReturn($config['response']);
        Functions\expect('wp_remote_retrieve_response_code')->with($expected['response'])->andReturn($config['code']);
        Functions\expect('wp_remote_retrieve_response_message')->with($expected['response'])->andReturn($config['message']);
        Functions\expect('wp_remote_retrieve_body')->with($expected['response'])->andReturn($config['body']);
        /**
         * @var RequestResponse $response
         */
        $response = $this->proxy->send($this->request);
        $this->assertSame($expected['code'], $response->getStatusCode());
        $this->assertSame($expected['message'], $response->getMessage());
        $this->assertSame($expected['body'], $response->getBody());
    }
}