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
 * Mod mediagallery external API
 *
 * @package    mod_mediagallery
 * @category   external
 * @copyright  2019 Bas Brands <bas@sonsbeekmedia.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');

/**
 * Mod mediagallery external functions.
 *
 * @copyright  2019 Bas Brands <bas@sonsbeekmedia.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_mediagallery_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.6
     */
    public static function like_parameters() {
        return new external_function_parameters([
            'itemid' => new external_value(PARAM_INT, 'item id', VALUE_DEFAULT, 0),
            'like' => new external_value(PARAM_BOOL, 'is this item liked ? (1 - default) otherwise (0)', VALUE_DEFAULT, 1)
        ]);
    }

    /**
     * Set liked item.
     *
     * @param int $itemid item id
     *
     * @return  array list of courses and warnings
     */
    public static function like($itemid, $like) {
        global $USER, $PAGE;

        $params = self::validate_parameters(self::like_parameters(), [
            'itemid' => $itemid
        ]);

        $itemid = $params['itemid'];

        $warnings = array();
        $usercontext = context_user::instance($USER->id);
        try {
            $item = new \mod_mediagallery\item($itemid);
            if ($like) {
                $item->like();
            } else {
                $item->unlike();
            }
        } catch (Exception $e) {
            $warning = array();
            $warning['item'] = 'mediagallery';
            $warning['itemid'] = $itemid;
            if ($e instanceof moodle_exception) {
                $warning['warningcode'] = $e->errorcode;
            } else {
                $warning['warningcode'] = $e->getCode();
            }
            $warning['message'] = $e->getMessage();
            $warnings[] = $warning;
        }

        $result = array();
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.6
     */
    public static function like_returns() {
        return new external_single_structure(
            array(
                'warnings' => new external_warnings()
            )
        );
    }
}
