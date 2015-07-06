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
 * This file contains the definition for the renderable classes for the assignment
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This class wraps the submit for grading confirmation page
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submit_for_grading_page implements renderable {
    /** @var array $notifications is a list of notification messages returned from the plugins */
    public $notifications = array();
    /** @var int $coursemoduleid */
    public $coursemoduleid = 0;
    /** @var moodleform $confirmform */
    public $confirmform = null;

    /**
     * Constructor
     * @param string $notifications - Any mesages to display
     * @param int $coursemoduleid
     * @param moodleform $confirmform
     */
    public function __construct($notifications, $coursemoduleid, $confirmform) {
        $this->notifications = $notifications;
        $this->coursemoduleid = $coursemoduleid;
        $this->confirmform = $confirmform;
    }

}

/**
 * Implements a renderable message notification
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_gradingmessage implements renderable {
    /** @var string $heading is the heading to display to the user */
    public $heading = '';
    /** @var string $message is the message to display to the user */
    public $message = '';
    /** @var int $coursemoduleid */
    public $coursemoduleid = 0;
    /** @var int $gradingerror should be set true if there was a problem grading */
    public $gradingerror = null;

    /**
     * Constructor
     * @param string $heading This is the heading to display
     * @param string $message This is the message to display
     * @param bool $gradingerror Set to true to display the message as an error.
     * @param int $coursemoduleid
     * @param int $page This is the current quick grading page
     */
    public function __construct($heading, $message, $coursemoduleid, $gradingerror = false, $page = null) {
        $this->heading = $heading;
        $this->message = $message;
        $this->coursemoduleid = $coursemoduleid;
        $this->gradingerror = $gradingerror;
        $this->page = $page;
    }

}

/**
 * Implements a renderable grading options form
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_form implements renderable {
    /** @var moodleform $form is the edit submission form */
    public $form = null;
    /** @var string $classname is the name of the class to assign to the container */
    public $classname = '';
    /** @var string $jsinitfunction is an optional js function to add to the page requires */
    public $jsinitfunction = '';

    /**
     * Constructor
     * @param string $classname This is the class name for the container div
     * @param moodleform $form This is the moodleform
     * @param string $jsinitfunction This is an optional js function to add to the page requires
     */
    public function __construct($classname, moodleform $form, $jsinitfunction = '') {
        $this->classname = $classname;
        $this->form = $form;
        $this->jsinitfunction = $jsinitfunction;
    }

}

/**
 * Implements a renderable user summary
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_user_summary implements renderable {
    /** @var stdClass $user suitable for rendering with user_picture and fullname(). */
    public $user = null;
    /** @var int $courseid */
    public $courseid;
    /** @var bool $viewfullnames */
    public $viewfullnames = false;
    /** @var bool $blindmarking */
    public $blindmarking = false;
    /** @var int $uniqueidforuser */
    public $uniqueidforuser;
    /** @var array $extrauserfields */
    public $extrauserfields;
    /** @var bool $suspendeduser */
    public $suspendeduser;

    /**
     * Constructor
     * @param stdClass $user
     * @param int $courseid
     * @param bool $viewfullnames
     * @param bool $blindmarking
     * @param int $uniqueidforuser
     * @param array $extrauserfields
     * @param bool $suspendeduser
     */
    public function __construct(stdClass $user,
                                $courseid,
                                $viewfullnames,
                                $blindmarking,
                                $uniqueidforuser,
                                $extrauserfields,
                                $suspendeduser = false) {
        $this->user = $user;
        $this->courseid = $courseid;
        $this->viewfullnames = $viewfullnames;
        $this->blindmarking = $blindmarking;
        $this->uniqueidforuser = $uniqueidforuser;
        $this->extrauserfields = $extrauserfields;
        $this->suspendeduser = $suspendeduser;
    }
}

