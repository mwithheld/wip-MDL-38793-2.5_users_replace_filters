<?php

require_once($CFG->libdir.'/formslib.php');

class user_add_filter_form extends moodleform {

    function definition() {
        $mform       =& $this->_form;
        $fields      = $this->_customdata['fields'];
        $extraparams = $this->_customdata['extraparams'];

        $mform->addElement('header', 'newfilter', get_string('newfilter','filters'));

        foreach($fields as $ft) {
            $ft->setupForm($mform);
        }

        // in case we wasnt to track some page params
        if ($extraparams) {
            foreach ($extraparams as $key=>$value) {
                $mform->addElement('hidden', $key, $value);
                $mform->setType($key, PARAM_RAW);
            }
        }

        // Add + replace filters buttons
        $objs = array();
        $objs[] = &$mform->createElement('submit', 'addfilter', get_string('addfilter','filters'));
        $mform->addElement('group', 'addfiltergrp', '', $objs, ' ', false);
    }
    
    function definition_after_data() {
        global $SESSION;

        if (!empty($SESSION->user_filtering)) {
            $mform       =& $this->_form;
            $mform->removeElement('addfiltergrp');

            $objs = array();
            $objs[] = &$mform->createElement('submit', 'addfilter', get_string('addfilter','filters'));
            $objs[] = &$mform->createElement('submit', 'replaceall', get_string('replacefilters','filters'));
            $mform->addElement('group', 'addfiltergrp', '', $objs, ' ', false);
        }
    }
}

class user_active_filter_form extends moodleform {

    function definition() {
        global $SESSION; // this is very hacky :-(

        $mform       =& $this->_form;
        $fields      = $this->_customdata['fields'];
        $extraparams = $this->_customdata['extraparams'];

        if (!empty($SESSION->user_filtering)) {
            // add controls for each active filter in the active filters group
            $mform->addElement('header', 'actfilterhdr', get_string('actfilterhdr','filters'));

            foreach ($SESSION->user_filtering as $fname=>$datas) {
                if (!array_key_exists($fname, $fields)) {
                    continue; // filter not used
                }
                $field = $fields[$fname];
                foreach($datas as $i=>$data) {
                    $description = $field->get_label($data);
                    $mform->addElement('checkbox', 'filter['.$fname.']['.$i.']', null, $description);
                }
            }

            if ($extraparams) {
                foreach ($extraparams as $key=>$value) {
                    $mform->addElement('hidden', $key, $value);
                    $mform->setType($key, PARAM_RAW);
                }
            }

            $objs = array();
            $objs[] = &$mform->createElement('submit', 'removeselected', get_string('removeselected','filters'));
            $objs[] = &$mform->createElement('submit', 'removeall', get_string('removeall','filters'));
            $mform->addElement('group', 'actfiltergrp', '', $objs, ' ', false);
        }
    }
}
