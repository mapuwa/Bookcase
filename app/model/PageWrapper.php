<?php

namespace App\Model;

use Nette;

class PageWrapper
{
    public function search($query)
    {
        $url = "http://www.databazeknih.cz/search?q=".urlencode($query);
        $r = $this->getHtmlCode($url);
        $html = $r['content'];
        $result = [];
        if ($r['redirect'] === false) {
            $result = $this->parseSearchResults($html);
        }
        else {
            $re = $this->parseBookPage($html);
            $re['author'] = $re['datePublished'] . ', ' . join(', ', $re['authors']);
            $re['link'] = $r['url'];
            $result[] = $re ;
        }
        return $result;
    }
    public function getPage($url)
    {
        $urlExt = $url . '?show=binfo';
        return $this->parseBookPage($this->getHtmlCode($urlExt)['content']);
    }
    public function getHtmlCode($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $html = curl_exec($ch);
        $last_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        curl_close($ch);
        if ($last_url !== false) {
            $html = $this->getHtmlCode($last_url . '?show=binfo')['content'];
        }
        return ['content' => $html, 'redirect' => !!$last_url, 'url' => !!$last_url ? $last_url : $url];
    }
    protected function parseSearchResults($html)
    {
        $result = [];
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        foreach ($xpath->query ("//p[@class='new_search']") as $section) {
            $image = $xpath->query(".//a[@class='search_to_stats']", $section)->item(0);
            $imageURL = $image->childNodes->item(0)->getAttribute('src');
            $title = $xpath->query(".//a[@class='search_to_stats strong']", $section)->item(0)->nodeValue;
            $link = $xpath->query(".//a[@class='search_to_stats strong']", $section)->item(0)->getAttribute('href');
            $author = $xpath->query(".//span[@class='smallfind']", $section)->item(0)->nodeValue;
            $result[] = [
                'image' => $imageURL,
                'title' => $title,
                'author' => $author,
                'link' => $link
            ];
        }
        return $result;
    }
    protected function parseBookPage($html)
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $authors = [];
        $auth = $xpath->query("//*[@itemprop='author']")->item(0);
        foreach ( $xpath->query(".//a", $auth) as $author) {
              $authors[] = $author->nodeValue;
        }

        return [
            'authors' => $authors,
            'image' => $xpath->query("//*[@class='kniha_img']")->item(0)->getAttribute('src'),
            'title' => $xpath->query("//*[@itemprop='name']")->item(0)->nodeValue,
            'description' => $xpath->query("//*[@itemprop='description']")->item(0)->nodeValue,
            'genre' => $xpath->query("//*[@itemprop='category']")->item(0)->childNodes->item(0)->nodeValue,
            'datePublished' => $xpath->query("//*[@itemprop='datePublished']")->item(0)->nodeValue,
            'publisher' => $xpath->query("//*[@itemprop='publisher']")->item(0)->childNodes->item(0)->nodeValue,
            'pages' => $xpath->query("//*[@itemprop='numberOfPages']")->item(0)->childNodes->item(0)->nodeValue,
            'isbn' => $xpath->query("//*[@itemprop='isbn']")->item(0)->nodeValue
        ];

    }
}