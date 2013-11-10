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
require_once(DOKU_PLUGIN . 'syntax.php');

class syntax_plugin_tablewidth extends DokuWiki_Syntax_Plugin {

    var $mode;

    function syntax_plugin_tablewidth() {
        $this->mode = substr(get_class($this), 7);
    }

    function getType() {
        return 'substition';
    }

    function getPType() {
        return 'block';
    }

    function getSort() {
        return 5;
    }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\s*\n\|<[^\n]+?>\|(?=\s*?\n[|^])', $mode, $this->mode);
    }

    function handle($match, $state, $pos, &$handler) {
        if ($state == DOKU_LEXER_SPECIAL) {
            if (preg_match('/(\s*)\|<\s*(.+?)\s*>\|/', $match, $match) != 1) {
                return false;
            }
            /* HACK: In Binky release table entry pattern became so greedy, that in ordere to compete 
             * with it TW has to consume all leading new line characters. This breaks detection of table
             * end on a table immediately preceding the one with TW syntax. Here we detect if we are in
             * the middle of a table and there was an empty line in front of TW, and then force-close
             * the previous table. Ugly. See https://bugs.dokuwiki.org/index.php?do=details&task_id=1833
             */
            if (substr_count($match[1], "\n") > 1 && $handler->CallWriter instanceof Doku_Handler_Table) {
                $handler->table("", DOKU_LEXER_EXIT, $pos);
                $this->Lexer->_mode->leave();
            }
            return array($match[2]);
        }
        return false;
    }

    function render($mode, &$renderer, $data) {
        if ($mode == 'xhtml') {
            $renderer->doc .= '<!-- table-width ' . $data[0] . ' -->' . DOKU_LF;
            return true;
        }
        return false;
    }
}
