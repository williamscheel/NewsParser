<?php

namespace App\Console\Commands;

use App\Models\Rss_Log;
use App\Models\Rss_News;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GetNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getNews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Выгрузить новости';

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
     * @return int
     */
    public function handle()
    {
        if(Rss_Log::where('active', 0)->first()) die('Еще не закончилось выполнение предыдущей команды');
        $this->rowDB = Rss_Log::firstOrCreate(['active' => 0]);
        try{
            $this->getFeeds();
            if($this->rowDB->response_HTTP_code == 200 ){
                if($this->rowDB->responseBody == '') {
                    $this->rowDB->update(['active' => 1]);
                    die('Ответ пришел пустым');
                }
                $data = new \SimpleXmlElement($this->rowDB->responseBody);
                foreach($data->channel->item as $item){
                    $news = Rss_News::firstOrCreate([
                        'name' => $item->title,
                        'shortDescription' => $item->description
                    ],[
                        'name' => $item->title,
                        'shortDescription' => $item->description,
                        'pubDate' => date('Y-m-d H:i:s', strtotime($item->pubDate)),
                        'author' => $item->author ?? 'Не указан',
                        'image' => ''
                    ]);
                    if($news->wasRecentlyCreated === true && !empty($item->children('rbc_news', TRUE)->image)){
                        $imageData = $item->children('rbc_news', TRUE)->image;
                        $file = file_get_contents($imageData->url);
                        $imageType = substr(stristr($imageData->type, '/'), 1);
                        Storage::put('public/RSS_image/'.$news->id.'.'.$imageType, $file);
                        $news->update(['image'=>$news->id.'.'.$imageType]);
                    }
                }
                $this->rowDB->update(['active' => 1]);
                return 1;
            }
            $this->rowDB->update(['active' => 1]);
            die('Статус ответа: '.$this->rowDB->response_HTTP_code . ' Error: '. $this->rowDB->error);
        } catch(\Throwable $e){
            $this->rowDB->update([
                'active' => 1,
                'error' => 'Error: '.$e->getMessage() . ' Line: '.$e->getLine()
            ]);
            die('Error: '.$e->getMessage() . ' Line: '.$e->getLine());
        }
    }

    private function getFeeds(){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://static.feed.rbc.ru/rbc/logical/footer/news.rss',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_USERAGENT => 'curl/'.curl_version()['version']
        ));
        $result = curl_exec($curl);

        $this->rowDB->update([
            'requestMethod' => 'method',
            'requestURL' => curl_getinfo($curl, CURLINFO_EFFECTIVE_URL),
            'response_HTTP_code' => curl_getinfo($curl, CURLINFO_HTTP_CODE),
            'responseBody' => $result,
            'executionTime' => curl_getinfo($curl, CURLINFO_TOTAL_TIME) * 1000,
            'error' => curl_error($curl) ?? null
        ]);
        curl_close($curl);
    }
}
