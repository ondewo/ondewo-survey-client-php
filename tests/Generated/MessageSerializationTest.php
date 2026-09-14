<?php

declare(strict_types=1);

namespace Ondewo\Survey\Tests\Generated;

use Ondewo\Survey\Answer;
use Ondewo\Survey\Answer\UserInfo;
use Ondewo\Survey\ListSurveysRequest;
use Ondewo\Survey\ScaleQuestion\ScaleValue;
use Ondewo\Survey\SubFlow;
use Ondewo\Survey\Survey;
use Ondewo\Survey\Survey\AgentStatus;
use Ondewo\Survey\SurveyInfo;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

/**
 * Wire-level exercise of the generated messages. These are the assertions that catch a broken
 * generator: a field that is declared but never written, a sub-message whose presence is lost, an
 * enum whose zero constant moved.
 *
 * PRODUCT-SPECIFIC: the message and enum names below come from ondewo-survey-api. Replicating this
 * suite to another ONDEWO client means swapping them for that api's own messages.
 *
 * There is deliberately no `proto3 optional` case here: ondewo-survey-api declares no `optional`
 * field anywhere, so the whole generated tree has no explicit-presence scalar to assert on.
 */
final class MessageSerializationTest extends TestCase
{
    public function testAMessageSurvivesABinaryRoundTrip(): void
    {
        $survey = new Survey();
        $survey->setSurveyId('projects/6b2c8e5a/agent');
        $survey->setDisplayName('ondewo-test-survey');
        $survey->setLanguageCode('de');
        $survey->setExcludeSubflows([SubFlow::LEGAL_ENTITY, SubFlow::PHONE_HOURS]);
        $survey->setStatus(AgentStatus::UPDATING);
        $survey->setSurveyInfo(new SurveyInfo([
            'legal_entity' => 'ONDEWO GmbH',
            'topic' => 'customer satisfaction',
            'anonymous' => true,
        ]));

        $bytes = $survey->serializeToString();
        self::assertNotSame('', $bytes, 'a populated message serialised to zero bytes');

        $parsed = new Survey();
        $parsed->mergeFromString($bytes);

        self::assertSame('projects/6b2c8e5a/agent', $parsed->getSurveyId());
        self::assertSame('ondewo-test-survey', $parsed->getDisplayName());
        self::assertSame('de', $parsed->getLanguageCode());
        self::assertSame(
            [SubFlow::LEGAL_ENTITY, SubFlow::PHONE_HOURS],
            iterator_to_array($parsed->getExcludeSubflows())
        );
        self::assertSame(AgentStatus::UPDATING, $parsed->getStatus());

        self::assertTrue($parsed->hasSurveyInfo());
        self::assertSame('ONDEWO GmbH', $parsed->getSurveyInfo()->getLegalEntity());
        self::assertSame('customer satisfaction', $parsed->getSurveyInfo()->getTopic());
        self::assertTrue($parsed->getSurveyInfo()->getAnonymous());

        // Byte-for-byte stability, which field-by-field getters alone would not prove.
        self::assertSame($bytes, $parsed->serializeToString());
    }

    public function testAnUnsetSubMessageStaysUnset(): void
    {
        $survey = new Survey();
        $survey->setDisplayName('no-survey-info');

        self::assertFalse($survey->hasSurveyInfo());
        self::assertNull($survey->getSurveyInfo());

        $survey->setSurveyInfo(new SurveyInfo(['legal_entity' => 'ONDEWO GmbH']));
        self::assertTrue($survey->hasSurveyInfo());

        $survey->clearSurveyInfo();
        self::assertFalse($survey->hasSurveyInfo());
    }

    public function testANestedSubMessageSurvivesABinaryRoundTrip(): void
    {
        // Answer.UserInfo is generated into its own nested namespace (Ondewo\Survey\Answer), which
        // is where a broken generator most easily loses a message.
        $answer = new Answer();
        $answer->setQuestionNr(3);
        $answer->setSessionId('session-1');
        $answer->setAnswerText('very satisfied');
        $answer->setAnonymous(false);
        $answer->setUserInformation(new UserInfo([
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'user_id' => 'user-1',
        ]));

        $parsed = new Answer();
        $parsed->mergeFromString($answer->serializeToString());

        self::assertSame(3, $parsed->getQuestionNr());
        self::assertSame('session-1', $parsed->getSessionId());
        self::assertSame('very satisfied', $parsed->getAnswerText());
        self::assertFalse($parsed->getAnonymous());
        self::assertTrue($parsed->hasUserInformation());
        self::assertInstanceOf(UserInfo::class, $parsed->getUserInformation());
        self::assertSame('Ada', $parsed->getUserInformation()->getFirstName());
        self::assertSame('Lovelace', $parsed->getUserInformation()->getLastName());
    }

