<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * AMOS renderer class is defined here
 *
 * @package   local-amos
 * @copyright 2010 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * AMOS renderer class
 */
class local_amos_renderer extends plugin_renderer_base {

    /**
     * Renders the filter form
     *
     * @todo this code was used as sort of prototype of the HTML produced by the future forms framework, to be replaced by proper forms library
     * @param local_amos_filter $filter
     * @return string
     */
    protected function render_local_amos_filter(local_amos_filter $filter) {
        $output = '';

        // version checkboxes
        $output .= html_writer::start_tag('div', array('class' => 'item checkboxgroup yui3-gd'));
        $output .= html_writer::start_tag('div', array('class' => 'label yui3-u first'));
        $output .= html_writer::tag('label', 'Version', array('for' => 'amosfilter_fver'));
        $output .= html_writer::tag('div', 'Show strings from these Moodle versions', array('class' => 'description'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::start_tag('div', array('class' => 'element yui3-u'));
        $fver = '';
        foreach (mlang_version::list_translatable() as $version) {
            $checkbox = html_writer::checkbox('fver[]', $version->code, in_array($version->code, $filter->get_data()->version),
                    $version->label);
            $fver .= html_writer::tag('div', $checkbox, array('class' => 'labelled_checkbox'));
        }
        $output .= html_writer::tag('div', $fver, array('id' => 'amosfilter_fver', 'class' => 'checkboxgroup'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        // language selector
        $output .= html_writer::start_tag('div', array('class' => 'item select yui3-gd'));
        $output .= html_writer::start_tag('div', array('class' => 'label yui3-u first'));
        $output .= html_writer::tag('label', 'Languages', array('for' => 'amosfilter_flng'));
        $output .= html_writer::tag('div', 'Display translations in these languages', array('class' => 'description'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::start_tag('div', array('class' => 'element yui3-u'));
        $options = mlang_tools::list_languages();
        foreach ($options as $langcode => $langname) {
            $options[$langcode] = $langname . ' (' . $langcode . ')';
        }
        unset($options['en']); // English is not translatable via AMOS
        $output .= html_writer::select($options, 'flng[]', $filter->get_data()->language, '',
                    array('id' => 'amosfilter_flng', 'multiple' => true, 'size' => 1));
        $output .= html_writer::tag('span', '', array('id' => 'amosfilter_flng_actions', 'class' => 'actions'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        // component selector
        $output .= html_writer::start_tag('div', array('class' => 'item select yui3-gd'));
        $output .= html_writer::start_tag('div', array('class' => 'label yui3-u first'));
        $output .= html_writer::tag('label', 'Component', array('for' => 'amosfilter_fcmp'));
        $output .= html_writer::tag('div', 'Show strings of these components', array('class' => 'description'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::start_tag('div', array('class' => 'element yui3-u'));
        $options = array();
        foreach (mlang_tools::list_components() as $componentname => $undefined) {
            $options[$componentname] = $componentname;
        }
        $output .= html_writer::select($options, 'fcmp[]', $filter->get_data()->component, '',
                    array('id' => 'amosfilter_fcmp', 'multiple' => true, 'size' => 5));
        $output .= html_writer::tag('span', '', array('id' => 'amosfilter_fcmp_actions', 'class' => 'actions'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        // other filter settings
        $output .= html_writer::start_tag('div', array('class' => 'item checkboxgroup yui3-gd'));
        $output .= html_writer::start_tag('div', array('class' => 'label yui3-u first'));
        $output .= html_writer::tag('label', 'Miscellaneous', array('for' => 'amosfilter_fmis'));
        $output .= html_writer::tag('div', 'Additional conditions on strings to display', array('class' => 'description'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::start_tag('div', array('class' => 'element yui3-u'));

        $fmis    = html_writer::checkbox('fmis', 1, $filter->get_data()->missing, 'missing and outdated strings only');
        $fmis    = html_writer::tag('div', $fmis, array('class' => 'labelled_checkbox'));

        $fhlp    = html_writer::checkbox('fhlp', 1, $filter->get_data()->helps, 'help tooltips only');
        $fhlp    = html_writer::tag('div', $fhlp, array('class' => 'labelled_checkbox'));

        $output .= html_writer::tag('div', $fmis . $fhlp, array('id' => 'amosfilter_fmis', 'class' => 'checkboxgroup vertical'));

        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        // must contain string
        $output .= html_writer::start_tag('div', array('class' => 'item text yui3-gd'));
        $output .= html_writer::start_tag('div', array('class' => 'label yui3-u first'));
        $output .= html_writer::tag('label', 'Substring', array('for' => 'amosfilter_ftxt'));
        $output .= html_writer::tag('div', 'String must contain given text (comma separated list of values)', array('class' => 'description'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::start_tag('div', array('class' => 'element yui3-u'));

        $output .= html_writer::empty_tag('input', array('name' => 'ftxt', 'type' => 'text', 'value' => $filter->get_data()->substring));

        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        // hidden fields
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '__lazyform_' . $filter->lazyformname, 'value' => 1));
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));

        // submit
        $output .= html_writer::start_tag('div', array('class' => 'item submit yui3-gd'));
        $output .= html_writer::start_tag('div', array('class' => 'label yui3-u first'));
        $output .= html_writer::tag('label', '&nbsp;', array('for' => 'amosfilter_fsbm'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::start_tag('div', array('class' => 'element yui3-u'));
        $output .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => 'Save filter settings'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');

        // block wrapper for xhtml strictness
        $output = html_writer::tag('div', $output, array('id' => 'amosfilter'));

        // form
        $attributes = array('method' => 'get',
                            'action' => $filter->handler->out(),
                            'id'     => html_writer::random_id(),
                            'class'  => 'lazyform ' . $filter->lazyformname,
                        );
        $output = html_writer::tag('form', $output, $attributes);
        $output = html_writer::tag('div', $output, array('class' => 'filterwrapper'));

        return $output;
    }

    /**
     * Renders the translation tool
     *
     * @param local_amos_translator $translator
     * @return string
     */
    protected function render_local_amos_translator(local_amos_translator $translator) {

        $table = new html_table();
        $table->id = 'amostranslator';
        $table->head = array('Component', 'Identifier', 'Ver', 'Original', 'Lang', 'Translation');
        $table->colclasses = array('component', 'stringinfo', 'version', 'original', 'lang', 'translation');

        if (empty($translator->strings)) {
            return $this->heading('No strings found');
        } else {
            $output = $this->heading('Found ' . count($translator->strings) . ' strings');
        }
        $missing = 0;

        foreach ($translator->strings as $string) {
            $cells = array();
            // component name
            $cells[0] = new html_table_cell($string->component);
            // string identification code and some meta information
            $t  = html_writer::tag('div', s($string->stringid), array('class' => 'stringid'));
            $t .= html_writer::tag('div', s($string->metainfo), array('class' => 'metainfo'));
            $cells[1] = new html_table_cell($t);
            // moodle version to put this translation onto
            $cells[2] = new html_table_cell($string->branch);
            // original of the string
            $cells[3] = new html_table_cell(html_writer::tag('div', s($string->original), array('class' => 'preformatted')));
            // the language in which the original is displayed
            $cells[4] = new html_table_cell($string->language);
            // Translation
            if (empty($string->translation)) {
                $missing++;
            }
            $t = s($string->translation);
            $sid = local_amos_translator::encode_identifier($string->language, $string->originalid, $string->translationid);
            $t = html_writer::tag('div', $t, array('class' => 'preformatted translation-view'));
            $i = html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'fields[]', 'value' => $sid));
            $cells[5] = new html_table_cell($t . $i);
            $cells[5]->id = $sid;
            $cells[5]->attributes['class'] = $string->class;
            // todo check if the user can translate into this language, allowing for all users now
            $cells[5]->attributes['class'] .= ' translateable';
            $row = new html_table_row($cells);
            $table->data[] = $row;
        }

        $output .= html_writer::table($table);
        $output = html_writer::tag('div', $output, array('class' => 'translatorwrapper'));

        return $output;
    }

    /**
     * Renders the stage
     *
     * @param local_amos_stage $stage
     * @return string
     */
    protected function render_local_amos_stage(local_amos_stage $stage) {
        global $CFG;

        $table = new html_table();
        $table->id = 'amosstage';
        $table->head = array('Component', 'Identifier', 'Ver', 'Original', 'Lang', 'Translation');
        $table->colclasses = array('component', 'stringinfo', 'version', 'original', 'lang', 'translation');

        if (empty($stage->strings)) {
            return $this->heading('No strings staged');
        } else {
            $output = $this->heading('Staged ' . count($stage->strings) . ' strings');
        }

        $form = html_writer::tag('textarea', '', array('name' => 'message'));
        $form .= html_writer::empty_tag('input', array('name' => 'sesskey', 'value' => sesskey(), 'type' => 'hidden'));
        $button = html_writer::empty_tag('input', array('value' => 'Commit', 'type' => 'submit'));
        $button = html_writer::tag('div', $button);
        $form = html_writer::tag('div', $form . $button);
        $form = html_writer::tag('form', $form, array('method' => 'post', 'action' => $CFG->wwwroot . '/local/amos/stage.php'));
        $form = html_writer::tag('div', $form, array('class' => 'commitformwrapper'));
        $output .= $form;

        foreach ($stage->strings as $string) {
            $cells = array();
            // component name
            $cells[0] = new html_table_cell($string->component);
            // string identification code and some meta information
            $t  = html_writer::tag('div', s($string->stringid), array('class' => 'stringid'));
            $cells[1] = new html_table_cell($t);
            // moodle version to put this translation onto
            $cells[2] = new html_table_cell($string->version);
            // original of the string
            $cells[3] = new html_table_cell(html_writer::tag('div', s($string->original), array('class' => 'preformatted')));
            // the language in which the original is displayed
            $cells[4] = new html_table_cell($string->language);
            // the current and the new translation
            $t1 = s($string->current);
            $t1 = html_writer::tag('del', $t1, array());
            $t1 = html_writer::tag('div', $t1, array('class' => 'current preformatted'));
            $t2 = s($string->new);
            $t2 = html_writer::tag('div', $t2, array('class' => 'new preformatted'));
            $cells[5] = new html_table_cell($t2 . $t1);

            $row = new html_table_row($cells);
            $table->data[] = $row;
        }

        $output .= html_writer::table($table);
        $output = html_writer::tag('div', $output, array('class' => 'stagewrapper'));

        return $output;

    }

    /**
     * Returns formatted commit date and time
     *
     * In our git repos, timestamps are stored in UTC always and that is what standard got log
     * displays.
     * TODO xxx not used, may be removed?
     *
     * @param int $timestamp
     * @return string formatted date and time
     */
    public static function commit_datetime($timestamp) {
        $tz = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $t = date('Y-m-d H:i', $timestamp);
        date_default_timezone_set($tz);
        return $t;
    }


}

