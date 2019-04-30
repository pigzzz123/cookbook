<?php

namespace App\Console\Commands;

use App\Models\Food;
use Goutte\Client;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class GatherFood extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gather:food';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'gather foods';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client();
        $httpClient = new HttpClient([
            'base_uri' => 'https://www.xiangha.com/',
            'timeout' => 60,
        ]);
        $client->setClient($httpClient);

        $crawler = $client->request('GET', 'shicai/');
        $list = $crawler->filter('.rec_classify_cell>ul>li>a:not([rel=nofollow])')->each(function (Crawler $node) {
            return ['name' => $node->text()];
        });

        foreach ($list as $data) {
            Food::firstOrCreate($data);
        }
    }
}
