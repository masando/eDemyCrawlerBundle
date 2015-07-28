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
        return self::getSubscriptions('t', [], array());
    }

    public function __construct()
    {
        parent::__construct();

        CssSelector::disableHtmlExtension();
    }

    public function afterSetEventDispatcher() {
        $this->city = $this->getParam('t.city');
        $this->url = $this->getParam('t.source');

        return true;
    }

    public function setTCrawler($fecha)
    {
        $this->filter = $this->getParam('t.filter');
        $this->client = new Client();
        $crawler = $this->follow($this->url);
        $str = preg_replace('/__fecha__/', $fecha, $this->filter);
        $this->pred_crawler = $crawler->filter($str);

        return $this->pred_crawler;
    }

    public function getPP($city = false, $fecha = null)
    {
        $this->filter2 = $this->getParam('t.filter2');

        return $this->pred_crawler->filter($this->filter2)->text();
    }

    public function getEC($city = false, $fecha = null)
    {
        $this->filter3 = $this->getParam('t.filter3');

        return $this->pred_crawler->filter($this->filter3)->attr("descripcion");
    }

    public function getMax($city = false, $fecha = null)
    {

        return $this->pred_crawler->filter('maxima')->text();
    }

    public function getMin($city = false, $fecha = null)
    {

        return $this->pred_crawler->filter('minima')->text();
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
