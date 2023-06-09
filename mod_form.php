<?php

/**
 * Module form definition
 *
 * @package    mod
 * @subpackage tiddlywiki
 * @copyright  Copyright
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class mod_tiddlywiki_mod_form extends moodleform {

    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('tiddlywikiname', 'mod_tiddlywiki'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('textarea', 'wikitext', get_string('tiddlywikitext', 'mod_tiddlywiki'), array('cols' => '50', 'rows' => '15'));
        $mform->setType('wikitext', PARAM_RAW);

    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (empty($data['wikitext'])) {
            $errors['wikitext'] = get_string('required');
        }
        return $errors;
    }

    public function data_preprocessing(&$default_values) {
        if (!empty($this->_instance)) {
            $default_values['wikitext'] = $this->_instance->wikitext;
        }
    }

    public function get_data() {
        $data = parent::get_data();
        if ($data) {
            $data->wikitext = clean_param($data->wikitext, PARAM_RAW);
        }
        return $data;
    }
    
    public function appification($editor = ''){
        global $CFG;
        $mform = $this->_form;
        // In Moodle 3.11 or later, set the component to 'mod_tiddlywiki' for better handling in the app.
        if ($CFG->version >= 2021051700) {
            $mform->addElement('hidden', 'component', 'mod_tiddlywiki');
        }
        return $editor;
    }
}