/**
 * Implements a renderable feedback plugin feedback
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_feedback_plugin_feedback implements renderable {
    /** @var int SUMMARY */
    const SUMMARY                = 10;
    /** @var int FULL */
    const FULL                   = 20;

    /** @var assign_submission_plugin $plugin */
    public $plugin = null;
    /** @var stdClass $grade */
    public $grade = null;
    /** @var string $view */
    public $view = self::SUMMARY;
    /** @var int $coursemoduleid */
    public $coursemoduleid = 0;
    /** @var string returnaction The action to take you back to the current page */
    public $returnaction = '';
    /** @var array returnparams The params to take you back to the current page */
    public $returnparams = array();

    /**
     * Feedback for a single plugin
     *
     * @param assign_feedback_plugin $plugin
     * @param stdClass $grade
     * @param string $view one of feedback_plugin::SUMMARY or feedback_plugin::FULL
     * @param int $coursemoduleid
     * @param string $returnaction The action required to return to this page
     * @param array $returnparams The params required to return to this page
     */
    public function __construct(assign_feedback_plugin $plugin,
                                stdClass $grade,
                                $view,
                                $coursemoduleid,
                                $returnaction,
                                $returnparams) {
        $this->plugin = $plugin;
        $this->grade = $grade;
        $this->view = $view;
        $this->coursemoduleid = $coursemoduleid;
        $this->returnaction = $returnaction;
        $this->returnparams = $returnparams;
    }

}

/**
 * Implements a renderable submission plugin submission
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_plugin_submission implements renderable {
    /** @var int SUMMARY */
    const SUMMARY                = 10;
    /** @var int FULL */
    const FULL                   = 20;

    /** @var assign_submission_plugin $plugin */
    public $plugin = null;
    /** @var stdClass $submission */
    public $submission = null;
    /** @var string $view */
    public $view = self::SUMMARY;
    /** @var int $coursemoduleid */
    public $coursemoduleid = 0;
    /** @var string returnaction The action to take you back to the current page */
    public $returnaction = '';
    /** @var array returnparams The params to take you back to the current page */
    public $returnparams = array();

    /**
     * Constructor
     * @param assign_submission_plugin $plugin
     * @param stdClass $submission
     * @param string $view one of submission_plugin::SUMMARY, submission_plugin::FULL
     * @param int $coursemoduleid - the course module id
     * @param string $returnaction The action to return to the current page
     * @param array $returnparams The params to return to the current page
     */
    public function __construct(assign_submission_plugin $plugin,
                                stdClass $submission,
                                $view,
                                $coursemoduleid,
                                $returnaction,
                                $returnparams) {
        $this->plugin = $plugin;
        $this->submission = $submission;
        $this->view = $view;
        $this->coursemoduleid = $coursemoduleid;
        $this->returnaction = $returnaction;
        $this->returnparams = $returnparams;
    }
}

/**
 * Renderable feedback status
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_feedback_status implements renderable {

    /** @var stding $gradefordisplay the student grade rendered into a format suitable for display */
    public $gradefordisplay = '';
    /** @var mixed the graded date (may be null) */
    public $gradeddate = 0;
    /** @var mixed the grader (may be null) */
    public $grader = null;
    /** @var array feedbackplugins - array of feedback plugins */
    public $feedbackplugins = array();
    /** @var stdClass assign_grade record */
    public $grade = null;
    /** @var int coursemoduleid */
    public $coursemoduleid = 0;
    /** @var string returnaction */
    public $returnaction = '';
    /** @var array returnparams */
    public $returnparams = array();

    /**
     * Constructor
     * @param string $gradefordisplay
     * @param mixed $gradeddate
     * @param mixed $grader
     * @param array $feedbackplugins
     * @param mixed $grade
     * @param int $coursemoduleid
     * @param string $returnaction The action required to return to this page
     * @param array $returnparams The list of params required to return to this page
     */
    public function __construct($gradefordisplay,
                                $gradeddate,
                                $grader,
                                $feedbackplugins,
                                $grade,
                                $coursemoduleid,
                                $returnaction,
                                $returnparams) {
        $this->gradefordisplay = $gradefordisplay;
        $this->gradeddate = $gradeddate;
        $this->grader = $grader;
        $this->feedbackplugins = $feedbackplugins;
        $this->grade = $grade;
        $this->coursemoduleid = $coursemoduleid;
        $this->returnaction = $returnaction;
        $this->returnparams = $returnparams;
    }
}

