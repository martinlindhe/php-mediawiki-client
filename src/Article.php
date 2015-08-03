<?php namespace MartinLindhe\MediawikiClient;

use Carbon\Carbon;

class Article
{
    // infobox variables:

    /** @var string */
    var $name;

    /** @var string */
    var $data;

    public function __construct($data)
    {
        $this->data = $data;

        preg_match('/(?=\{Infobox)(\{([^{}]|(?1))*\})/s', $data, $infobox);
        if ($infobox) {
            $lines = explode("\n", $infobox[0]);

            foreach ($this->convertInfoBoxToKeyValuePairs($lines) as $key => $val) {
                $this->$key = $val;
            }
        }
    }

    /**
     * @param $lines
     * @return array
     */
    private function convertInfoBoxToKeyValuePairs($lines)
    {
        $res = [];

        foreach ($lines as $line) {
            //d($line);
            if (strpos($line, '=') === false) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);

            $key = trim(str_replace('|', '', $key));
            $res[$key] = $this->parseValue($value);

            // TODO parse into array:
            /*
                    "| occupation   = {{flatlist|"
                    "* Rapper"
                    "* record producer"
                    "* songwriter"
                    "* actor"
                    "}}"
             */
        }
        return $res;
    }

    private function parseValue($value)
    {
        $value = trim($value);

        // strip <ref> tag if exists:
        $value = preg_replace('/<ref([ \w=]+)>?(.*)?<\/ref>/i', '', $value);

        if (substr($value, 0, 2) == '{{' && substr($value, -2) == '}}') {
            // https://en.wikipedia.org/wiki/Template:Death_date_and_age
            // {{birth date and age|mf=yes|1972|10|17}}
            // {{Birth date|df=yes|1707|5|23}}
            // {{Death date and age|df=yes|1778|1|10|1707|5|23}}
            // {{birth date|1889|4|20|df=yes}}
            $value = substr($value, 2, -2);
            $parts = explode('|', $value);
            $parts[0] = strtolower($parts[0]);

            if (in_array($parts[0], ['birth date and age', 'birth date', 'death date and age'])) {

                $date = [];
                for ($i = 1; $i < count($parts); $i++) {
                    // df/mf  = date first, month first (controls date display on wp, we can ignore)
                    if (in_array($parts[$i], ['mf=yes', 'df=yes', 'df=y', 'mf=y'])) {
                        continue;
                    }
                    $date[] = $parts[$i];
                }
                return Carbon::create($date[0], $date[1], $date[2]);
            }
        }

        //dbg('unrecognized value format: '.$value);

        return $value;
    }
}
