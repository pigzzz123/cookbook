<?php

namespace App\Console\Commands;

use App\Models\CookBook;
use App\Models\Food;
use Carbon\Carbon;
use Goutte\Client;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Pool;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Symfony\Component\DomCrawler\Crawler;

class GatherCookBook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gather:cookbook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'gather cookbooks';

    private $list_url;
    private $page_index = 1;
    private $pages = 30;
    private $category_id;

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
        $this->list_url = $this->ask('请输入列表网址');
        $this->page_index = $this->ask('请输入起始页');
        $this->pages = $this->ask('请输入采集页数');
        $this->category_id = $this->ask('请输入分类 ID');

        $urls = $this->gatherUrls();

        $this->gatherDetail($urls);
    }

    protected function gatherUrls()
    {
        $httpClient = new HttpClient([
            'base_uri' => 'http://www.zuofan.cn/',
            'timeout' => 60,
            'header' => [
                'User-Agent' => $this->randomUA(),
                'Referer' => 'http://www.zuofan.cn/jc/',
            ]
        ]);

        $requests = function ($total) use ($httpClient) {
            for ($i = $this->page_index; $i <= $total; $i++) {
                $url = str_ireplace('PAGE', $i, $this->list_url);
                yield function() use ($httpClient, $url) {
                    return $httpClient->getAsync($url);
                };
            }
        };

        $urls = [];

        $pool = new Pool($httpClient, $requests($this->pages), [
            'concurrency' => 5,
            'fulfilled'   => function ($response) use (&$urls) {

                $contentCrawler = new Crawler($response->getBody()->getContents());
                $contentCrawler->filter('.cp_list>.center>dl a')->each(function (Crawler $node) use (&$urls) {
                    $urls[] = $node->attr('href');
                });

            },
            'rejected' => function ($reason, $index){
                $this->error("rejected" );
                $this->error("rejected reason: " . $reason );
            },
        ]);

        // 开始发送请求
        $promise = $pool->promise();
        $promise->wait();

        return array_unique($urls);
    }

    protected function gatherDetail($urls = [])
    {
        $httpClient = new HttpClient([
            'base_uri' => 'http://www.zuofan.cn/',
            'timeout' => 120,
            'header' => [
                'User-Agent' => $this->randomUA(),
                'Referer' => 'http://www.zuofan.cn/jc/',
            ]
        ]);

        $requests = function ($total) use ($httpClient, $urls) {
            foreach ($urls as $url) {
                yield function() use ($httpClient, $url) {
                    return $httpClient->getAsync($url);
                };
            }
        };

        $pool = new Pool($httpClient, $requests(count($urls)), [
            'concurrency' => 5,
            'fulfilled'   => function ($response, $index) use (&$urls) {

                $contentCrawler = new Crawler($response->getBody()->getContents());
                $contentCrawler->filter('.cp_show>.center')->each(function (Crawler $node) use (&$urls, $index) {
                    $category_id = $this->category_id;
                    $name = $node->filter('h1')->count() ? $node->filter('h1')->text() : '';
                    $cover = $node->filter('.pic>img')->count() ? $node->filter('.pic>img')->attr('src') : ($node->filter('.content img')->count() ? $node->filter('.content img')->eq(0)->attr('src') : '');
                    $description = $node->filter('.efficacy')->count() ? preg_replace("/<(span.*?)>(.*?)<(\/span.*?)>/si","", $node->filter('.efficacy')->html()) : '';
                    $tips = $node->filter('.jiqiao>p')->count() ? $node->filter('.jiqiao>p')->text() : '';
                    $foods = $node->filter('.yuanliao>ul>li')->count() ? $node->filter('.yuanliao>ul>li')->each(function (Crawler $n) {
                       return [
                           'name' => preg_replace("/<(span.*?)>(.*?)<(\/span.*?)>/si","", $n->html()),
                           'number' => $n->filter('span')->text()
                       ];
                    }) : [];

                    $steps = $node->filter('.zuofa>ul>li')->count() ? $node->filter('.zuofa>ul>li')->each(function (Crawler $n, $i) {
                        return [
                            'cover' => $n->filter('img')->count() ? $n->filter('img')->attr('src') : '',
                            'content' => preg_replace("/<(em.*?)>(.*?)<(\/em.*?)>/si","", $n->filter('p')->html()),
                            'order' => $i+1
                        ];
                    }) : [];

                    $data = compact('name', 'cover', 'description', 'tips', 'foods', 'steps', 'category_id');
                    $this->createBook($data);
                });

            },
            'rejected' => function ($reason, $index){
                $this->error("rejected" );
                $this->error("rejected reason: " . $reason );
            },
        ]);

        // 开始发送请求
        $promise = $pool->promise();
        $promise->wait();
    }

    public function createBook($data)
    {
        $cookbook = CookBook::firstOrCreate(Arr::only($data, ['name', 'cover', 'description', 'tips', 'category_id']));

        if (count($data['foods'])) {
            foreach ($data['foods'] as $item) {
                $food = Food::firstOrCreate(['name' => $item['name']]);
                $cookbook->foods()->create([
                    'food_id' => $food->id,
                    'number' => $item['number']
                ]);
            }
        }

        if (count($data['steps'])) {
            $cookbook->steps()->createMany($data['steps']);
        }

        $this->info('创建菜谱：'.$cookbook->name.'成功');
    }

    protected function randomUA()
    {
        $agentArray=[
            //PC端的UserAgent
            "safari 5.1 – MAC"=>"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11",
            "safari 5.1 – Windows"=>"Mozilla/5.0 (Windows; U; Windows NT 6.1; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50",
            "Firefox 38esr"=>"Mozilla/5.0 (Windows NT 10.0; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0",
            "IE 11"=>"Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; InfoPath.3; rv:11.0) like Gecko",
            "IE 9.0"=>"Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0",
            "IE 8.0"=>"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)",
            "IE 7.0"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)",
            "IE 6.0"=>"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)",
            "Firefox 4.0.1 – MAC"=>"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0.1) Gecko/20100101 Firefox/4.0.1",
            "Firefox 4.0.1 – Windows"=>"Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1",
            "Opera 11.11 – MAC"=>"Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; en) Presto/2.8.131 Version/11.11",
            "Opera 11.11 – Windows"=>"Opera/9.80 (Windows NT 6.1; U; en) Presto/2.8.131 Version/11.11",
            "Chrome 17.0 – MAC"=>"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11",
            "傲游（Maxthon）"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Maxthon 2.0)",
            "腾讯TT"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; TencentTraveler 4.0)",
            "世界之窗（The World） 2.x"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
            "世界之窗（The World） 3.x"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; The World)",
            "360浏览器"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; 360SE)",
            "搜狗浏览器 1.x"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; SE 2.X MetaSr 1.0; SE 2.X MetaSr 1.0; .NET CLR 2.0.50727; SE 2.X MetaSr 1.0)",
            "Avant"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Avant Browser)",
            "Green Browser"=>"Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
            //移动端口
            "safari iOS 4.33 – iPhone"=>"Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5",
            "safari iOS 4.33 – iPod Touch"=>"Mozilla/5.0 (iPod; U; CPU iPhone OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5",
            "safari iOS 4.33 – iPad"=>"Mozilla/5.0 (iPad; U; CPU OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5",
            "Android N1"=>"Mozilla/5.0 (Linux; U; Android 2.3.7; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1",
            "Android QQ浏览器 For android"=>"MQQBrowser/26 Mozilla/5.0 (Linux; U; Android 2.3.7; zh-cn; MB200 Build/GRJ22; CyanogenMod-7) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1",
            "Android Opera Mobile"=>"Opera/9.80 (Android 2.3.4; Linux; Opera Mobi/build-1107180945; U; en-GB) Presto/2.8.149 Version/11.10",
            "Android Pad Moto Xoom"=>"Mozilla/5.0 (Linux; U; Android 3.0; en-us; Xoom Build/HRI39) AppleWebKit/534.13 (KHTML, like Gecko) Version/4.0 Safari/534.13",
            "BlackBerry"=>"Mozilla/5.0 (BlackBerry; U; BlackBerry 9800; en) AppleWebKit/534.1+ (KHTML, like Gecko) Version/6.0.0.337 Mobile Safari/534.1+",
            "WebOS HP Touchpad"=>"Mozilla/5.0 (hp-tablet; Linux; hpwOS/3.0.0; U; en-US) AppleWebKit/534.6 (KHTML, like Gecko) wOSBrowser/233.70 Safari/534.6 TouchPad/1.0",
            "UC标准"=>"NOKIA5700/ UCWEB7.0.2.37/28/999",
            "UCOpenwave"=>"Openwave/ UCWEB7.0.2.37/28/999",
            "UC Opera"=>"Mozilla/4.0 (compatible; MSIE 6.0; ) Opera/UCWEB7.0.2.37/28/999",
            "微信内置浏览器"=>"Mozilla/5.0 (Linux; Android 6.0; 1503-M02 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/37.0.0.0 Mobile MQQBrowser/6.2 TBS/036558 Safari/537.36 MicroMessenger/6.3.25.861 NetType/WIFI Language/zh_CN",
            // ""=>"",

        ];

        return Arr::random($agentArray);
    }
}