/**
 * Renderable submission status
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_status implements renderable {
    /** @var int STUDENT_VIEW */
    const STUDENT_VIEW     = 10;
    /** @var int GRADER_VIEW */
    const GRADER_VIEW      = 20;

    /** @var int allowsubmissionsfromdate */
    public $allowsubmissionsfromdate = 0;
    /** @var bool alwaysshowdescription */
    public $alwaysshowdescription = false;
    /** @var stdClass the submission info (may be null) */
    public $submission = null;
    /** @var boolean teamsubmissionenabled - true or false */
    public $teamsubmissionenabled = false;
    /** @var stdClass teamsubmission the team submission info (may be null) */
    public $teamsubmission = null;
    /** @var stdClass submissiongroup the submission group info (may be null) */
    public $submissiongroup = null;
    /** @var array submissiongroupmemberswhoneedtosubmit list of users who still need to submit */
    public $submissiongroupmemberswhoneedtosubmit = array();
    /** @var bool submissionsenabled */
    public $submissionsenabled = false;
    /** @var bool locked */
    public $locked = false;
    /** @var bool graded */
    public $graded = false;
    /** @var int duedate */
    public $duedate = 0;
    /** @var int cutoffdate */
    public $cutoffdate = 0;
    /** @var array submissionplugins - the list of submission plugins */
    public $submissionplugins = array();
    /** @var string returnaction */
    public $returnaction = '';
    /** @var string returnparams */
    public $returnparams = array();
    /** @var int courseid */
    public $courseid = 0;
    /** @var int coursemoduleid */
    public $coursemoduleid = 0;
    /** @var int the view (STUDENT_VIEW OR GRADER_VIEW) */
    public $view = self::STUDENT_VIEW;
    /** @var bool canviewfullnames */
    public $canviewfullnames = false;
    /** @var bool canedit */
    public $canedit = false;
    /** @var bool cansubmit */
    public $cansubmit = false;
    /** @var int extensionduedate */
    public $extensionduedate = 0;
    /** @var context context */
    public $context = 0;
    /** @var bool blindmarking - Should we hide student identities from graders? */
    public $blindmarking = false;
    /** @var string gradingcontrollerpreview */
    public $gradingcontrollerpreview = '';
    /** @var string attemptreopenmethod */
    public $attemptreopenmethod = 'none';
    /** @var int maxattempts */
    public $maxattempts = -1;
    /** @var string gradingstatus */
    public $gradingstatus = '';
    /** @var bool preventsubmissionnotingroup */
    public $preventsubmissionnotingroup = 0;


    /**
     * Constructor
     *
     * @param int $allowsubmissionsfromdate
     * @param bool $alwaysshowdescription
     * @param stdClass $submission
     * @param bool $teamsubmissionenabled
     * @param stdClass $teamsubmission
     * @param int $submissiongroup
     * @param array $submissiongroupmemberswhoneedtosubmit
     * @param bool $submissionsenabled
     * @param bool $locked
     * @param bool $graded
     * @param int $duedate
     * @param int $cutoffdate
     * @param array $submissionplugins
     * @param string $returnaction
     * @param array $returnparams
     * @param int $coursemoduleid
     * @param int $courseid
     * @param string $view
     * @param bool $canedit
     * @param bool $cansubmit
     * @param bool $canviewfullnames
     * @param int $extensionduedate - Any extension to the due date granted for this user
     * @param context $context - Any extension to the due date granted for this user
     * @param bool $blindmarking - Should we hide student identities from graders?
     * @param string $gradingcontrollerpreview
     * @param string $attemptreopenmethod - The method of reopening student attempts.
     * @param int $maxattempts - How many attempts can a student make?
     * @param string $gradingstatus - The submission status (ie. Graded, Not Released etc).
     * @param bool $preventsubmissionnotingroup - Prevent submission if user is not in a group
     */
    public function __construct($allowsubmissionsfromdate,
                                $alwaysshowdescription,
                                $submission,
                                $teamsubmissionenabled,
                                $teamsubmission,
                                $submissiongroup,
                                $submissiongroupmemberswhoneedtosubmit,
                                $submissionsenabled,
                                $locked,
                                $graded,
                                $duedate,
                                $cutoffdate,
                                $submissionplugins,
                                $returnaction,
                                $returnparams,
                                $coursemoduleid,
                                $courseid,
                                $view,
                                $canedit,
                                $cansubmit,
                                $canviewfullnames,
                                $extensionduedate,
                                $context,
                                $blindmarking,
                                $gradingcontrollerpreview,
                                $attemptreopenmethod,
                                $maxattempts,
                                $gradingstatus,
                                $preventsubmissionnotingroup) {
        $this->allowsubmissionsfromdate = $allowsubmissionsfromdate;
        $this->alwaysshowdescription = $alwaysshowdescription;
        $this->submission = $submission;
        $this->teamsubmissionenabled = $teamsubmissionenabled;
        $this->teamsubmission = $teamsubmission;
        $this->submissiongroup = $submissiongroup;
        $this->submissiongroupmemberswhoneedtosubmit = $submissiongroupmemberswhoneedtosubmit;
        $this->submissionsenabled = $submissionsenabled;
        $this->locked = $locked;
        $this->graded = $graded;
        $this->duedate = $duedate;
        $this->cutoffdate = $cutoffdate;
        $this->submissionplugins = $submissionplugins;
        $this->returnaction = $returnaction;
        $this->returnparams = $returnparams;
        $this->coursemoduleid = $coursemoduleid;
        $this->courseid = $courseid;
        $this->view = $view;
        $this->canedit = $canedit;
        $this->cansubmit = $cansubmit;
        $this->canviewfullnames = $canviewfullnames;
        $this->extensionduedate = $extensionduedate;
        $this->context = $context;
        $this->blindmarking = $blindmarking;
        $this->gradingcontrollerpreview = $gradingcontrollerpreview;
        $this->attemptreopenmethod = $attemptreopenmethod;
        $this->maxattempts = $maxattempts;
        $this->gradingstatus = $gradingstatus;
        $this->preventsubmissionnotingroup = $preventsubmissionnotingroup;
    }
}

