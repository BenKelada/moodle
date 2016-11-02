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
 * Manual authentication tests.
 *
 * @package    auth_sharedcookie
 * @category   test
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/auth/ouasharedcookie/auth.php');

/**
 * OUAsharedcookie authentication tests class.
 *
 * @package    auth_sharedcookie
 * @category   test
 */
class auth_sharedcookie_testcase extends advanced_testcase {

    /** @var auth_plugin_manual Keeps the authentication plugin. */
    protected $authplugin;

    protected $olderrorhandler;

    /**
     * Setup test data configuration.
     */
    protected function setUp() {
        $this->resetAfterTest(true);

        // Setup all of the config before we start.
        set_config('secret', 'bilbo', 'auth/ouasharedcookie');

        set_config('shared_login_url', 'SHAREDLOGINURL', 'auth/ouasharedcookie');
        set_config('no_account_url', 'NOACCOUNTURL', 'auth/ouasharedcookie');
        set_config('logout_url', 'LOGOUTURL', 'auth/ouasharedcookie');
        set_config('change_password_url', 'PASSWORDURL', 'auth/ouasharedcookie');
        set_config('cookie_name', 'TESTCOOKIE', 'auth/ouasharedcookie');
        set_config('shared_cookie_domain', '.', 'auth/ouasharedcookie');
        set_config('dosinglelogout', true, 'auth/ouasharedcookie');
        set_config('iterations', 1000, 'auth/ouasharedcookie');
        set_config('timeout', 3600, 'auth/ouasharedcookie');

        $this->authplugin = new auth_plugin_ouasharedcookie();
    }

    /**
     * Reset the cookie to null after each test to ensure it's not available to the next test.
     */
    protected function tearDown() {
        // We set the cookie value each test, ensure it's reset.
        $_COOKIE['TESTCOOKIE'] = null;
        unset($_GET['redirect']);
    }

    /**
     * GIVEN we have an encrypted shared cookie
     * WHEN we visit a page that requires authentication
     *  AND the cookie is out of date.
     * THEN we are logged log a warning that the cookie is old.
     *
     * @test
     */
    public function test_production_cookie_too_old_to_verify_decrypt_algorithm_is_correct() {
        global $DB;

        $_COOKIE['TESTCOOKIE'] = 'dq2LxL6rQ/WGwlb5SUHYPOG+imELAnVgr59Q00lPPDY8WLqHP2JEDQ==';

        $admin = new \stdClass();
        $admin->id = 1;
        $admin->idnumber = 867554;
        $DB->update_record('user', $admin);

        $this->authplugin->pre_loginpage_hook();
        $this->assertDebuggingCalled('ouasharedcookie: Cookie is too old. Data: 867554:benkelada:1433479872166');
        $this->assertFalse(isloggedin(), 'User should not have been logged in.');
    }

    /**
     * GIVEN we have and id, name and time
     *  WHEN we encrypt and decrypt the cookie
     *  THEN the original data is returned to us
     * @dataProvider provider_encryption_data
     * @test
     */
    public function encrypt_and_decrypt_work_when_called_with_different_lengths($id, $name, $time) {
        $result = $this->authplugin->encrypt_cookie($id, $name, $time);
        $decryptedresult = $this->authplugin->decrypt_cookie($result);
        $this->assertEquals("$id:$name:$time", $decryptedresult);
    }

    /**
     * @test
     * GIVEN we encrypt a cookie and don't supply the time
     *  WHEN we decrypt the cookie
     *  THEN the time is the current time.
     */
    public function cookie_encrypts_with_current_time() {
        $time = time();
        $result = $this->authplugin->encrypt_cookie(57, 'test_user');
        $decryptedresult = $this->authplugin->decrypt_cookie($result);
        preg_match('/^\d+:.*:(\d+)$/', $decryptedresult, $match);

        $this->assertArrayHasKey(1, $match, 'The cookie must decrypt with a match');
        $this->assertGreaterThanOrEqual($time, $match[1]);
        $this->assertLessThan($time + 10, $match[1], 'Cookie must be within 10 seconds of the start of the function');
        $this->assertContains('57:test_user:', $decryptedresult);
    }
    /**
     * Only provides limited examples unless PHPUNIT_LONGTEST is set to complete an extended list.
     *
     * @return array Data list to ensure encryption and decryption works as expected.
     */
    public function provider_encryption_data() {
        $data = array();
        $data[] = array('867554','benkelada','1433479872166');

        // Under longer test conditions, run a lot of scenarios.
        if (defined('PHPUNIT_LONGTEST') && PHPUNIT_LONGTEST == true) {
            for ($i = 0; $i <= 8; $i += 4) {
                for ($j = 0; $j <= 8; $j += 2) {
                    for ($k = 0; $k <= 8; $k += 1) {
                        $data[] = array(str_repeat('I', $i), str_repeat('N', $j), str_repeat('T', $k));
                    }
                }
            }
        }
        return $data;
    }

