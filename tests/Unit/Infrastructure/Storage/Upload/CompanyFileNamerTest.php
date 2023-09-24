<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Storage\Upload;

use App\Domain\Document\Company\Company;
use App\Domain\Document\ComplianceFile;
use App\Domain\Document\Interfaces\CompanyAware;
use App\Infrastructure\Storage\Upload\CompanyFileNamer;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Util\Transliterator;

class CompanyFileNamerTest extends UnitTestCase
{
    protected readonly CompanyFileNamer $namer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->namer = new CompanyFileNamer(new Transliterator(new AsciiSlugger()));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public function fileDataProvider(): iterable
    {
        yield 'typical file name' => ['test.pdf', '#^507f191e810c19729de860ea_\d{8,10}_test\.pdf$#'];
        yield 'file name containing space' => ['My File .pdf', '#^507f191e810c19729de860ea_\d{8,10}_my_file\.pdf$#'];
        yield 'double extension' => ['my file.jpg.pdf', '#^507f191e810c19729de860ea_\d{8,10}_my_file_jpg\.pdf$#'];
        yield 'uppercase extension' => ['my file.JPG', '#^507f191e810c19729de860ea_\d{8,10}_my_file\.jpg$#'];
    }

    /**
     * @return iterable<string, array{CompanyAware}>
     */
    public function companyAwareObjectProvider(): iterable
    {
        $companyAware = $this->createStub(CompanyAware::class);
        $companyAware->method('getCompany')->willReturn($this->getCompany());

        yield 'CompanyAware' => [$companyAware];

        $complianceFile = new ComplianceFile();
        $complianceFile->setCompany($this->getCompany());

        yield 'ComplianceFile' => [$complianceFile];
    }

    /**
     * @dataProvider fileDataProvider
     */
    public function testNameReturnsAnUniqueName(string $originalName, string $pattern): void
    {
        $entity = $this->createStub(CompanyAware::class);
        $entity->method('getCompany')->willReturn($this->getCompany());

        $file = $this->createStub(UploadedFile::class);
        $file->method('getClientOriginalName')->willReturn($originalName);

        $mapping = $this->createMock(PropertyMapping::class);
        $mapping->expects(self::once())->method('getFile')->with($entity)->willReturn($file);

        self::assertMatchesRegularExpression($pattern, $this->namer->name($entity, $mapping));
    }

    /**
     * @dataProvider companyAwareObjectProvider
     */
    public function testItHandlesCompanyAwareObjects(CompanyAware $object): void
    {
        $file = $this->createStub(UploadedFile::class);
        $file->method('getClientOriginalName')->willReturn('test.png');

        $mapping = $this->createMock(PropertyMapping::class);
        $mapping->expects(self::once())->method('getFile')->with($object)->willReturn($file);

        self::assertMatchesRegularExpression('#^507f191e810c19729de860ea_\d{8,10}_test.png$#', $this->namer->name($object, $mapping));
    }

    public function testItReturnsEmptyNameIfFileIsNotPresent(): void
    {
        $entity = new ComplianceFile();
        $mapping = $this->createMock(PropertyMapping::class);
        $mapping->expects(self::once())->method('getFile')->with($entity)->willReturn(null);

        self::assertSame('', $this->namer->name($entity, $mapping));
    }

    private function getCompany(): Company
    {
        $company = $this->createStub(Company::class);
        $company->method('getId')->willReturn('507f191e810c19729de860ea');

        return $company;
    }
}