/**
 * Used to output the attempt history for a particular assignment.
 *
 * @package mod_assign
 * @copyright 2012 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_attempt_history implements renderable {

    /** @var array submissions - The list of previous attempts */
    public $submissions = array();
    /** @var array grades - The grades for the previous attempts */
    public $grades = array();
    /** @var array submissionplugins - The list of submission plugins to render the previous attempts */
    public $submissionplugins = array();
    /** @var array feedbackplugins - The list of feedback plugins to render the previous attempts */
    public $feedbackplugins = array();
    /** @var int coursemoduleid - The cmid for the assignment */
    public $coursemoduleid = 0;
    /** @var string returnaction - The action for the next page. */
    public $returnaction = '';
    /** @var string returnparams - The params for the next page. */
    public $returnparams = array();
    /** @var bool cangrade - Does this user have grade capability? */
    public $cangrade = false;
    /** @var string useridlistid - Id of the useridlist stored in cache, this plus rownum determines the userid */
    public $useridlistid = 0;
    /** @var int rownum - The rownum of the user in the useridlistid - this plus useridlistid determines the userid */
    public $rownum = 0;

    /**
     * Constructor
     *
     * @param array $submissions
     * @param array $grades
     * @param array $submissionplugins
     * @param array $feedbackplugins
     * @param int $coursemoduleid
     * @param string $returnaction
     * @param array $returnparams
     * @param bool $cangrade
     * @param int $useridlistid
     * @param int $rownum
     */
    public function __construct($submissions,
                                $grades,
                                $submissionplugins,
                                $feedbackplugins,
                                $coursemoduleid,
                                $returnaction,
                                $returnparams,
                                $cangrade,
                                $useridlistid,
                                $rownum) {
        $this->submissions = $submissions;
        $this->grades = $grades;
        $this->submissionplugins = $submissionplugins;
        $this->feedbackplugins = $feedbackplugins;
        $this->coursemoduleid = $coursemoduleid;
        $this->returnaction = $returnaction;
        $this->returnparams = $returnparams;
        $this->cangrade = $cangrade;
        $this->useridlistid = $useridlistid;
        $this->rownum = $rownum;
    }
}

/**
 * Renderable header
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_header implements renderable {
    /** @var stdClass the assign record  */
    public $assign = null;
    /** @var mixed context|null the context record  */
    public $context = null;
    /** @var bool $showintro - show or hide the intro */
    public $showintro = false;
    /** @var int coursemoduleid - The course module id */
    public $coursemoduleid = 0;
    /** @var string $subpage optional subpage (extra level in the breadcrumbs) */
    public $subpage = '';
    /** @var string $preface optional preface (text to show before the heading) */
    public $preface = '';
    /** @var string $postfix optional postfix (text to show after the intro) */
    public $postfix = '';

    /**
     * Constructor
     *
     * @param stdClass $assign  - the assign database record
     * @param mixed $context context|null the course module context
     * @param bool $showintro  - show or hide the intro
     * @param int $coursemoduleid  - the course module id
     * @param string $subpage  - an optional sub page in the navigation
     * @param string $preface  - an optional preface to show before the heading
     */
    public function __construct(stdClass $assign,
                                $context,
                                $showintro,
                                $coursemoduleid,
                                $subpage='',
                                $preface='',
                                $postfix='') {
        $this->assign = $assign;
        $this->context = $context;
        $this->showintro = $showintro;
        $this->coursemoduleid = $coursemoduleid;
        $this->subpage = $subpage;
        $this->preface = $preface;
        $this->postfix = $postfix;
    }
}

