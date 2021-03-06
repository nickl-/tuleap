<?php
/**
 * Copyright (c) Enalean, 2012. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

// This is an on going work to help developers to build more expressive tests
// please add the functions/methods below when needed.
// For further information about the Test Data Builder pattern
// @see http://nat.truemesh.com/archives/000727.html

require_once(dirname(__FILE__).'/../../include/Tracker/TrackerManager.class.php');

function aTextField() {
    return new Test_Tracker_FormElement_Builder('Tracker_FormElement_Field_Text');
}

function anIntegerField() {
    return new Test_Tracker_FormElement_Builder('Tracker_FormElement_Field_Integer');
}

function aFloatField() {
    return new Test_Tracker_FormElement_Builder('Tracker_FormElement_Field_Float');
}

function aStringField() {
    return new Test_Tracker_FormElement_Builder('Tracker_FormElement_Field_String');
}

function aDateField() {
    return new Test_Tracker_FormElement_Builder('Tracker_FormElement_Field_Date');
}

function anOpenListField() {
    return new Test_Tracker_FormElement_Builder('Tracker_FormElement_Field_OpenList');
}

function anArtifactLinkField() {
    return new Test_Tracker_FormElement_Builder('Tracker_FormElement_Field_ArtifactLink');
}

function aSelectBoxField() {
    return new Test_Tracker_FormElement_Builder('Tracker_FormElement_Field_Selectbox');
}

class Test_Tracker_FormElement_Builder {
    private $name;
    private $id;
    private $tracker;
    private $trackerId;
    private $originalField;
    
    public function __construct($klass) {
        $this->name = $klass;
    }
    
    public function withId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function withTracker($tracker) {
        $this->tracker   = $tracker;
        return $this;
    }
    
    public function withTrackerId($trackerId) {
        $this->trackerId = $trackerId;
        return $this;
    }
    
    /**
     * @return Tracker_FormElement
     */
    public function build() {
        $klass  = $this->name;
        $object = new $klass($this->id, $this->trackerId, null, null, null, null, null, null, null, null, null, $this->originalField);
        if ($this->tracker) {
            $object->setTracker($this->tracker);
        }
        return $object;
    }
}

?>
