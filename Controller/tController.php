<?php

namespace eDemy\CrawlerBundle\Controller;

use GuzzleHttp\Client;
use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\DomCrawler\Crawler;

use eDemy\MainBundle\Controller\BaseController;
use eDemy\MainBundle\Event\ContentEvent;

class tController extends BaseController
{
    protected $file;
    protected $pred;

    public static function getSubscribedEvents()
    {
        return self::getSubscriptions('t', [], array(
        ));
    }

    public function __construct()
    {
        parent::__construct();

        CssSelector::disableHtmlExtension();
    }

    public function getTCrawler($city, $fecha = null) 
    {
        switch($city) {
            case '__CITY__':
                $this->url = '';
                break;
            case '__CITY2__':
                $this->url = '';
                break;
        }
        $this->client = new Client();
        $crawler = $this->follow($this->url);
        $pred_crawler = $crawler->filter('__FILTER__');
        return $pred_crawler;
    }

    public function getPP($city = '__CITY__', $fecha = null)
    {

        return $this->getTCrawler($city, $fecha)->filter('__FILTER2__')->text();
    }

    public function getEC($city = '__CITY__', $fecha = null)
    {

        return $this->getTCrawler($city, $fecha)->filter('__FILTER3__')->attr("descripcion");
    }

    public function getMax($city = '__CITY__', $fecha = null)
    {

        return $this->getTCrawler($city, $fecha)->filter('max')->text();
    }

    public function getMin($city = '__CITY__', $fecha = null)
    {

        return $this->getTCrawler($city, $fecha)->filter('min')->text();
    }

    protected function follow($link) 
    {
        
        return new Crawler($this->getContents($this->client->get($link)->getBody()));
    }

    protected function getContents($stream) {
        ob_start();
        echo $stream;
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}
