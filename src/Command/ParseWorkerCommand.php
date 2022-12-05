<?php

namespace App\Command;

use App\Repository\ArticleRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Article;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ParseWorkerCommand extends Command
{
    private $articleRepository;
    private $connection;
    private $channel;
    private $params;

    protected static $defaultName = 'app:news:parse_worker';

    public function __construct(ArticleRepository $articleRepository,ContainerBagInterface $params)
    {
        parent::__construct();
        $this->articleRepository = $articleRepository;
        $this->params = $params;
        $this->connection = new AMQPStreamConnection($this->params->get('RABBITMQ_HOST'), $this->params->get('RABBITMQ_PORT'), $this->params->get('RABBITMQ_USER'), $this->params->get('RABBITMQ_PASSWORD'));
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare('task_queue', false, true, false, false);
    }

    private function curl_get_contents($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // For https connections, we do not require SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        $content = curl_exec($ch);
        //$error = curl_error($ch);
        //$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $content;
    }

    private function scrapNews() {
        $fileCotent = @$this->curl_get_contents("https://highload.today/category/novosti/");
        $doc = new \DomDocument();
        //$doc->validateOnParse = true;
        @$doc->loadHtml($fileCotent);
        //echo $doc->saveHTML();
        $finder = new \DomXPath($doc);
        $articlesNodes = $finder->query("//*[contains(@class, 'lenta-item')]");
        
        //$articles = [] ;
        
        foreach($articlesNodes as $key => $node) {
            if ($key === 0) continue;//FIRST ITEM IS NOT AN ARTICLE
        
            //$articles[]= ['title'=>$articlesNodes[$key]->childNodes[5]->nodeValue,'description'=>$articlesNodes[$key]->lastElementChild->nodeValue,'image'=>$articlesNodes[2]->childNodes[10-1]->childNodes[1]->childNodes[1]->attributes[6]->textContent];
            $article = $this->articleRepository->findArticleByTitle($articlesNodes[$key]->childNodes[5]->nodeValue);
            
            if($article === null){
                $article = new Article();
                $article->setTitle($articlesNodes[$key]->childNodes[5]->nodeValue);
                $article->setDescription($articlesNodes[$key]->lastElementChild->nodeValue);
                $article->setPicture($articlesNodes[$key]->childNodes[10-1]->childNodes[1]->childNodes[1]->attributes[8]->value);
            }             
            else $article->setDateUpdate(new \DateTime());
        
            $this->articleRepository->save($article, true);
        }

        return count($articlesNodes)-1;//FIRST ITEM REMOVED BECCAUSE IS NOT AN ARTICLE
    }

    protected function configure()
    {
        $this ->setDescription('ParseWorkerCommand');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->success(sprintf('" [*] Waiting for messages. To exit press CTRL+C'));

        $callback = function ($msg) use($io) {
            //echo ' [x] Received ', $msg->body, "\n";
            //sleep(substr_count($msg->body, '.'));
            $coutFoundedArticle = $this->scrapNews();
            $io->success(sprintf('"%d" article(s) founded.', $coutFoundedArticle));
            $io->success(sprintf('[x] Done\n'));
            $msg->ack();
        };
        
        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume('task_queue', '', false, false, false, false, $callback);
        
        while ($this->channel->is_open())  $this->channel->wait();
        
        $this->channel->close();
        $this->connection->close();
        
        return 0;
    }
}