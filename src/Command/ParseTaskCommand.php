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
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ParseTaskCommand extends Command
{
    private $articleRepository;
    private $connection;
    private $channel;
    private $params;

    protected static $defaultName = 'app:news:parse_task';

    public function __construct(ArticleRepository $articleRepository,ContainerBagInterface $params)
    {
        parent::__construct();
        $this->articleRepository = $articleRepository;
        $this->params = $params;
        $this->connection = new AMQPStreamConnection($this->params->get('RABBITMQ_HOST'), $this->params->get('RABBITMQ_PORT'), $this->params->get('RABBITMQ_USER'), $this->params->get('RABBITMQ_PASSWORD'));
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare('task_queue', false, true, false, false);
    }

    protected function configure()
    {
        $this ->setDescription('ParseTaskCommand');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        //$data = implode(' ', array_slice($argv, 1));
        //if (empty($data))  $data = "Hello World!";
        $data = "Hello World!";
        $msg = new AMQPMessage($data,array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $this->channel->basic_publish($msg, '', 'task_queue');
        $this->channel->close();
        $this->connection->close();

        $io->success(sprintf("[x] task pushed to worker"));

        return 0;
    }
}