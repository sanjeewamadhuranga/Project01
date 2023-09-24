<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Http\ParamConverter;

use App\Application\Security\CognitoUser;
use App\Domain\Document\Security\Administrator;
use App\Infrastructure\Http\ParamConverter\CognitoUserParamConverter;
use App\Infrastructure\Security\CognitoUserManagerInterface;
use App\Tests\Unit\UnitTestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CognitoUserParamConverterTest extends UnitTestCase
{
    public function testItWillSupportWhenProperUserProvided(): void
    {
        $cognitoUserManager = $this->createStub(CognitoUserManagerInterface::class);
        $converter = new CognitoUserParamConverter($cognitoUserManager);

        $paramConverter = $this->createStub(ParamConverter::class);
        $paramConverter->method('getClass')->willReturn(CognitoUser::class);

        self::assertTrue($converter->supports($paramConverter));
    }

    public function testItWontSupportWhenWrongUserProvided(): void
    {
        $cognitoUserManager = $this->createStub(CognitoUserManagerInterface::class);
        $converter = new CognitoUserParamConverter($cognitoUserManager);

        $paramConverter = $this->createStub(ParamConverter::class);
        $paramConverter->method('getClass')->willReturn(Administrator::class);

        self::assertFalse($converter->supports($paramConverter));
    }

    public function testItThrowsExceptionWhenNoUserFoundAndItIsNotOptional(): void
    {
        $sub = 'someSuB';
        $cognitoUserManager = $this->createStub(CognitoUserManagerInterface::class);
        $cognitoUserManager->method('getUserBySub')->willReturn(null);

        $paramConverter = $this->createStub(ParamConverter::class);
        $paramConverter->method('isOptional')->willReturn(false);

        $request = $this->createStub(Request::class);
        $parameterBag = $this->createStub(ParameterBag::class);
        $parameterBag->method('get')->willReturn($sub);
        $request->attributes = $parameterBag;

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(sprintf('Could not find user with sub: %s', $sub));

        $converter = new CognitoUserParamConverter($cognitoUserManager);
        $converter->apply($request, $paramConverter);
    }

    public function testItDoesntThrowExceptionWhenNoUserFoundAndItIsOptional(): void
    {
        $sub = 'someSuB';
        $variableName = 'cognitoUser';
        $cognitoUserManager = $this->createStub(CognitoUserManagerInterface::class);
        $cognitoUserManager->method('getUserBySub')->willReturn(null);

        $paramConverter = $this->createStub(ParamConverter::class);
        $paramConverter->method('isOptional')->willReturn(true);
        $paramConverter->method('getName')->willReturn($variableName);

        $parameterBag = $this->createMock(ParameterBag::class);
        $parameterBag->method('get')->willReturn($sub);
        $parameterBag->expects(self::once())->method('set')->with($variableName, null);

        $request = $this->createStub(Request::class);
        $request->attributes = $parameterBag;

        $converter = new CognitoUserParamConverter($cognitoUserManager);
        $converter->apply($request, $paramConverter);
    }

    public function testItSetsUserWhenItIsFound(): void
    {
        $sub = 'someSuB';
        $user = $this->createStub(CognitoUser::class);
        $variableName = 'user';
        $cognitoUserManager = $this->createStub(CognitoUserManagerInterface::class);
        $cognitoUserManager->method('getUserBySub')->willReturn($user);

        $paramConverter = $this->createStub(ParamConverter::class);
        $paramConverter->method('isOptional')->willReturn(false);
        $paramConverter->method('getName')->willReturn($variableName);

        $parameterBag = $this->createMock(ParameterBag::class);
        $parameterBag->method('get')->willReturn($sub);
        $parameterBag->expects(self::once())->method('set')->with($variableName, $user);

        $request = $this->createStub(Request::class);
        $request->attributes = $parameterBag;

        $converter = new CognitoUserParamConverter($cognitoUserManager);
        $converter->apply($request, $paramConverter);
    }
}
