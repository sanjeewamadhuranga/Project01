<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Http\ParamConverter;

use App\Domain\Document\Company\Company;
use App\Domain\Document\Location\Location;
use App\Infrastructure\Http\ParamConverter\CompanyLocationParamConverter;
use App\Tests\Unit\UnitTestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use stdClass;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompanyLocationParamConverterTest extends UnitTestCase
{
    private CompanyLocationParamConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new CompanyLocationParamConverter();
    }

    public function testItSupportsWhenLocationClassProvided(): void
    {
        $paramConverter = $this->createMock(ParamConverter::class);
        $paramConverter
            ->expects(self::once())
            ->method('getClass')
            ->willReturn(new Location());

        self::assertTrue($this->converter->supports($paramConverter));
    }

    public function testItDoesNotSupportsWhenOtherClassProvided(): void
    {
        $paramConverter = $this->createMock(ParamConverter::class);
        $paramConverter
            ->expects(self::once())
            ->method('getClass')
            ->willReturn(new stdClass());

        self::assertFalse($this->converter->supports($paramConverter));
    }

    public function testItReturnsFalseWhenWrongCompanyProvided(): void
    {
        $parameterBag = $this->createStub(ParameterBag::class);
        $parameterBag->method('get')->willReturn(null);

        self::assertFalse($this->converter->apply($this->getRequest($parameterBag), $this->createStub(ParamConverter::class)));
    }

    public function testItThrowsExceptionWhenLocationNotInCompany(): void
    {
        $locationId = '62012071631bc157d669a031';
        $company = $this->createStub(Company::class);
        $company->method('getLocation')->willReturn(null);

        $parameterBag = $this->createStub(ParameterBag::class);
        $parameterBag->method('get')->willReturnOnConsecutiveCalls($company, $locationId);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(sprintf('Could not find location with id: %s', $locationId));

        $this->converter->apply($this->getRequest($parameterBag), $this->createStub(ParamConverter::class));
    }

    public function testItSetsLocationWhenFounded(): void
    {
        $name = 'location';
        $paramConverted = $this->createStub(ParamConverter::class);
        $paramConverted->method('getName')->willReturn($name);

        $location = new Location();
        $company = $this->createStub(Company::class);
        $company->method('getLocation')->willReturn($location);

        $parameterBag = $this->createMock(ParameterBag::class);
        $parameterBag->method('get')->willReturnOnConsecutiveCalls($company, '6201214f4c7556679e0270f9');
        $parameterBag->expects(self::once())->method('set')->with($name, $location);

        self::assertTrue($this->converter->apply($this->getRequest($parameterBag), $paramConverted));
    }

    private function getRequest(ParameterBag $parameterBag): Request
    {
        $request = $this->createStub(Request::class);
        $request->attributes = $parameterBag;

        return $request;
    }
}
