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
 * Unit tests for mod_tiddlywiki lib
 *
 * @package    mod_tiddlywiki
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Unit tests for mod_tiddlywiki lib
 *
 * @package    mod_tiddlywiki
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_tiddlywiki_lib_testcase extends advanced_testcase {

    /**
     * Prepares things before this test case is initialised
     * @return void
     */
    public static function setUpBeforeClass() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/tiddlywiki/lib.php');
    }

    /**
     * Test tiddlywiki_view
     * @return void
     */
    public function test_tiddlywiki_view() {
        global $CFG;

        $CFG->enablecompletion = 1;
        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $tiddlywiki = $this->getDataGenerator()->create_module('tiddlywiki', array('course' => $course->id),
                                                            array('completion' => 2, 'completionview' => 1));
        $context = context_module::instance($tiddlywiki->cmid);
        $cm = get_coursemodule_from_instance('tiddlywiki', $tiddlywiki->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        tiddlywiki_view($tiddlywiki, $course, $cm, $context);

        $events = $sink->get_events();
        // 2 additional events thanks to completion.
        $this->assertCount(3, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_tiddlywiki\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/tiddlywiki/view.php', array('id' => $cm->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Check completion status.
        $completion = new completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);

    }

    /**
     * Tests the tiddlywiki_get_coursemodule_info function.
     *
     * Note: This currently doesn't test every aspect of the function, mainly focusing on the icon.
     */
    public function test_get_coursemodule_info() {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create course.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Create a tiddlywiki with no files.
        $draftid = file_get_unused_draft_itemid();
        $tiddlywiki1 = $generator->create_module('tiddlywiki', array('course' => $course->id,
                'name' => 'R1', 'files' => $draftid));

        // Create a tiddlywiki with one file.
        $draftid = file_get_unused_draft_itemid();
        $contextid = context_user::instance($USER->id)->id;
        $filerecord = array('component' => 'user', 'filearea' => 'draft', 'contextid' => $contextid,
                'itemid' => $draftid, 'filename' => 'r2.txt', 'filepath' => '/');
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecord, 'Test');
        $tiddlywiki2 = $generator->create_module('tiddlywiki', array('course' => $course->id,
                'name' => 'R2', 'files' => $draftid));

        // Create a tiddlywiki with two files.
        $draftid = file_get_unused_draft_itemid();
        $filerecord = array('component' => 'user', 'filearea' => 'draft', 'contextid' => $contextid,
                'itemid' => $draftid, 'filename' => 'r3.txt', 'filepath' => '/', 'sortorder' => 1);
        $fs->create_file_from_string($filerecord, 'Test');
        $filerecord['filename'] = 'r3.doc';
        $filerecord['sortorder'] = 2;
        $fs->create_file_from_string($filerecord, 'Test');
        $tiddlywiki3 = $generator->create_module('tiddlywiki', array('course' => $course->id,
                'name' => 'R3', 'files' => $draftid));

        // Try get_coursemodule_info for first one.
        $info = tiddlywiki_get_coursemodule_info(
                $DB->get_record('course_modules', array('id' => $tiddlywiki1->cmid)));

        // The name should be set. There is no overridden icon.
        $this->assertEquals('R1', $info->name);
        $this->assertEmpty($info->icon);

        // For second one, there should be an overridden icon.
        $info = tiddlywiki_get_coursemodule_info(
                $DB->get_record('course_modules', array('id' => $tiddlywiki2->cmid)));
        $this->assertEquals('R2', $info->name);
        $this->assertEquals('f/text-24', $info->icon);

        // For third one, it should use the highest sortorder icon.
        $info = tiddlywiki_get_coursemodule_info(
                $DB->get_record('course_modules', array('id' => $tiddlywiki3->cmid)));
        $this->assertEquals('R3', $info->name);
        $this->assertEquals('f/document-24', $info->icon);
    }

    public function test_tiddlywiki_core_calendar_provide_event_action() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $tiddlywiki = $this->getDataGenerator()->create_module('tiddlywiki', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $tiddlywiki->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_tiddlywiki_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_tiddlywiki_core_calendar_provide_event_action_already_completed() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $tiddlywiki = $this->getDataGenerator()->create_module('tiddlywiki', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('tiddlywiki', $tiddlywiki->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $tiddlywiki->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_tiddlywiki_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid The course id.
     * @param int $instanceid The instance id.
     * @param string $eventtype The event type.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'tiddlywiki';
        $event->courseid = $courseid;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        $event->timestart = time();

        return calendar_event::create($event);
    }
}
