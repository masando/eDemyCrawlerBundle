<?php

namespace eDemy\CrawlerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class fCommand extends ContainerAwareCommand
{
    private $domain;
    private $output, $input;
    
    protected function configure()
    {
        $this
            ->setName('crawler:f')
            ->setDescription('Show HTML Response')
            //->addArgument('url', InputArgument::OPTIONAL, 'What URL do you want to get?')
            ->addOption('a', null, InputOption::VALUE_NONE, 'ayer')
            ->addOption('h', null, InputOption::VALUE_OPTIONAL, 'hoy', true)
            ->addOption('m', null, InputOption::VALUE_NONE, 'mañana')
            ->addOption('todos', null, InputOption::VALUE_NONE, 'todos los días')
            ->addOption('todas', null, InputOption::VALUE_NONE, 'todas las ciudades')
            ->addOption('ciudad', null, InputOption::VALUE_REQUIRED, 'ciudad', '__CITY__')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->count = 0;
        $this->subcount = 0;
        $this->file = '';
        $this->output = $output;
        $this->input = $input;
        
        $this->a = $this->input->getOption('a');
        $this->h = $this->input->getOption('h');
        $this->m = $this->input->getOption('m');
        $this->todos = $this->input->getOption('todos');
        $this->ciudad = $this->input->getOption('ciudad');
        $this->todas = $this->input->getOption('todas');

        $this->fCrawler = $this->getContainer()->get('edemy.f_crawler');
        $this->fs = $this->fCrawler->getF($this->ciudad, $this->todas, $this->a, $this->h, $this->m, $this->todos);
        foreach($this->fs as $f) {
            $this->output->writeln($f['fecha'] . ' - ' . $f['f'] . ' - ' . $f['direccion'] . ' - ' . $f['ciudad']);
        }
    }
}
