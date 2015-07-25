<?php

namespace eDemy\CrawlerBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use eDemy\MainBundle\Controller\BaseController;
use eDemy\MainBundle\Event\ContentEvent;

class CrawlerController extends BaseController
{
    public static function getSubscribedEvents()
    {
        return self::getSubscriptions('crawler', [], array(
        ));
    }

    public function __construct()
    {
        parent::__construct();
        
    }

    public function imagickAction()
    {
        try
        {
            $hoy = $this->SpanishDate((new \DateTime('now'))->getTimeStamp());
            $f = $this->get('edemy.f_crawler')->getFs()[0];
            $mensaje = __MSG1__;
            $mensaje .= "\n" . $f['f'] . " en " .  $f['direccion'];
            $f = $this->get('edemy.f_crawler')->getFs(__CITY__)[0];
            $mensaje .= __MSG2__;
            $mensaje .= "\n" . $f['f'] . " en " . $f['direccion'];
            $fecha = (new \DateTime('now'))->format('Y-m-d');
            $tCrawler = $this->get('edemy.t_crawler');
            $pp = $tCrawler->getPP(__CITY__, $fecha);
            $mensaje .= __MSG3__;
            $ec = $tCrawler->getEC(__CITY__, $fecha);
            $mensaje .= "\nEC: " . $ec;
            $min = $tCrawler->getMin(__CITY__, $fecha);
            $max = $tCrawler->getMax(__CITY__, $fecha);
            $mensaje .= __MSG4__;

            $imagick = new \Imagick();
            $imagick->setResourceLimit(\Imagick::RESOURCETYPE_MEMORY, 8);
            $imagick->newImage(698, 698, new \ImagickPixel("white"));
            $imagick->setImageFormat( "jpg" );

            //$ellipse = $this->getEllipse(200, 100, 50, 50, 0, 360, "orange");
            //$im->drawImage($ellipse);

            $text = $this->annotateImage($mensaje, 20, 230);
            $imagick->drawImage($text);


            $logo = new \Imagick(__LOGO__);
            $imagick->addImage($logo);
            $imagick = $imagick->mergeImageLayers(\Imagick::LAYERMETHOD_COMPOSITE);
        } catch(Exception $e) {
            echo $e->getMessage();
        }
        $imagick->trimImage(10);
        $imagick->borderImage('white', 0, 20);
        $response = new Response($imagick, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'image/png');

        return $response;
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
}
