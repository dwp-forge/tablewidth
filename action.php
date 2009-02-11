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

class action_plugin_refnotes extends DokuWiki_Action_Plugin {

    /**
     * Return some info
     */
    function getInfo() {
        return array(
            'author' => 'Mykola Ostrovskyy',
            'email'  => 'spambox03@mail.ru',
            'date'   => '2009-02-11',
            'name'   => 'Table Width',
            'desc'   => 'Allows to specify width of table columns.',
            'url'    => 'http://code.google.com/p/dwp-forge/',
        );
    }

    /**
     * Register callbacks
     */
    function register(&$controller) {
        $controller->register_hook('RENDERER_CONTENT_POSTPROCESS', 'AFTER', $this, 'replaceComments');
    }

    /**
     * Replaces table-width comments by <col> tags
     */
    function replaceComments(&$event, $param) {
        if ($event->data[0] == 'xhtml') {
            $pattern = '/(<!-- table-width [^\n]+? -->)(\s*<table.*?>\s*)(<)/';
            $flags = PREG_SET_ORDER | PREG_OFFSET_CAPTURE;
            if (preg_match($pattern, $event->data[0] $match, $flags) > 0) {
            }
        }
    }
}
