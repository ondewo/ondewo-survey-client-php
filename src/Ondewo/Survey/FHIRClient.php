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
 * ///// FHIR Services ///////
 *
 * The following servicer was designed to support the FHIR standard.
 * Both Questionnaires and Responses will be detected and transformed for a simpler usage.
 *
 */
class FHIRClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * Create a Survey from FHIR format and an empty NLU Agent for it
     * @param \Ondewo\Survey\CreateFHIRSurveyRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function CreateFHIRSurvey(\Ondewo\Survey\CreateFHIRSurveyRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/ondewo.survey.FHIR/CreateFHIRSurvey',
        $argument,
        ['\Ondewo\Survey\Survey', 'decode'],
        $metadata, $options);
    }

    /**
     * Get Survey Answers on FHIR format
     * @param \Ondewo\Survey\GetSurveyAnswersRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetFHIRSurveyAnswers(\Ondewo\Survey\GetSurveyAnswersRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/ondewo.survey.FHIR/GetFHIRSurveyAnswers',
        $argument,
        ['\Ondewo\Survey\SurveyFHIRAnswersResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * Get all Survey Answers on FHIR format
     * @param \Ondewo\Survey\GetAllSurveyAnswersRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function GetAllFHIRSurveyAnswers(\Ondewo\Survey\GetAllSurveyAnswersRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/ondewo.survey.FHIR/GetAllFHIRSurveyAnswers',
        $argument,
        ['\Ondewo\Survey\SurveyFHIRAnswersResponse', 'decode'],
        $metadata, $options);
    }

}
