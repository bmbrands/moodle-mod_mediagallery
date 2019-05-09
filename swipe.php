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
 * Prints a particular instance of mediagallery
 *
 * @package    mod_mediagallery
 * @copyright  Bas Brands
 * @author     Bas Brands <bas@sonsbeekmedia.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

$g = optional_param('g', 0, PARAM_INT); // A mediagallery_gallery id.
$page = optional_param('page', 0, PARAM_ALPHA);

if (!$g) {
    print_error('missingparameter');
}

$gallery = new \mod_mediagallery\gallery($g, []);
$gallery->sync(false);
$m = $gallery->instanceid;
$mediagallery = $gallery->get_collection();
$course     = $DB->get_record('course', array('id' => $mediagallery->course), '*', MUST_EXIST);
$cm = $mediagallery->cm;

$PAGE->requires->strings_for_js(array(
        'addcomment',
        'comments',
        'commentscount',
        'commentsrequirelogin',
        'deletecommentbyon'
    ),
    'moodle'
);

$context = context_module::instance($cm->id);

$pageurl = new moodle_url('/mod/mediagallery/swipe.php', array('g' => $g, 'page' => 0));
$PAGE->set_cm($cm, $course);
$PAGE->set_url($pageurl);
require_login($course, true, $cm);

$renderer = $PAGE->get_renderer('mod_mediagallery');

if ($page === 'exportxls') {
    $renderer->view_cards_report_xls($gallery);
    exit(0);
}

echo $OUTPUT->header(null, true);

if ($page === 'storefeedback') {
    $gallery->store_feedback('FOOP!');
} else {

if ($page === 'report') {
	if (has_capability('mod/mediagallery:grade', $context)) {
		echo $renderer->view_cards_report($gallery, array('filter' => true));
	}
} else {
	echo $renderer->view_cards($gallery, $context);
}

}

echo $OUTPUT->footer();