    /**
     * GIVEN an encrypted cookie with a : in the username
     * WHEN we visit the login page
     * THEN we are logged in and redirected.
     * @test
     */
    public function loginpage_user_is_logged_in_when_cookie_is_present() {
        global $user;

        // We include a : in the username to ensure we are splitting it correctly on decryption.
        $_COOKIE['TESTCOOKIE'] = $this->authplugin->encrypt_cookie(867552, 'Adm:in', time()*1000);

        $loginuser = $this->getDataGenerator()->create_user(array('idnumber' => 867552, 'auth' => 'ouasharedcookie'));

        $this->authplugin->loginpage_hook();

        $this->assertEquals($loginuser->id, $user->id);
    }

    /**
     * GIVEN there is no cookie
     * WHEN we visit the login page
     * THEN we are not logged in and redirected.
     *
     * @test
     */
    public function loginpage_user_is_not_logged_in_when_cookie_is_not_present() {
        $this->authplugin->loginpage_hook();
        $this->assertFalse(isloggedin(), 'User should not have been logged in.');
    }

    /**
     * test_ must be in the name to ensure debugging is collected properly.
     *
     * GIVEN there is an invalid cookie
     *  WHEN we go to the login page
     *  THEN we are redirected to the other login page
     */
    public function test_loginpage_user_is_not_logged_in_when_cookie_is_garbage() {

        $_COOKIE['TESTCOOKIE'] = base64_encode('This is a garbage cookie!');

        $this->authplugin->loginpage_hook();

        $this->assertDebuggingCalled('ouasharedcookie: Cookie corrupt or missing data. Failed to match cookie data with format /^(\d+):(.*):(\d+)$/: ');
        $this->assertFalse(isloggedin(), 'User should not have been logged in.');
    }


    /**
     * @test
     *
     * GIVEN there is a valid cookie
     *  WHEN we visit a page that requires authentication.
     *  THEN we are logged in automatically.
     */
    public function user_is_logged_in_when_cookie_is_present() {
        global $USER;

        $_COOKIE['TESTCOOKIE'] = $this->authplugin->encrypt_cookie(867552, 'Admin', time()*1000);

        $user = $this->getDataGenerator()->create_user(array('idnumber' => 867552, 'auth' => 'ouasharedcookie'));

        $this->olderrorhandler = set_error_handler(array($this, 'handle_session_regeneration_warning'), E_WARNING);
        $this->authplugin->pre_loginpage_hook();
        restore_error_handler();

        $this->assertTrue(isloggedin(), 'User should have been logged in.');
        $this->assertEquals($user->id, $USER->id);
    }

    /**
     * @test
     */
    public function user_is_not_logged_in_when_cookie_is_not_present() {
        $this->authplugin->pre_loginpage_hook();
        $this->assertFalse(isloggedin(), 'User should not have been logged in.');
    }

    /**
     *
     */
    public function test_user_is_not_logged_in_when_cookie_is_garbage() {

        $_COOKIE['TESTCOOKIE'] = base64_encode('This is a garbage cookie!');

        $this->authplugin->pre_loginpage_hook();

        $this->assertDebuggingCalled('ouasharedcookie: Cookie corrupt or missing data. Failed to match cookie data with format /^(\d+):(.*):(\d+)$/: ');
        $this->assertFalse(isloggedin(), 'User should not have been logged in.');
    }

    public function test_basic_functions_of_the_module() {
        $this->assertFalse($this->authplugin->is_internal());
        $this->assertFalse($this->authplugin->user_login('username','password'));
        $this->assertTrue($this->authplugin->prevent_local_passwords());
    }

    /**
     * GIVEN a default configuration
     * WHEN we process the configuration
     * THEN all the properties are set
     */
    public function test_config_processes_defaults() {
        $config = new \stdClass();
        $this->authplugin->process_config($config);

        $config = get_config('auth/ouasharedcookie');
        $this->assertObjectHasAttribute('shared_login_url', $config);
        $this->assertObjectHasAttribute('no_account_url', $config);
        $this->assertObjectHasAttribute('logout_url', $config);
        $this->assertObjectHasAttribute('change_password_url', $config);
        $this->assertObjectHasAttribute('cookie_name', $config);
        $this->assertObjectHasAttribute('shared_cookie_domain', $config);
        $this->assertObjectHasAttribute('dosinglelogout', $config);
        $this->assertObjectHasAttribute('iterations', $config);
        $this->assertObjectHasAttribute('timeout', $config);
    }

