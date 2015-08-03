<?php

class MediawikiTest extends \PHPUnit_Framework_TestCase
{
    private function getClient()
    {
        return (new MartinLindhe\MediawikiClient\Client)
            ->server('https://en.wikipedia.org/')
            ->cacheTtlSeconds(3600 * 24 * 30); // 30 days
    }

    public function testArticleFetching()
    {
        $x = $this->getClient()->fetchArticle('Monkey wrench');
        //d($x);
    }

    public function testArticleParsing()
    {
        $article = $this->getClient()->fetchArticle('Eminem');
        //d($article);

        $this->assertEquals('Marshall Bruce Mathers III', $article->birth_name);
    }


    public function testArticleParsing2()
    {
        $article = $this->getClient()->fetchArticle('Madonna (entertainer)');
        //d($article);

        $this->assertEquals('Madonna Louise Ciccone', $article->birth_name);

        $this->assertEquals('1958-08-16', $article->birth_date->toDateString());
    }

    public function testRedirect()
    {
        $article = $this->getClient()->fetchArticle('Carl von Linné'); // #REDIRECT [[Carl Linnaeus]]"}]}}}}

        $this->assertEquals('1707-05-23', $article->birth_date->toDateString());
    }
}
