<?php

namespace App\Console\Commands;

use App\Models\Category;
use Goutte\Client;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class GatherCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gather:category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'gather categories';

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

        $list = [];
        $crawler = $client->request('GET', 'caipu/');
        $crawler->filter('.rec_classify_cell>h3')->each(function (Crawler $node) use ($crawler, &$list) {
            $name = $node->text();
            $children = $node->nextAll()->eq(0)->filter('a')->each(function (Crawler $n) {
               return ['name' => $n->text()];
            });

            $list[] = compact('name', 'children');
        });

        foreach ($list as $data) {
            $this->createCategory($data);
        }
    }

    protected function createCategory($data, $parent = null)
    {
        // 创建一个新的类目对象
        $category = Category::firstOrNew(['name' => $data['name']]);
        // 如果有 children 字段则代表这是一个父类目
        $category->is_directory = isset($data['children']);
        // 如果有传入 $parent 参数，代表有父类目
        if (!is_null($parent)) {
            $category->parent()->associate($parent);
        }
        //  保存到数据库
        $category->save();
        // 如果有 children 字段并且 children 字段是一个数组
        if (isset($data['children']) && is_array($data['children'])) {
            // 遍历 children 字段
            foreach ($data['children'] as $child) {
                // 递归调用 createCategory 方法，第二个参数即为刚刚创建的类目
                $this->createCategory($child, $category);
            }
        }
    }
}
