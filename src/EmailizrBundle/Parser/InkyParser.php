<?php

namespace EmailizrBundle\Parser;

use Pinky;
use Pimcore\Http\Request\Resolver\EditmodeResolver;

class InkyParser
{
    protected EditmodeResolver $editmodeResolver;

    public function __construct(EditmodeResolver $editmodeResolver)
    {
        $this->editmodeResolver = $editmodeResolver;
    }

    /**
     * @param string $templateHtml
     *
     * @return string
     */
    public function parseInkyHtml($templateHtml)
    {
        if ($this->editmodeResolver->isEditmode() === false) {
            return Pinky\transformString($templateHtml)->saveHTML();
        }

        $templateHtml = preg_replace_callback('/(<script[\s\S]*?>)([\s\S]*?)(<\/script>)/', function ($hit) {
            return $hit[1] . base64_encode($hit[2]) . $hit[3];
        }, $templateHtml);

        $inkedHtml = Pinky\transformString($templateHtml)->saveHTML();

        return preg_replace_callback('/(<script[\s\S]*?>)([\s\S]*?)(<\/script>)/', function ($hit) {
            return $hit[1] . base64_decode($hit[2]) . $hit[3];
        }, $inkedHtml);
    }
}
