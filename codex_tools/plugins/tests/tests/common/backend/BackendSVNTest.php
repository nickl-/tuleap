<?php
/* 
 * Copyright (c) The Codendi Team, Xerox, 2009. All Rights Reserved.
 *
 * This file is a part of Codendi.
 *
 * Codendi is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Codendi is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Codendi; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * 
 */


require_once('common/backend/BackendSVN.class.php');
require_once('common/user/UserManager.class.php');
Mock::generate('UserManager');
require_once('common/user/User.class.php');
Mock::generate('User');
require_once('common/project/ProjectManager.class.php');
Mock::generate('ProjectManager');
require_once('common/project/Project.class.php');
Mock::generate('Project');
Mock::generatePartial('BackendSVN', 'BackendSVNTestVersion', array('_getUserManager', 
                                                             '_getProjectManager',
                                                             'chown',
                                                             'chgrp',
                                                             ));


class BackendSVNTest extends UnitTestCase {
    
    function __construct($name = 'BackendSVN test') {
        parent::__construct($name);
    }

    function setUp() {
        $GLOBALS['svn_prefix']                = dirname(__FILE__) . '/_fixtures/svnroot';
        $GLOBALS['tmp_dir']                   = dirname(__FILE__) . '/_fixtures/var/tmp';
    }
    
    function tearDown() {
        unset($GLOBALS['svn_prefix']);
        unset($GLOBALS['tmp_dir']);
    }
    
    function testConstructor() {
        $backend = BackendSVN::instance();
    }
    

    function testArchiveProjectSVN() {
        $project =& new MockProject($this);
        $project->setReturnValue('getUnixName', 'TestProj',array(false));
        $project->setReturnValue('getUnixName', 'testproj',array(true));

        $pm =& new MockProjectManager();
        $pm->setReturnReference('getProject', $project, array(142));

        $backend =& new BackendSVNTestVersion($this);
        $backend->setReturnValue('_getProjectManager', $pm);

        $projdir=$GLOBALS['svn_prefix']."/TestProj";

        // Setup test data
        mkdir($projdir);
        mkdir($projdir."/db");
        
        //$this->assertTrue(is_dir($projdir),"Project dir should be created");

        $this->assertEqual($backend->archiveProjectSVN(142),True);
        $this->assertFalse(is_dir($projdir),"Project SVN repository should be deleted");
        $this->assertTrue(is_file($GLOBALS['tmp_dir']."/TestProj-svn.tgz"),"SVN Archive should be created");

        // Check that a wrong project id does not raise an error
        $this->assertEqual($backend->archiveProjectSVN(99999),False);

        // Cleanup
        unlink($GLOBALS['tmp_dir']."/TestProj-svn.tgz");
    }


    function testCreateProjectSVN() {
        $project =& new MockProject($this);
        $project->setReturnValue('getUnixName', 'TestProj',array(false));
        $project->setReturnValue('getUnixName', 'testproj',array(true));
        $project->setReturnValue('isSVNTracked',true);
        $proj_members = array("0" =>
                              array (
                                     "user_name"=> "user1",
                                     "user_id"  => "1"),
                              "1" =>
                              array (
                                     "user_name"=> "user2",
                                     "user_id"  => "2"),
                              "2" =>
                              array (
                                     "user_name"=> "user3",
                                     "user_id"  => "3"));

        $project->setReturnValue('getMembersUserNames',$proj_members);

        $pm =& new MockProjectManager();
        $pm->setReturnReference('getProject', $project, array(142));

        $backend =& new BackendSVNTestVersion($this);
        $backend->setReturnValue('_getProjectManager', $pm);

        $this->assertEqual($backend->createProjectSVN(142),True);
        $this->assertTrue(is_dir($GLOBALS['svn_prefix']."/TestProj"),"SVN dir should be created");
        $this->assertTrue(is_dir($GLOBALS['svn_prefix']."/TestProj/hooks"),"hooks dir should be created");
        $this->assertTrue(is_file($GLOBALS['svn_prefix']."/TestProj/hooks/post-commit"),"post-commit file should be created");


        // Cleanup
        $backend->recurseDeleteInDir($GLOBALS['svn_prefix']."/TestProj");
        rmdir($GLOBALS['svn_prefix']."/TestProj");
    }
}
?>
