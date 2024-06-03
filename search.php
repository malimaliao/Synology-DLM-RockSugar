<?php
/**
 *  RockSugarSearch
 */

class RockSugarSearch
{
    private $url_domain = 'https://cili.xfuse.fun';
    private $qurl = '/toSearch';
    private $qpage = 'https://cili.xfuse.fun/magnet/%s/0';
    public $debug = false;

    public function __construct()
    {
    }

    public function prepare($curl, $query)
    {
        // get
        # $this->configureCurl($curl, sprintf($this->url_domain . $this->qurl, urlencode($query)), 0, '');
        // post
        $post_data = array("keyword" => $query, "page" => "0", "size" => "15");
        $this->configureCurl($curl, $this->url_domain . $this->qurl, 1, $post_data);
    }

    public function parse($plugin, $response)
    {
        $arrayData = json_decode($response, true);
        if ($arrayData["msg"] != "success") {
            return;
        }
        // Accessing values in the array
        foreach ($arrayData['data']['content'] as $item) {
            // $plugin->addResult(title, download, size, datetime, fromPage, hash, seeders, leechers, category);
            $title = urldecode($item['name']);
            if(substr($title, -1) == '1'){ // 删除莫名其妙的“1”
                $title = substr($title, 0,-1);
            }
            $download = $this->getDownloadLink($item["btih"], $item["name"]);
            $size = (float)$item["size"];
            $datetime = date('Y-m-d', (int)$item["timestamp"] / 1000);
            $fromPage = sprintf($this->qpage, $item["btih"]);
            $hash = $item["btih"];
            $seeders = 0;
            $leechers = 0;
            $category = 'Video';

            if ($this->debug) { // debug
                echo $title . '<br>' . $download . '<br>' . $size . '<br>' . $datetime . '<br>' . $fromPage . '<br>';
                echo $hash . '<br>' . $seeders . '<br>' . $leechers . '<br>' . $category . '<hr>';
            } else {
                // add to plugin..
                $plugin->addResult($title, $download, $size, $datetime, $fromPage, $hash, $seeders, $leechers, $category);
            }
        }
    }

    private function configureCurl($curl, $url, $POST, $field_data="")
    {
        $headers = array
        (
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*;q=0.8',
            'Accept-Language: ru,en-us;q=0.7,en;q=0.3',
            'Accept-Encoding: deflate',
            'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7'
        );

        #curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_FAILONERROR, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36 Edg/125.0.0.0');
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        if($POST == 1){ // post
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $field_data);
        }
    }

    private function getDownloadLink($info_hash, $name)
    {
        return "magnet:?xt=urn:btih:" . $info_hash . "&dn=" . urlencode($name)
            . '&tr=' . urlencode('udp://tracker.opentrackr.org:1337')
            . '&tr=' . urlencode('udp://tracker.openbittorrent.com:6969/announce')
            . '&tr=' . urlencode('udp://open.stealth.si:80/announce')
            . '&tr=' . urlencode('udp://tracker.torrent.eu.org:451/announce')
            . '&tr=' . urlencode('udp://tracker.bittor.pw:1337/announce')
            . '&tr=' . urlencode('udp://public.popcorn-tracker.org:6969/announce')
            . '&tr=' . urlencode('udp://tracker.dler.org:6969/announce')
            . '&tr=' . urlencode('udp://exodus.desync.com:6969')
            . '&tr=' . urlencode('udp://opentracker.i2p.rocks:6969/announce');
    }
}
