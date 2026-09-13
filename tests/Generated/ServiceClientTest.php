<?php

declare(strict_types=1);

namespace Ondewo\Survey\Tests\Generated;

use Grpc\BaseStub;
use Grpc\ChannelCredentials;
use Ondewo\Survey\Auth\BearerTokenAuthenticator;
use Ondewo\Survey\FHIRClient;
use Ondewo\Survey\SurveysClient;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Constructs the generated service stubs against a dummy target. gRPC channels connect lazily, so
 * nothing here touches the network - but the stub, its channel options and its method surface are
 * all real.
 *
 * PRODUCT-SPECIFIC: the service and method names below come from ondewo-survey-api.
 *
 * There is deliberately no streaming case here: ondewo-survey-api declares no streaming RPC at
 * all, so neither generated stub has a `_bidiRequest` or a `_serverStreamRequest` call to assert
 * on.
 */
final class ServiceClientTest extends TestCase
{
    private const DUMMY_TARGET = 'localhost:50051';

    /**
     * @var list<BaseStub>
     */
    private array $openClients = [];

    protected function tearDown(): void
    {
        foreach ($this->openClients as $client) {
            $client->close();
        }
        $this->openClients = [];

        parent::tearDown();
    }

    public function testAServiceClientIsConstructedAgainstAnInsecureChannel(): void
    {
        $client = $this->open(new SurveysClient(self::DUMMY_TARGET, [
            'credentials' => ChannelCredentials::createInsecure(),
        ]));

        self::assertInstanceOf(BaseStub::class, $client);
        // Contains, not equals: gRPC canonicalises the target (`dns:///localhost:50051`) in some
        // core versions.
        self::assertStringContainsString(self::DUMMY_TARGET, $client->getTarget());
    }

    public function testAServiceClientAcceptsTheHandWrittenBearerAuthenticator(): void
    {
        $authenticator = new BearerTokenAuthenticator('a-token');

        // The point of the hand-written auth surface: its output IS a valid `$opts` array for a
        // generated stub. \Grpc\BaseStub rejects a missing `credentials` key and a non-callable
        // `update_metadata`, so constructing successfully proves both.
        $client = $this->open(new SurveysClient(self::DUMMY_TARGET, $authenticator->channelOptions()));

        self::assertStringContainsString(self::DUMMY_TARGET, $client->getTarget());
    }

    #[DataProvider('unaryMethods')]
    public function testTheExpectedUnaryMethodsExist(string $method): void
    {
        self::assertTrue(
            method_exists(SurveysClient::class, $method),
            SurveysClient::class . '::' . $method . '() is missing from the generated stub'
        );

        $reflected = new ReflectionMethod(SurveysClient::class, $method);
        self::assertTrue($reflected->isPublic());
        // <request message>, array $metadata = [], array $options = []
        self::assertSame(3, $reflected->getNumberOfParameters());
        self::assertSame(1, $reflected->getNumberOfRequiredParameters());
    }

    /**
     * Every RPC of `ondewo.survey.Surveys` - the service is small enough to list in full.
     *
     * @return iterable<string, array{string}>
     */
    public static function unaryMethods(): iterable
    {
        foreach ([
            'CreateSurvey',
            'GetSurvey',
            'UpdateSurvey',
            'DeleteSurvey',
            'ListSurveys',
            'GetSurveyAnswers',
            'GetAllSurveyAnswers',
            'CreateAgentSurvey',
            'UpdateAgentSurvey',
            'DeleteAgentSurvey',
        ] as $method) {
            yield $method => [$method];
        }
    }

    #[DataProvider('fhirMethods')]
    public function testTheSecondServiceIsGeneratedToo(string $method): void
    {
        // Survey is one of the two ONDEWO apis with more than one service in it; the FHIR one is
        // easy to lose when the .proto that declares it stops being compiled.
        self::assertTrue(
            method_exists(FHIRClient::class, $method),
            FHIRClient::class . '::' . $method . '() is missing from the generated stub'
        );

        $reflected = new ReflectionMethod(FHIRClient::class, $method);
        self::assertTrue($reflected->isPublic());
        self::assertSame(1, $reflected->getNumberOfRequiredParameters());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function fhirMethods(): iterable
    {
        foreach ([
            'CreateFHIRSurvey',
            'GetFHIRSurveyAnswers',
            'GetAllFHIRSurveyAnswers',
        ] as $method) {
            yield $method => [$method];
        }
    }

    public function testTheGeneratedMethodSurfaceIsNotEmpty(): void
    {
        $methods = get_class_methods(SurveysClient::class);

        self::assertContains('CreateSurvey', $methods);
        // 10 RPCs + the constructor + the 5 public methods inherited from \Grpc\BaseStub. The
        // bound is the REAL surface of this single-service product, not the >20 of a large api.
        self::assertGreaterThanOrEqual(
            16,
            count($methods),
            'SurveysClient exposes suspiciously few methods - the service proto may not have been compiled'
        );
    }

    private function open(BaseStub $client): BaseStub
    {
        $this->openClients[] = $client;

        return $client;
    }
}
