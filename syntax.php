<?php

/**
 * Plugin TableWidth
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mykola Ostrovskyy <spambox03@mail.ru>
 */

class syntax_plugin_tablewidth extends DokuWiki_Syntax_Plugin {

    private $mode;

    public function __construct() {
        $this->mode = substr(get_class($this), 7);
    }

    public function getType() {
        return 'container';
    }

    public function getPType() {
        return 'block';
    }

    public function getSort() {
        return 5;
    }

    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('[\t ]*\n\|<[^\n]+?>\|(?=\s*?\n[|^])', $mode, $this->mode);
    }

    public function handle($match, $state, $pos, Doku_Handler $handler) {
        if ($state == DOKU_LEXER_SPECIAL) {
            if (preg_match('/\|<\s*(.+?)\s*>\|/', $match, $match) != 1) {
                return false;
            }

            return array($match[1]);
        }

        return false;
    }

    public function render($mode, Doku_Renderer $renderer, $data) {
        if ($mode == 'xhtml') {
            $renderer->doc .= '<!-- table-width ' . $data[0] . ' -->' . DOKU_LF;

            return true;
        }

        return false;
    }
}