/**
 * Renderable header related to an individual subplugin
 * @package   mod_assign
 * @copyright 2014 Henning Bostelmann
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_plugin_header implements renderable {
    /** @var assign_plugin $plugin */
    public $plugin = null;

    /**
     * Header for a single plugin
     *
     * @param assign_plugin $plugin
     */
    public function __construct(assign_plugin $plugin) {
        $this->plugin = $plugin;
    }
}

/**
 * Renderable grading summary
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_grading_summary implements renderable {
    /** @var int participantcount - The number of users who can submit to this assignment */
    public $participantcount = 0;
    /** @var bool submissiondraftsenabled - Allow submission drafts */
    public $submissiondraftsenabled = false;
    /** @var int submissiondraftscount - The number of submissions in draft status */
    public $submissiondraftscount = 0;
    /** @var bool submissionsenabled - Allow submissions */
    public $submissionsenabled = false;
    /** @var int submissionssubmittedcount - The number of submissions in submitted status */
    public $submissionssubmittedcount = 0;
    /** @var int submissionsneedgradingcount - The number of submissions that need grading */
    public $submissionsneedgradingcount = 0;
    /** @var int duedate - The assignment due date (if one is set) */
    public $duedate = 0;
    /** @var int cutoffdate - The assignment cut off date (if one is set) */
    public $cutoffdate = 0;
    /** @var int coursemoduleid - The assignment course module id */
    public $coursemoduleid = 0;
    /** @var boolean teamsubmission - Are team submissions enabled for this assignment */
    public $teamsubmission = false;
    /** @var boolean warnofungroupedusers - Do we need to warn people that there are users without groups */
    public $warnofungroupedusers = false;

    /**
     * constructor
     *
     * @param int $participantcount
     * @param bool $submissiondraftsenabled
     * @param int $submissiondraftscount
     * @param bool $submissionsenabled
     * @param int $submissionssubmittedcount
     * @param int $cutoffdate
     * @param int $duedate
     * @param int $coursemoduleid
     * @param int $submissionsneedgradingcount
     * @param bool $teamsubmission
     */
    public function __construct($participantcount,
                                $submissiondraftsenabled,
                                $submissiondraftscount,
                                $submissionsenabled,
                                $submissionssubmittedcount,
                                $cutoffdate,
                                $duedate,
                                $coursemoduleid,
                                $submissionsneedgradingcount,
                                $teamsubmission,
                                $warnofungroupedusers) {
        $this->participantcount = $participantcount;
        $this->submissiondraftsenabled = $submissiondraftsenabled;
        $this->submissiondraftscount = $submissiondraftscount;
        $this->submissionsenabled = $submissionsenabled;
        $this->submissionssubmittedcount = $submissionssubmittedcount;
        $this->duedate = $duedate;
        $this->cutoffdate = $cutoffdate;
        $this->coursemoduleid = $coursemoduleid;
        $this->submissionsneedgradingcount = $submissionsneedgradingcount;
        $this->teamsubmission = $teamsubmission;
        $this->warnofungroupedusers = $warnofungroupedusers;
    }
}

/**
 * Renderable course index summary
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_course_index_summary implements renderable {
    /** @var array assignments - A list of course module info and submission counts or statuses */
    public $assignments = array();
    /** @var boolean usesections - Does this course format support sections? */
    public $usesections = false;
    /** @var string courseformat - The current course format name */
    public $courseformatname = '';

    /**
     * constructor
     *
     * @param boolean $usesections - True if this course format uses sections
     * @param string $courseformatname - The id of this course format
     */
    public function __construct($usesections, $courseformatname) {
        $this->usesections = $usesections;
        $this->courseformatname = $courseformatname;
    }

    /**
     * Add a row of data to display on the course index page
     *
     * @param int $cmid - The course module id for generating a link
     * @param string $cmname - The course module name for generating a link
     * @param string $sectionname - The name of the course section (only if $usesections is true)
     * @param int $timedue - The due date for the assignment - may be 0 if no duedate
     * @param string $submissioninfo - A string with either the number of submitted assignments, or the
     *                                 status of the current users submission depending on capabilities.
     * @param string $gradeinfo - The current users grade if they have been graded and it is not hidden.
     */
    public function add_assign_info($cmid, $cmname, $sectionname, $timedue, $submissioninfo, $gradeinfo) {
        $this->assignments[] = array('cmid'=>$cmid,
                               'cmname'=>$cmname,
                               'sectionname'=>$sectionname,
                               'timedue'=>$timedue,
                               'submissioninfo'=>$submissioninfo,
                               'gradeinfo'=>$gradeinfo);
    }


}


