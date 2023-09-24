<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Http\ParamConverter;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Company\User;
use App\Domain\Document\Security\Administrator as SecurityUser;
use App\Infrastructure\Http\ParamConverter\CompanyUserParamConverter;
use App\Tests\Unit\UnitTestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompanyUserParamConverterTest extends UnitTestCase
{
    private CompanyUserParamConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new CompanyUserParamConverter();
    }

    public function testWillSupportWhenProperUserProvided(): void
    {
        $paramConverter = $this->createMock(ParamConverter::class);
        $paramConverter
            ->expects(self::once())
            ->method('getClass')
            ->willReturn(new User());

        self::assertTrue($this->converter->supports($paramConverter));
    }

    public function testWontSupportWhenWrongUserProvided(): void
    {
        $paramConverter = $this->createMock(ParamConverter::class);
        $paramConverter
            ->expects(self::once())
            ->method('getClass')
            ->willReturn(new SecurityUser());

        self::assertFalse($this->converter->supports($paramConverter));
    }

    public function testWontApplyWhenIncorrectCompanyParameter(): void
    {
        $parameterBag = $this->createMock(ParameterBag::class);
        $parameterBag->expects(self::once())->method('get');
        $parameterBag->method('get')->with('company')->willReturn('some incorrect company');
        $request = $this->getRequest($parameterBag);

        self::assertFalse($this->converter->apply($request, new ParamConverter()));
    }

    public function testWillApplyWhenProperRequestAttributes(): void
    {
        $company = $this->createMock(Company::class);
        $company->expects(self::once())->method('getUser')->willReturn(new User());

        $parameterBag = $this->createMock(ParameterBag::class);
        $parameterBag
            ->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive(['company'], ['userId'])
            ->willReturnOnConsecutiveCalls($company, 'userId');
        $request = $this->getRequest($parameterBag);

        $paramConverter = $this->createMock(ParamConverter::class);
        $paramConverter->expects(self::once())->method('getName')->willReturn('name');

        self::assertTrue($this->converter->apply($request, $paramConverter));
    }

    public function testWillThrowExceptionWhenNoUserInCompany(): void
    {
        $company = $this->createMock(Company::class);
        $company->expects(self::once())->method('getUser')->willReturn(null);

        $parameterBag = $this->createMock(ParameterBag::class);
        $parameterBag
            ->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive(['company'], ['userId'])
            ->willReturnOnConsecutiveCalls($company, 'userId');
        $request = $this->getRequest($parameterBag);

        self::expectException(NotFoundHttpException::class);
        $this->converter->apply($request, new ParamConverter());
    }

    private function getRequest(ParameterBag $parameterBag): Request
    {
        $request = $this->createMock(Request::class);
        $request->attributes = $parameterBag;

        return $request;
    }
}
