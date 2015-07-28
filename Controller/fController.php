<?php

namespace eDemy\CrawlerBundle\Controller;

use eDemy\MainBundle\Controller\BaseController;
use eDemy\MainBundle\Event\ContentEvent;

class fController extends BaseController
{
    protected $fs;

    public static function getSubscribedEvents()
    {
        return self::getSubscriptions('f', [], array());
    }

    public function afterSetEventDispatcher() {
        $this->file = $this->getParam('f.source');

        return true;
    }

    public function load($file = null)
    {
        if($file == null) {
            $file = $this->file;
        }
        if($file) {
            $this->todasFs = array();

            if (($handle = fopen($file, "r")) !== false) {
                $i = 0;
                while (($data = fgetcsv($handle, null, ";")) !== false) {
                    try {
                        $this->todasFs[$i]['fecha'] = $data[0];
                        $f = explode(',', $data[5]);
                        $this->todasFs[$i]['f'] = trim($f[1].' '.$f[0]);
                        $this->todasFs[$i]['direccion'] = trim(utf8_encode($data[6]));
                        $this->todasFs[$i]['ciudad'] = strtolower($data[7]);
                    } catch (\Exception $e) {
                    }
                    $i++;
                }
                fclose($handle);
            }
        }
    }

    public function getFs($ciudad = false, $todas = false, $ayer = false, $hoy = true, $mañana = false, $todos = false)
    {
        if($ciudad) {
            $ciudad = strtolower($ciudad);
            $fs = array();
            if ($ayer) {
                $a = new \DateTime('yesterday');
            }
            if ($hoy) {
                $h = new \DateTime('now');
            }
            if ($mañana) {
                $m = new \DateTime('tomorrow');
            }

            foreach ($this->todasFs as $f) {
                if (count($f) == 4) {
                    if ($todas) {
                        if ($todos) {
                            $fs[] = $f;
                        } else {
                            if ($ayer) {
                                if ($f['fecha'] == $a->format('d/m/y')) {
                                    $fs[] = $f;
                                }
                            }
                            if ($hoy) {
                                if ($f['fecha'] == $h->format('d/m/y')) {
                                    $fs[] = $f;
                                }
                            }
                            if ($mañana) {
                                if ($f['fecha'] == $m->format('d/m/y')) {
                                    $fs[] = $f;
                                }
                            }
                        }
                    } else {
                        if ($f['ciudad'] == $ciudad) {
                            if ($todos) {
                                $fs[] = $f;
                            } else {
                                if ($ayer) {
                                    if ($f['fecha'] == $a->format('d/m/y')) {
                                        $fs[] = $f;
                                    }
                                }
                                if ($hoy) {
                                    if ($f['fecha'] == $h->format('d/m/y')) {
                                        $fs[] = $f;
                                    }
                                }
                                if ($mañana) {
                                    if ($f['fecha'] == $m->format('d/m/y')) {
                                        $fs[] = $f;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $fs;
        }
    }
}