/**
 * An assign file class that extends rendererable class and is used by the assign module.
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_files implements renderable {
    /** @var context $context */
    public $context;
    /** @var string $context */
    public $dir;
    /** @var MoodleQuickForm $portfolioform */
    public $portfolioform;
    /** @var stdClass $cm course module */
    public $cm;
    /** @var stdClass $course */
    public $course;

    /**
     * The constructor
     *
     * @param context $context
     * @param int $sid
     * @param string $filearea
     * @param string $component
     */
    public function __construct(context $context, $sid, $filearea, $component) {
        global $CFG;
        $this->context = $context;
        list($context, $course, $cm) = get_context_info_array($context->id);
        $this->cm = $cm;
        $this->course = $course;
        $fs = get_file_storage();
        $this->dir = $fs->get_area_tree($this->context->id, $component, $filearea, $sid);

        $files = $fs->get_area_files($this->context->id,
                                     $component,
                                     $filearea,
                                     $sid,
                                     'timemodified',
                                     false);

        if (!empty($CFG->enableportfolios)) {
            require_once($CFG->libdir . '/portfoliolib.php');
            if (count($files) >= 1 &&
                    has_capability('mod/assign:exportownsubmission', $this->context)) {
                $button = new portfolio_add_button();
                $callbackparams = array('cmid' => $this->cm->id,
                                        'sid' => $sid,
                                        'area' => $filearea,
                                        'component' => $component);
                $button->set_callback_options('assign_portfolio_caller',
                                              $callbackparams,
                                              'mod_assign');
                $button->reset_formats();
                $this->portfolioform = $button->to_html(PORTFOLIO_ADD_TEXT_LINK);
            }

        }

        $this->preprocess($this->dir, $filearea, $component);
    }

    /**
     * Preprocessing the file list to add the portfolio links if required.
     *
     * @param array $dir
     * @param string $filearea
     * @param string $component
     * @return void
     */
    public function preprocess($dir, $filearea, $component) {
        global $CFG;
        foreach ($dir['subdirs'] as $subdir) {
            $this->preprocess($subdir, $filearea, $component);
        }
        foreach ($dir['files'] as $file) {
            $file->portfoliobutton = '';
            if (!empty($CFG->enableportfolios)) {
                $button = new portfolio_add_button();
                if (has_capability('mod/assign:exportownsubmission', $this->context)) {
                    $portfolioparams = array('cmid' => $this->cm->id, 'fileid' => $file->get_id());
                    $button->set_callback_options('assign_portfolio_caller',
                                                  $portfolioparams,
                                                  'mod_assign');
                    $button->set_format_by_file($file);
                    $file->portfoliobutton = $button->to_html(PORTFOLIO_ADD_ICON_LINK);
                }
            }
            $path = '/' .
                    $this->context->id .
                    '/' .
                    $component .
                    '/' .
                    $filearea .
                    '/' .
                    $file->get_itemid() .
                    $file->get_filepath() .
                    $file->get_filename();
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php", $path, true);
            $filename = $file->get_filename();
            $file->fileurl = html_writer::link($url, $filename);
        }
    }

}
/**
 * Renderable assignment student summary page
 * @package   mod_assign
 * @copyright 2015 Ben Kelada (ben.kelada@gmail.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_page implements renderable, templatable {
    public $assignviewobj;
    public $studentsummaryonly;

    public function __construct(\mod_assign_view_object $assignviewobj, $studentsummaryonly = false) {
        $this->assignviewobj = $assignviewobj;
        $this->studentsummaryonly = $studentsummaryonly;
    }

    public function export_for_template(\renderer_base $output) {

        $viewobj = $this->assignviewobj;
        $data = new \stdClass();

        if (!$this->studentsummaryonly) {
            $postfix = '';
            if ($viewobj->has_visible_attachments) {
                $postfix = $viewobj->area_files_rendered;
            }
            $header = new assign_header($viewobj->instance,
                $viewobj->context,
                $viewobj->show_intro,
                $viewobj->course_module_id,
                '', '', $postfix);

            $data->renderables['header'] = $header;

            $data->plugin_headers = '';
            $plugins = array_merge($viewobj->submission_plugins, $viewobj->feedback_plugins);
            foreach ($plugins as $plugin) {
                if ($plugin->is_enabled() && $plugin->is_visible()) {
                    $data->renderables[$plugin->get_type()] = new assign_plugin_header($plugin);
                }
            }

            if ($viewobj->can_view_grades) {

                $data->group_selector_activity_menu = groups_print_activity_menu($viewobj->course_module,
                                                                                 $viewobj->current_url->out(), true);

                $gradingsummary = new assign_grading_summary($viewobj->participant_count,
                    $viewobj->submission_drafts_enabled,
                    $viewobj->submission_drafts_count,
                    $viewobj->is_any_submission_plugin_enabled,
                    $viewobj->submissions_submitted_count,
                    $viewobj->cutoffdate,
                    $viewobj->duedate,
                    $viewobj->course_module_id,
                    $viewobj->count_submissions_need_grading,
                    $viewobj->is_team_submission,
                    $viewobj->warn_of_ungrouped_users);

                $data->renderables['grading_summary'] = $gradingsummary;
            }
        }
        if ($viewobj->can_view_submission) {
            $submissionstatus = new assign_submission_status($viewobj->instance->allowsubmissionsfromdate,
                $viewobj->instance->alwaysshowdescription,
                $viewobj->submission,
                $viewobj->is_team_submission,
                $viewobj->group_submission,
                $viewobj->submission_group,
                $viewobj->group_members_not_submitted,
                $viewobj->is_any_submission_plugin_enabled,
                $viewobj->grade_locked,
                $viewobj->is_graded,
                $viewobj->instance->duedate,
                $viewobj->instance->cutoffdate,
                $viewobj->submission_plugins,
                $viewobj->return_action,
                $viewobj->return_params,
                $viewobj->course_module_id,
                $viewobj->course_id,
                $viewobj->view_type,
                $viewobj->show_edit_button,
                $viewobj->show_submit_button,
                $viewobj->view_full_names,
                $viewobj->extension_due_date,
                $viewobj->context,
                $viewobj->is_blind_marking,
                $viewobj->grading_controller_preview,
                $viewobj->instance->attemptreopenmethod,
                $viewobj->instance->maxattempts,
                $viewobj->grading_status,
                $viewobj->instance->preventsubmissionnotingroup);

            $data->renderables['submission_status'] = $submissionstatus;
            if ($viewobj->visible_grade) {
                $feedbackstatus = new assign_feedback_status($viewobj->grade_for_display,
                    $viewobj->graded_date,
                    $viewobj->grader,
                    $viewobj->feedback_plugins,
                    $viewobj->grade,
                    $viewobj->course_module_id,
                    $viewobj->return_action,
                    $viewobj->return_params);

                $data->renderables['feedback_status'] = $feedbackstatus;

                if (count($viewobj->all_submissions > 1)) {
                    $history = new assign_attempt_history($viewobj->all_submissions,
                        $viewobj->all_grades,
                        $viewobj->submission_plugins,
                        $viewobj->feedback_plugins,
                        $viewobj->course_module_id,
                        $viewobj->return_action,
                        $viewobj->return_params,
                        false,
                        0,
                        0);

                    $data->renderables['history'] = $history;
                }
            }
        }

        return $data;
    }
}

/**
 * Class that holds view information to build assignment page
 *
 */
