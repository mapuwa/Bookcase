<?php

namespace App\Model;

use Nette;

class PageWrapper
{
    public function search($query)
    {
        $url = "http://www.databazeknih.cz/search?q=".urlencode($query);
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $html = curl_exec($ch);
        $last_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        curl_close($ch);

        $result = [];
        if ($last_url === false) {
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);

            foreach ($xpath->query ("//p[@class='new_search']") as $section)
            {
                $image = $xpath->query(".//a[@class='search_to_stats']", $section)->item(0);
                $imageURL = $image->childNodes->item(0)->getAttribute('src');

                $title = $xpath->query(".//a[@class='search_to_stats strong']", $section)->item(0)->nodeValue;
                $author = $xpath->query(".//span[@class='smallfind']", $section)->item(0)->nodeValue;
                $result[] = ['image' => $imageURL, 'title' => $title, 'author' => $author];
            }
        }

        return $result;
    }
}