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

/**
 * The criteria for a cross-tracker search.
 * Includes both semantic field criteria (e.g. title, status) and shared field
 * ones.
 */
class Tracker_CrossSearch_Query {
    /**
     * @var array of array
     */
    private $shared_fields_criteria;
    
    /**
     * @var array of string
     */
    private $semantic_criteria;

    private $artifact_ids;
    
    /**
     * @param array of array $shared_fields_criteria
     * @param array of string $semantic_criteria
     * @param $artifact_ids array(tracker_id_1 => array(artifact_id_1, artifact_id_2), tracker_id_2 => array(artifact_id_3))
     */
    public function __construct(Array $shared_fields_criteria=array(), Array $semantic_criteria = array(), Array $artifact_ids = array()) {
        $this->shared_fields_criteria = $shared_fields_criteria;
        $this->semantic_criteria      = $semantic_criteria ? $semantic_criteria : array('title' => '', 'status' => '');
        $this->artifact_ids           = $artifact_ids;
    }
    
    /**
     * @return array
     */
    public function toArrayOfDoom() {
        return array(
            'shared_fields' => $this->shared_fields_criteria,
            'semantic'      => $this->semantic_criteria,
            'artifacts'     => $this->artifact_ids
        );
    }
    
    public function getSharedFields() {
        return $this->shared_fields_criteria;
    }
    
    public function getSemanticCriteria() {
        return $this->semantic_criteria;
    }
    
    public function getTitle() {
        return $this->semantic_criteria['title'];
    }
    
    public function getStatus() {
        return $this->semantic_criteria['status'];
    }
    
    /**
     * @return the flattened list of artifact_ids
     */
    public function listArtifactIds() {
        $id_list = array();
        foreach ($this->artifact_ids as $artifact_ids) {
            $id_list = array_merge($id_list, $artifact_ids);
        }
        
        return $id_list;
    }
    
    //TODO : is it going to be used?
    public function getArtifactsOfTracker($tracker_id) {
        $artifacts = array();
        if (isset($this->artifact_ids[$tracker_id])) {
            foreach($this->artifact_ids[$tracker_id] as $artifact_id) {
                $artifacts[] = new Tracker_Artifact($artifact_id, $tracker_id, null, null, null);
            }
        }
        return $artifacts;
    }
    /**
     * add the public property isSelected on each artifact of $artifact_list
     * A selected artifact must be shwn as selected in the html select box of the view.
     * An artifact is selected if it exists in this $artifacts_ids
     * ///TODO: find a better to do this.
     *  
     * @param int   $tracker_id    the tracker id of artifacts 
     * @param array $artifact_list an artifact list
     * 
     * @return array the artifact list 
     */
    public function setSelectedArtifacts($tracker_id, array $artifact_list) {
        $selected = isset($this->artifact_ids[$tracker_id]);
        foreach($artifact_list as $artifact) {
            $artifact->isSelected = $selected && in_array($artifact->getId(), $this->artifact_ids[$tracker_id]);
        }
        return $artifact_list;
    }
}
?>
