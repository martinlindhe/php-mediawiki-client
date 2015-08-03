<?php namespace MartinLindhe\MediawikiClient;

use Httpful\Request;
use MartinLindhe\Traits\DiskCacheTrait;
use MartinLindhe\UserAgentTrait\UserAgentTrait;

class Client
{
    use DiskCacheTrait;
    use UserAgentTrait;

    /** @var string */
    protected $server;

    /**
     * @param string $host
     * @return $this
     */
    public function server($host)
    {
        $this->server = $host;
        return $this;
    }

    /**
     * @param string $title
     * @return Article|null
     */
    public function fetchArticle($title)
    {
        $url = 'https://' . $this->server . '/w/api.php?'
            . http_build_query([
                'action' => 'query',
                'prop' => 'revisions',
                'rvprop' => 'content',
                'format' => 'json',
                'titles' => $title,
            ]);

        $data = $this->load($url);
        if ($data) {
            return $this->parseResponse($data);
        }

        nfo('GET ' . $url);

        /** @var \Httpful\Response $response HACK for httpful */
        $response = Request::get($url)
            ->addHeader('User-Agent', $this->userAgent)
            ->send();

        $this->store($url, $response->raw_body);

        return $this->parseResponse($response->raw_body);
    }

    private function parseResponse($data)
    {
        $res = json_decode($data);
        if (!$res) {
            return null;
        }

        foreach ($res->query->pages as $page) {
            if (!isset($page->revisions[0])) {
                return null;
            }

            $content = trim($page->revisions[0]->{'*'});
            if (substr($content, 0, 9) == '#REDIRECT') {
                $x = explode(' ', $content, 2);

                $redirectTo = self::stripMediawikiLinks($x[1]);
                nfo('REDIRECT: '.$redirectTo);
                return $this->fetchArticle($redirectTo);
            }

            return new Article($content);
        }
        return null;
    }

    public static function stripMediawikiLinks($s)
    {
        $s = str_replace('[', '', $s);
        $s = str_replace(']', '', $s);
        return $s;
    }
}
