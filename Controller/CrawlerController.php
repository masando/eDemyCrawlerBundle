<?php

namespace eDemy\CrawlerBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\EventDispatcher\GenericEvent;

use eDemy\MainBundle\Controller\BaseController;
use eDemy\MainBundle\Event\ContentEvent;

class CrawlerController extends BaseController
{
    public static function getSubscribedEvents()
    {
        return self::getSubscriptions('crawler', [], array(
            'edemy_crawler_onprocess' => array('onCrawlerProcess', 0),
        ));
    }

    public function __construct()
    {
        parent::__construct();
        
    }

    public function onFrontpage(ContentEvent $event)
    {
        $this->get('edemy.meta')->setTitlePrefix("Crawler");
        $params = $this->getParam('crawler.frontpage_module', null, null, null, true);

        if(count($params) == 1) {
            $modules[] = $params;
        } else {
            $modules = $params;
        }
        foreach($modules as $module) {
            $params = array();
            if($module->getDescription() != null) {
                $params = json_decode($module->getDescription(), true);
            }

            $e = new GenericEvent("module");
            $e->setArguments($params);
            $e->setArgument('content', null);

            $this->get('event_dispatcher')->dispatch($module->getValue(), $e);
        }

        $this->addEventModule($event, 'templates/crawler/frontpage', array(
            'content' => $e->getArgument('content'),
            //'entities' => $this->getRepository($event->getRoute())->findAllOrdered($this->getNamespace()),
        ));

    }

    public function onCrawlerProcess(GenericEvent $event) {
        $param = $event->getArgument('param');
        $proc = $this->getParam($param, null, null, null, true);
        $process = new Process($proc);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $event->setArgument('content', $event->getArgument('content') . $process->getOutput());
        //$this->addEventModule($event, null, $process->getOutput());
    }

    public function imageAction($param)
    {
        $msg = $this->SpanishDate((new \DateTime('tomorrow'))->getTimeStamp()) . "\n";

        $city = $this->getParam('t.city', null, false);
        $logo = $this->getParam('f.logo');
        $fecha = (new \DateTime('tomorrow'))->format('Y-m-d');
        //$file = $this->getParam('f.source');
        $fCrawler = $this->get('edemy.f_crawler');
        $tCrawler = $this->get('edemy.t_crawler');

        if($city) {
            try {
                $fCrawler->Load();
                $f = $fCrawler->getFs($city, false, false, false, true)[0];
                $msg_f = $this->getParam('f.msg');
                $msg_f = preg_replace('/\$city/', $city, $msg_f);
                $msg_f = preg_replace('/\$f\[f\]/', $f['f'], $msg_f);
                $msg_f = preg_replace('/\$f\[direccion\]/', $f['direccion'], $msg_f);
                $parts = explode('\\n', $msg_f);
                foreach($parts as $part) {
                    $msg .= $part . "\n";
                }

                $tCrawler->setTCrawler($fecha);
                $msg_t = $this->getParam('t.msg');
                $pp = $tCrawler->getPP($city, $fecha);
                $ec = $tCrawler->getEC($city, $fecha);
                $min = $tCrawler->getMin($city, $fecha);
                $max = $tCrawler->getMax($city, $fecha);

                $msg_t = preg_replace('/\$city/', $city, $msg_t);
                $msg_t = preg_replace('/\$pp/', $pp, $msg_t);
                $msg_t = preg_replace('/\$min/', $min, $msg_t);
                $msg_t = preg_replace('/\$max/', $max, $msg_t);

                $parts = explode('\\n', $msg_t);
                foreach($parts as $part) {
                    $msg .= $part . "\n";
                }

                $imagick = new \Imagick();
                $imagick->setResourceLimit(\Imagick::RESOURCETYPE_MEMORY, 8);
                $imagick->newImage(698, 698, new \ImagickPixel("white"));
                $imagick->setImageFormat("png");

                //$ellipse = $this->getEllipse(200, 100, 50, 50, 0, 360, "orange");
                //$im->drawImage($ellipse);

                if($logo) {
                    $mylogo = new \Imagick($logo);
                    $imagick->addImage($mylogo);
                    $imagick = $imagick->mergeImageLayers(\Imagick::LAYERMETHOD_COMPOSITE);
                }

                $text = $this->annotateImage($msg, 1, 130);
                $imagick->drawImage($text);

            } catch (Exception $e) {
                echo $e->getMessage();
            }
            $imagick->trimImage(10);
            $imagick->borderImage('white', 20, 20);
            $response = new Response($imagick, Response::HTTP_OK);
            $response->headers->set('Content-Type', 'image/png');

            return $response;
        }
    }

    public function annotateImage($text, $x = 0, $y = 0, $spacing = 25) {
        $draw = new \ImagickDraw();
        //$draw->setFont('Helvetica');
        //$draw->setFontFamily('Arial');
        $draw->setFontSize(20);
        $lines = explode("\n", $text);
        foreach ($lines as $line)
        {
          $draw->annotation($x, $y, $line);
          $y += $spacing;
        }
        //$metrics = $image->queryFontMetrics($draw, $text);

        //$draw->setStrokeColor('rgb(0, 0, 0)');
        //$draw->setFillColor('rgb(90%, 20%, 10%)');
        //$draw->setStrokeWidth(0.5);

        //$font_info = $im->queryFontMetrics($draw, $text);
        //$width = $font_info['textWidth'];
        //$height = $font_info['textHeight'];
        //$draw->annotation($x, $y, $text);
        
        return $draw;
    }

    public function getEllipse($x, $y, $rx, $ry, $start, $end, $color = 'black') {
        $draw = new \ImagickDraw();
        $draw->setFillColor(new \ImagickPixel($color));
        $draw->ellipse($x, $y, $rx, $ry, $start, $end);
        
        return $draw;
    }

    function SpanishDate($FechaStamp)
    {
       $ano = date('Y',$FechaStamp);
       $mes = date('n',$FechaStamp);
       $dia = date('j',$FechaStamp);
       $diasemana = date('w',$FechaStamp);
       $diassemanaN= array("Domingo","Lunes","Martes","Miércoles",
                      "Jueves","Viernes","Sábado");
       $mesesN=array(1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio",
                 "Agosto","Septiembre","Octubre","Noviembre","Diciembre");
       return $diassemanaN[$diasemana]." $dia de ". $mesesN[$mes];
    }

    public function throughAction($param) {
        $param = $this->getParam($param, null, null, null, true);
        //$content = "<script>" . file_get_contents($param) . "</script>";

        return $this->newResponse($param->getDescription());
    }

    public function processAction($param) {
        $proc = $this->getParam($param, null, null, null, true);
        $process = new Process($proc);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $this->newResponse($process->getOutput());
        //echo $process->getOutput();
    }
}
