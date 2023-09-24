<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Form\Company;

use App\Domain\Company\CompanyDataIntegration;
use App\Domain\Settings\SystemSettings;
use App\Infrastructure\Form\Company\AppType;
use App\Infrastructure\Form\Company\CompanyResellerMetadataType;
use App\Infrastructure\Form\Company\ResellerMetadata\BmlEposType;
use App\Infrastructure\Form\Company\ResellerMetadata\BmlMposType;
use App\Infrastructure\Form\Company\ResellerMetadata\CommbankType;
use App\Infrastructure\Form\Company\ResellerMetadata\CyberSourceType;
use App\Infrastructure\Form\Company\ResellerMetadata\DialogType;
use App\Infrastructure\Form\Company\ResellerMetadata\MpgsType;
use App\Infrastructure\Form\Company\ResellerMetadata\ApmType;
use App\Infrastructure\Form\Company\ResellerMetadata\Type;
use App\Infrastructure\Form\Company\ResellerMetadata\TapToPayType;
use App\Infrastructure\Form\Type\MccType;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompanyResellerMetadataTypeTest extends TypeTestCase
{
    private SystemSettings&MockObject $settings;

    protected function setUp(): void
    {
        $this->settings = $this->createMock(SystemSettings::class);

        parent::setUp();
    }

    public function testItThrowsExceptionWhenNoCompanyDataIntegrationProvided(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory->create(CompanyResellerMetadataType::class, options: ['companyDataIntegration' => null]);
    }

    public function testItThrowsExceptionWhenWrongCompanyDataIntegrationProvided(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory->create(CompanyResellerMetadataType::class, options: ['companyDataIntegration' => AppType::class]);
    }

    public function testItThrowsExceptionWhenIntegrationIsNotEnabled(): void
    {
        $this->settings->method('getValue')->willReturn([CompanyDataIntegration::->value]);
        $integration = CompanyDataIntegration::_APM;

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(sprintf('Integration "%s" is not enabled.', $integration->value));

        $this->factory->create(CompanyResellerMetadataType::class, options: ['companyDataIntegration' => $integration]);
    }

    /**
     * @dataProvider formTypeAndFieldNameProvider
     */
    public function testItBuildsFormWithExpectedFieldWhenCorrectCompanyDataIntegrationProvided(CompanyDataIntegration $integration, string $fieldName): void
    {
        $this->settings->method('getValue')->willReturn([$integration->value]);
        $form = $this->factory->create(CompanyResellerMetadataType::class, options: ['companyDataIntegration' => $integration]);
        self::assertInstanceOf(Form::class, $form->get('resellerMetadata')->get($fieldName));
    }

    /**
     * @dataProvider integrationAndFormTypeDataProvider
     */
    public function testItTransformsCorrectlyDataIntegrationToForm(CompanyDataIntegration $integration, string $formType): void
    {
        $this->settings->method('getValue')->willReturn($this->getAllAvailableIntegrations());
        $companyResellerMetadataType = new CompanyResellerMetadataType($this->settings);

        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects(self::once())->method('add')->with('resellerMetadata', $formType);

        $companyResellerMetadataType->buildForm($builder, ['companyDataIntegration' => $integration]);
    }

    /**
     * @return iterable<array{CompanyDataIntegration, string}>
     */
    public function formTypeAndFieldNameProvider(): iterable
    {
        yield 'BmlEposType' => [CompanyDataIntegration::BML_EPOS, 'paymentGatewayMerchantID'];
        yield 'BmlMposType' => [CompanyDataIntegration::BML_MPOS, 'bmlMposVersion'];
        yield 'CommbankType' => [CompanyDataIntegration::COMMBANK, 'commBankTransactionMinAmount'];
        yield 'DialogType' => [CompanyDataIntegration::DIALOG, 'lankaQrCode'];
        yield 'MpgsType' => [CompanyDataIntegration::MPGS, 'unifiedPaymentForm'];
        yield 'ApmType' => [CompanyDataIntegration::_APM, 'contractId'];
        yield 'Type' => [CompanyDataIntegration::, 'valitorTerminalId'];
        yield 'TapToPayType' => [CompanyDataIntegration::TAP_TO_PAY, 'tapToPayTerminalId'];
    }

    /**
     * @return iterable<array{CompanyDataIntegration, string}>
     */
    public function integrationAndFormTypeDataProvider(): iterable
    {
        yield 'BML_EPOS' => [CompanyDataIntegration::BML_EPOS, BmlEposType::class];
        yield '' => [CompanyDataIntegration::, Type::class];
        yield '_APM' => [CompanyDataIntegration::_APM, ApmType::class];
        yield 'TAP_TO_PAY' => [CompanyDataIntegration::TAP_TO_PAY, TapToPayType::class];
        yield 'MPGS' => [CompanyDataIntegration::MPGS, MpgsType::class];
        yield 'COMMBANK' => [CompanyDataIntegration::COMMBANK, CommbankType::class];
        yield 'BML_MPOS' => [CompanyDataIntegration::BML_MPOS, BmlMposType::class];
        yield 'DIALOG' => [CompanyDataIntegration::DIALOG, DialogType::class];
        yield 'CYBERSOURCE' => [CompanyDataIntegration::CYBERSOURCE, CyberSourceType::class];
    }

    /**
     * @return PreloadedExtension[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([new CompanyResellerMetadataType($this->settings)], []),
            new PreloadedExtension([new MccType($this->settings)], []),
        ];
    }

    /**
     * @return string[]
     */
    private function getAllAvailableIntegrations(): array
    {
        return array_map(static fn (CompanyDataIntegration $integration) => $integration->value, CompanyDataIntegration::cases());
    }
}