    public function testAMessageSurvivesAJsonRoundTrip(): void
    {
        $survey = new Survey();
        $survey->setSurveyId('survey-2');
        $survey->setDisplayName('json-round-trip');
        $survey->setStatus(AgentStatus::OUTDATED);

        $json = $survey->serializeToJsonString();
        self::assertJson($json);
        // The JSON mapping spells an enum with its NAME, not its number.
        self::assertStringContainsString('OUTDATED', $json);

        $parsed = new Survey();
        $parsed->mergeFromJsonString($json);

        self::assertSame('survey-2', $parsed->getSurveyId());
        self::assertSame('json-round-trip', $parsed->getDisplayName());
        self::assertSame(AgentStatus::OUTDATED, $parsed->getStatus());

        $request = new ListSurveysRequest();
        $request->setPageToken('page-2');

        $parsedRequest = new ListSurveysRequest();
        $parsedRequest->mergeFromJsonString($request->serializeToJsonString());

        self::assertSame('page-2', $parsedRequest->getPageToken());
    }

    public function testAnIntegerFieldSurvivesAJsonRoundTrip(): void
    {
        // Its own case because google/protobuf's PURE-PHP JSON parser range-checks every integer
        // with bccomp(): without ext-bcmath this dies with "Call to undefined function
        // Google\Protobuf\Internal\bccomp()" on the first int field it meets. The extension is a
        // `suggest` of google/protobuf, not a `require`, so nothing else would surface that.
        // ondewo-survey-api declares its two integer kinds in two different messages, and both
        // are covered here: JSON spells an int64 as a string and an int32 as a number, which are
        // different branches of the parser - and of the range check.
        $answer = new Answer();
        $answer->setQuestionNr(1700000000123);
        $answer->setSessionId('session-1');
        $answer->setAnswerText('very satisfied');

        $answerJson = $answer->serializeToJsonString();

        // The integers have to REACH the JSON or the parser never range-checks them, and the case
        // would be green with or without the extension: a proto3 scalar at its zero value is
        // omitted from the JSON entirely.
        self::assertStringContainsString('"questionNr":"1700000000123"', $answerJson);

        $parsedAnswer = new Answer();
        $parsedAnswer->mergeFromJsonString($answerJson);

        self::assertSame(1700000000123, $parsedAnswer->getQuestionNr());
        self::assertSame('session-1', $parsedAnswer->getSessionId());
        self::assertSame('very satisfied', $parsedAnswer->getAnswerText());

        $scaleValue = new ScaleValue();
        $scaleValue->setValue(5);
        $scaleValue->setLabel('very satisfied');

        $scaleJson = $scaleValue->serializeToJsonString();
        self::assertStringContainsString('"value":5', $scaleJson);

        $parsedScaleValue = new ScaleValue();
        $parsedScaleValue->mergeFromJsonString($scaleJson);

        self::assertSame(5, $parsedScaleValue->getValue());
        self::assertSame('very satisfied', $parsedScaleValue->getLabel());
    }

    public function testTheEnumZeroValueIsTheDefaultOfAFieldTypedByIt(): void
    {
        self::assertSame(0, AgentStatus::TO_BE_INITIALIZED);
        self::assertSame('TO_BE_INITIALIZED', AgentStatus::name(AgentStatus::TO_BE_INITIALIZED));
        self::assertSame(AgentStatus::UPDATED, AgentStatus::value('UPDATED'));

        // The zero value must be requestable, i.e. it must be the DEFAULT of a field typed by it.
        self::assertSame(AgentStatus::TO_BE_INITIALIZED, (new Survey())->getStatus());

        // SubFlow spells its zero member out as UNSPECIFIED, the ONDEWO api convention.
        self::assertSame(0, SubFlow::SUBFLOW_UNSPECIFIED);
        self::assertSame('SUBFLOW_UNSPECIFIED', SubFlow::name(SubFlow::SUBFLOW_UNSPECIFIED));
    }

    public function testAnUnknownEnumMemberIsRejected(): void
    {
        $this->expectException(UnexpectedValueException::class);

        AgentStatus::name(4242);
    }
}
