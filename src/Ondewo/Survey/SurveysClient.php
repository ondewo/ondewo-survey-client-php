<?php
// GENERATED CODE -- DO NOT EDIT!

// Original file comments:
// Copyright 2020 ONDEWO GmbH
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License. (editesyntax = "proto3";
namespace Ondewo\Survey;

/**
 * ///// Services ///////
 *
 */
class SurveysClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * Create a Survey and an empty NLU Agent for it
     * @param \Ondewo\Survey\CreateSurveyRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function CreateSurvey(\Ondewo\Survey\CreateSurveyRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/ondewo.survey.Surveys/CreateSurvey',
        $argument,
        ['\Ondewo\Survey\Survey', 'decode'],
        $metadata, $options);
    }

    /**
     * Retrieve a Survey message from the Database and return it
     * @param \Ondewo\Survey\GetSurveyRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetSurvey(\Ondewo\Survey\GetSurveyRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/ondewo.survey.Surveys/GetSurvey',
        $argument,
        ['\Ondewo\Survey\Survey', 'decode'],
        $metadata, $options);
    }

    /**
     * Update an existing Survey message from the Database and return it
     * @param \Ondewo\Survey\UpdateSurveyRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function UpdateSurvey(\Ondewo\Survey\UpdateSurveyRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/ondewo.survey.Surveys/UpdateSurvey',
        $argument,
        ['\Ondewo\Survey\Survey', 'decode'],
        $metadata, $options);
    }

    /**
     * Delete a survey and its associated agent (if existent)
     * @param \Ondewo\Survey\DeleteSurveyRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function DeleteSurvey(\Ondewo\Survey\DeleteSurveyRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/ondewo.survey.Surveys/DeleteSurvey',
        $argument,
        ['\Google\Protobuf\GPBEmpty', 'decode'],
        $metadata, $options);
    }

    /**
     * Returns the list of all surveys in the server
     * @param \Ondewo\Survey\ListSurveysRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function ListSurveys(\Ondewo\Survey\ListSurveysRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/ondewo.survey.Surveys/ListSurveys',
        $argument,
        ['\Ondewo\Survey\ListSurveysResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Retrieve answers to survey questions collected in interactions with a survey agent for a specific session
     * @param \Ondewo\Survey\GetSurveyAnswersRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetSurveyAnswers(\Ondewo\Survey\GetSurveyAnswersRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/ondewo.survey.Surveys/GetSurveyAnswers',
        $argument,
        ['\Ondewo\Survey\SurveyAnswersResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Retrieve all answers to survey questions collected in interactions with a survey agent in any session
     * @param \Ondewo\Survey\GetAllSurveyAnswersRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetAllSurveyAnswers(\Ondewo\Survey\GetAllSurveyAnswersRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/ondewo.survey.Surveys/GetAllSurveyAnswers',
        $argument,
        ['\Ondewo\Survey\SurveyAnswersResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Populate and configures an NLU Agent from a Survey
     * @param \Ondewo\Survey\AgentSurveyRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function CreateAgentSurvey(\Ondewo\Survey\AgentSurveyRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/ondewo.survey.Surveys/CreateAgentSurvey',
        $argument,
        ['\Ondewo\Survey\AgentSurveyResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Update an NLU agent from a survey
     * @param \Ondewo\Survey\AgentSurveyRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function UpdateAgentSurvey(\Ondewo\Survey\AgentSurveyRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/ondewo.survey.Surveys/UpdateAgentSurvey',
        $argument,
        ['\Ondewo\Survey\AgentSurveyResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Deletes all data of an NLU agent associated to a survey
     * @param \Ondewo\Survey\AgentSurveyRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function DeleteAgentSurvey(\Ondewo\Survey\AgentSurveyRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/ondewo.survey.Surveys/DeleteAgentSurvey',
        $argument,
        ['\Google\Protobuf\GPBEmpty', 'decode'],
        $metadata, $options);
    }

}