    /**
     * @test
     *
     * GIVEN a user is logged in.
     *  WHEN they logout
     *  THEN the cookie will be removed.
     */
    public function cookie_is_removed_on_logout() {
        $_COOKIE['TESTCOOKIE'] = $this->authplugin->encrypt_cookie(867552, 'Admin', time()*1000);

        $user = $this->getDataGenerator()->create_user(array('idnumber' => 867552, 'auth' => 'ouasharedcookie'));
        $this->setUser($user->id);

        $this->olderrorhandler = set_error_handler(array($this, 'handle_headers_sent_warning'), E_ALL);
        $this->authplugin->logoutpage_hook();
        restore_error_handler();

        $this->assertArrayNotHasKey('TESTCOOKIE', $_COOKIE);
    }

    /**
     * GIVEN a user has logged out.
     *  WHEN the logout process has finished.
     *  THEN they will be given a redirect url that is stored in the configuration.
     * @test
     */
    public function redirect_url_is_set_correctly_on_logout() {
        global $redirect;

        $user = $this->getDataGenerator()->create_user(array('idnumber' => 867552, 'auth' => 'ouasharedcookie'));
        $this->setUser($user->id);

        $redirect = null;
        $this->authplugin->postlogout_hook($user);

        $this->assertEquals('LOGOUTURL', $redirect);
    }

    /**
     * GIVEN a user has logged in to the website
     *   AND user is NOT logged in to the LMS
     *   AND they don't exist as a user in the LMS.
     *  WHEN the login is processed
     *  THEN the alternateloginurl is set to the no account url.
     */
    public function test_redirect_url_is_set_correctly_on_non_existant_account() {
        global $CFG;

        $_COOKIE['TESTCOOKIE'] = $this->authplugin->encrypt_cookie(867552, 'Admin', time()*1000);

        $user = $this->getDataGenerator()->create_user(array('idnumber' => 8675521, 'auth' => 'ouasharedcookie'));
        $this->setUser(null); // User is logged out of LMS.

        $redirect = null;
        $this->authplugin->loginpage_hook($user);

        $this->assertDebuggingCalled('No users configured with idnumber for this authtype. idnumber: 867552');
        $this->assertEquals('NOACCOUNTURL', $CFG->alternateloginurl);
    }

    /**
     * GIVEN a user has logged in.
     *   AND they don't exist as a user in the LMS.
     *   AND redirect=0 is set
     *  WHEN the login is processed
     *  THEN the the login page is displayed.
     */
    public function test_no_redirect_on_non_existant_account_and_redirect_is_zero() {
        global $CFG;

        $_COOKIE['TESTCOOKIE'] = $this->authplugin->encrypt_cookie(867552, 'Admin', time()*1000);
        $_GET['redirect'] = 0;

        $user = $this->getDataGenerator()->create_user(array('idnumber' => 8675521, 'auth' => 'ouasharedcookie'));
        $this->setUser($user->id);

        $redirect = null;
        $this->authplugin->loginpage_hook($user);

        $this->assertDebuggingNotCalled('No users configured with idnumber for this authtype. idnumber: 867552');
        $this->assertEquals('', $CFG->alternateloginurl);
        $this->assertNull($redirect, 'No redirect should be set which redirect was set to 0.');
    }

    /**
     * Handle the session regeneration failure warning to ensure we can log a user in during test without errors
     * being reported.
     *
     * @param $errno PHP Error number.
     * @param $errstr PHP Error string.
     * @param $errfile File the error was contained in.
     * @param $errline The line number of the error.
     * @param $errcontext The context, which is unused.
     * @return bool Whether the original error handler should be processed.
     */
    public function handle_session_regeneration_warning($errno, $errstr, $errfile, $errline, $errcontext) {
        if ($errstr === 'session_regenerate_id(): Cannot regenerate session id - headers already sent') {
            return true;
        }
        return call_user_func($this->olderrorhandler, $errno, $errstr, $errfile, $errline, $errcontext);
    }

    /**
     * setcookie warnings when it is called from phpunit.
     *
     * @param $errno PHP Error number.
     * @param $errstr PHP Error string.
     * @param $errfile File the error was contained in.
     * @param $errline The line number of the error.
     * @param $errcontext The context, which is unused.
     * @return bool Whether the original error handler should be processed.
     */
    public function handle_headers_sent_warning($errno, $errstr, $errfile, $errline, $errcontext) {
        if (strpos($errstr, "Cannot modify header information - headers already sent by") === 0) {
            return true;
        }
        return call_user_func($this->olderrorhandler, $errno, $errstr, $errfile, $errline, $errcontext);
    }
}
