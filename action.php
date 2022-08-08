<?php

/**
 * Plugin TableWidth
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <spambox03@mail.ru>
 */

/* Must be run within Dokuwiki */
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once(DOKU_PLUGIN . 'action.php');

class action_plugin_tablewidth extends DokuWiki_Action_Plugin {

    /**
     * Register callbacks
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('RENDERER_CONTENT_POSTPROCESS', 'AFTER', $this, 'replaceComments');
    }

    /**
     * Replace table-width comments by HTML
     */
    public function replaceComments(&$event, $param) {
        if ($event->data[0] == 'xhtml') {
            $pattern = '/(<!-- table-width [^\n]+? -->\n)([^\n]*<table.*?>)(\s*<t)/';
            $flags = PREG_SET_ORDER | PREG_OFFSET_CAPTURE;

            if (preg_match_all($pattern, $event->data[1], $match, $flags) > 0) {
                $start = 0;
                $html = '';

                foreach ($match as $data) {
                    $html .= substr($event->data[1], $start, $data[0][1] - $start);
                    $html .= $this->processTable($data);
                    $start = $data[0][1] + strlen($data[0][0]);
                }

                $event->data[1] = $html . substr($event->data[1], $start);;
            }
        }
    }

    /**
     * Convert table-width comments and table mark-up into final HTML
     */
    private function processTable($data) {
        preg_match('/<!-- table-width ([^\n]+?) -->/', $data[1][0], $match);

        $width = preg_split('/\s+/', $match[1]);
        $tableWidth = array_shift($width);

        if ($tableWidth != '-') {
            $table = $this->styleTable($data[2][0], $tableWidth);
        }
        else {
            $table = $data[2][0];
        }

        return $table . $this->renderColumns($width) . $data[3][0];
    }

    /**
     * Add width style to the table
     */
    private function styleTable($html, $width) {
        preg_match('/^([^\n]*<table)(.*?)(>)$/', $html, $match);

        $entry = $match[1];
        $attributes = $match[2];
        $exit = $match[3];
        $widthStyle = 'min-width: 0px; width: ' . $width . ';';

        if (preg_match('/(.*?style\s*=\s*(["\']).*?)(\2.*)/', $attributes, $match) == 1) {
            $attributes = $match[1] . '; ' . $widthStyle . $match[3];
        }
        else {
            $attributes .= ' style="' . $widthStyle . '"';
        }

        return $entry . $attributes . $exit;
    }

    /**
     * Render column tags
     */
    private function renderColumns($width) {
        $html = DOKU_LF;

        foreach ($width as $w) {
            if ($w != '-') {
                $html .= '<col style="width: ' . $w . '" />';
            }
            else {
                $html .= '<col />';
            }
        }

        return $html;
    }
}
