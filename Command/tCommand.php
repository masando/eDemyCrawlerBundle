<?php

namespace eDemy\CrawlerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class tCommand extends ContainerAwareCommand
{
    private $domain;
    private $output, $input;
    
    protected function configure()
    {
        $this
            ->setName('crawler:t')
            ->setDescription('Show HTML Response')
            //->addArgument('url', InputArgument::OPTIONAL, 'What URL do you want to get?')
            ->addOption('a', null, InputOption::VALUE_NONE, 'ayer')
            //->addOption('h', null, InputOption::VALUE_OPTIONAL, 'hoy', true)
            //->addOption('m', null, InputOption::VALUE_NONE, 'mañana')
            //->addOption('todos', null, InputOption::VALUE_NONE, 'todos los días')
            //->addOption('todas', null, InputOption::VALUE_NONE, 'todas las ciudades')
            //->addOption('ciudad', null, InputOption::VALUE_REQUIRED, 'ciudad', '__CITY__')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input = $input;
        //$this->a = $this->input->getOption('a');
        $this->t('__CITY__');
    }
    
    protected function t($city = '__CITY__', $fecha = null) {
        if($fecha == null) {
            $fecha = new \DateTime('now');
        }
        $fecha = $fecha->format('Y-m-d');

        $this->tCrawler = $this->getContainer()->get('edemy.t_crawler');

        $pp = $this->tCrawler->getPP($city, $fecha);
        $ec = $this->tCrawler->getEC($city, $fecha);
        $max = $this->tCrawler->getMax($city, $fecha);
        $min = $this->tCrawler->getMin($city, $fecha);

        $this->output->writeln("c: " . $city . " - fecha: " . $fecha);
        $this->output->writeln("pp: " . $pp);
        $this->output->writeln("ec: " . $ec);
        $this->output->writeln("max: " . $max);
        $this->output->writeln("min: " . $min);
    }
}
