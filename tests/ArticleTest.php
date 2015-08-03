<?php

use MartinLindhe\MediawikiClient\Article;

class ArticleTest extends \PHPUnit_Framework_TestCase {

    public function testOne()
    {
        $x = new Article(
            '{{Infobox person'."\n"
            .' | name         = Eminem'."\n"
            .' | birth_date   = {{birth date and age|mf=yes|1972|10|17}}'."\n"
            .' | birth_place  = [[St. Joseph, Missouri]], [[United States|U.S.]]'."\n"
            .' | occupation   = {{flatlist|'."\n"
            .' * Rapper'."\n"
            .' * record producer'."\n"
            .' * songwriter'."\n"
            .' * actor'."\n"
            .' }}'."\n"
            .'}}'."\n"
        );

        //d($x);

        $this->assertEquals('Eminem', $x->name);
        $this->assertEquals('1972-10-17', $x->birth_date->toDateString());
    }

    public function testTwo()
    {
        $x = new Article(
            '{{Infobox scientist'."\n"
            .'| name = Carl Linnaeus (Carl von Linn\u00e9)'."\n"
            .'| image = Carl von Linn\u00e9.jpg'."\n"
            .'| image_size = 220px'."\n"
            .'| alt = Portrait of Linnaeus on a brown background with the word \"Linne\" in the top right corner'."\n"
            .'| caption = "Carl von Linn\u00e9", [[Alexander Roslin]], 1775.<br />Oil painting in the portrait collection at <br />[[Gripsholm Castle]]'."\n"
            .'| birth_date = {{Birth date|df=yes|1707|5|23}}<ref group=note name=birthdate>Carl Linnaeus was born in 1707 on 13 May ([[Swedish calendar|Swedish Style]]) or 23 May according to the modern calendar. According to the [[Julian calendar]] he was born 12 May. (Blunt 2004, p. 12)</ref>'."\n"
            .'| birth_place = [[Råshult]], Stenbrohult parish (now within [[Älmhult Municipality]]), Sweden'."\n"
            .'| death_date = {{Death date and age|df=yes|1778|1|10|1707|5|23}}'."\n"
            .'| death_place = Hammarby ([[estate (land)|estate]]), Danmark parish (outside Uppsala), Sweden'."\n"
            .'}}'."\n"
        );

        $this->assertEquals('1707-05-23', $x->birth_date->toDateString());
        $this->assertEquals('1778-01-10', $x->death_date->toDateString());
    }
}