class mod_assign_view_object {
    /** @var core_user $user Contains user object, this is the user whose assignment we are viewing. */
    public $user;
    /** @var stdClass $instance The assignment instance we are viewing. */
    public $instance;
    /** @var bool $show_links Should we show submit/edit links, not shown on report/user page. */
    public $show_links;
    /** @var bool Are there any intro attachments to display? */
    public $has_visible_attachments;
    /** @var assign_files Renderered files for this mod_assign intro area */
    public $area_files_rendered;
    /** @var  context $context Context module */
    public $context;
    /** @var  bool $show_intro Assignment configuration show assignment intro text? */
    public $show_intro;
    /** @var  stdClass $course_module Course module. */
    public $course_module;
    /** @var  int $course_module_id Course module id. */
    public $course_module_id;
    /** @var  int $course_id Course id. */
    public $course_id;
    /** @var  array $feedback_plugins List of feedback plugins installed. */
    public $feedback_plugins;
    /** @var  array $submission_plugins List of submisison plugins installed */
    public $submission_plugins;
    /** @var  bool $can_view_grades Can user view grades? */
    public $can_view_grades;
    /** @var  bool $can_view_submission Can user view this submission? */
    public $can_view_submission;
    /** @var  bool $is_team_submission Is this assignment a group/team submisison? */
    public $is_team_submission;
    /** @var  stdClass $group_submission The groups submission */
    public $group_submission;
    /** @var  mixed $submission_group The group of the user or false */
    public $submission_group;
    /** @var  array $group_members_not_submitted List of group members who have not submitted */
    public $group_members_not_submitted;
    /** @var  bool $is_any_submission_plugin_enabled Check of all submission plugins true if any are enabled */
    public $is_any_submission_plugin_enabled;
    /** @var  stdClass $grade Grade record for this users assignment */
    public $grade;
    /** @var  stdClass $submission submission of the user */
    public $submission;
    /** @var  int $groupid id of users group */
    public $groupid;
    /** @var  bool $warn_of_ungrouped_users Warn that submission is restricted to groups and user is not in a group */
    public $warn_of_ungrouped_users;
    /** @var  int $participant_count Count of groups or users */
    public $participant_count;
    /** @var bool $submission_drafts_enabled INSTANCE: Are submission drafts enabled for this instance */
    public $submission_drafts_enabled;
    /** @var  int $submission_drafts_count ?? How many drafts have been submitted */
    public $submission_drafts_count;
    /** @var  int $submissions_submitted_count How many submissions have been submitted */
    public $submissions_submitted_count;
    /** @var  core_date $cutoffdate INSTANCE: cut off date for this assignment instance */
    public $cutoffdate;
    /** @var  core_date $duedate INSTANCE: due date for this assignment instance */
    public $duedate;
    /** @var  int $count_submissions_need_grading INSTANCE: number of submissions in the current instance that need grading */
    public $count_submissions_need_grading;
    /** @var  stdClass $flags user flags object */
    public $flags;
    /** @var  bool $can_edit_submission Can this grader edit this submission do they have the capability? */
    public $can_edit_submission;
    /** @var  bool $grading_disabled Can this users grade be edited? */
    public $grading_disabled;
    /** @var  bool $submissions_open Open for submissions? check of duedate, late, already submitted, locked */
    public $submissions_open;
    /** @var  bool $show_edit_button Should we show the edit button? */
    public $show_edit_button;
    /** @var  bool $show_submit_button Should we show the submit button? */
    public $show_submit_button;
    /** @var  bool $grade_locked Is user flagged as locked or grading disabled? */
    public $grade_locked;
    /** @var  mixed $extension_due_date does user have an extension due date or null */
    public $extension_due_date;
    /** @var  bool $view_full_names does user have capability to view full names for this course context */
    public $view_full_names;
    /** @var  string $grading_status Grading status */
    public $grading_status;
    /** @var  bool $user_has_submit_capability Does the user have the submit capability */
    public $user_has_submit_capability;
    /** @var  bool $is_graded has this assignment been graded? */
    public $is_graded;
    /** @var  string $return_action action to get back to current page */
    public $return_action;
    /** @var  string $return_params params for return action */
    public $return_params;
    /** @var int $view_type hardcoded to submission_status::student_view currently */
    public $view_type;
    /** @var  bool $visible_grade is grade visible */
    public $visible_grade;
    /** @var  bool $is_blind_marking is blind marking enabled */
    public $is_blind_marking;
    /** @var  string $grading_controller_preview rendered preview of grading controller. */
    public $grading_controller_preview;
    /** @var  array $grading_info various  grading information */
    public $grading_info;
    /** @var  grade_item $grading_item Grading item */
    public $grading_item;
    /** @var  grade $grade_book_grade gradebook grade */
    public $grade_book_grade;
    /** @var  bool $can_grade Does user have grade capability */
    public $can_grade;
    /** @var  string $grade_for_display Grade for display */
    public $grade_for_display;
    /** @var  string $graded_date Date item was graded */
    public $graded_date;
    /** @var  stdClass $grader user object of person grading (grader) */
    public $grader;
    /** @var  moodle_url $current_url current url */
    public $current_url;
    /** @var  array $all_submissions All the submissions from this user for this assignment */
    public $all_submissions;
    /** @var  array $all_grades All the grades from submissions for this user for this assignment */
    public $all_grades;
}
