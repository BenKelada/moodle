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
 * Defines profile view manager class
 *
 * @since     Moodle 3.0
 * @package   core_user
 * @copyright 2015 onwards Ben Kelada (ben.kelada@open.edu.au
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user;
defined('MOODLE_INTERNAL') || die();

/**
 * Defines profile view manager class
 *
 * @since     Moodle 3.0
 * @package   core_user
 * @copyright 2015 onwards Ben Kelada (ben.kelada@open.edu.au
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profileviewmanager {
    /**
     * Check if a user has the permission to viewdetails in a shared course's context.
     *
     * @param object  $user The other user's details.
     * @param object  $course Use this course to see if we have permission to see this user's profile.
     * @param context $usercontext The user context if available.
     *
     * @return bool true for ability to view this user, else false.
     */
    public static function user_can_view_profile($user, $course = null, $usercontext = null) {
        global $USER, $CFG;

        if ($user->deleted) {
            return false;
        }

        // If any of these four things, return true.
        // Number 1. Profile is of current user.
        if ($USER->id == $user->id) {
            return true;
        }

        // Number 2. Force login for profiles is disabled.
        if (empty($CFG->forceloginforprofiles)) {
            return true;
        }

        if (empty($usercontext)) {
            $usercontext = \context_user::instance($user->id);
        }
        // Number 3. User has capability for the user profile context.
        if (has_capability('moodle/user:viewdetails', $usercontext)) {
            return true;
        }

        // Number 4. Profile is of a coursecontact.
        if (has_coursecontact_role($user->id)) {
            return true;
        }

        if (isset($course)) {
            $sharedcourses = array($course);
        } else {
            $sharedcourses = enrol_get_shared_courses($USER->id, $user->id, true);
        }
        foreach ($sharedcourses as $sharedcourse) {
            $coursecontext = \context_course::instance($sharedcourse->id);
            if (has_capability('moodle/user:viewdetails', $coursecontext)) {
                if (!groups_user_groups_visible($sharedcourse, $user->id)) {
                    // Not a member of the same group.
                    continue;
                }

                return true;
            }
        }

        return false;
    }

}
