<?php

declare(strict_types=1);

namespace OxidSolutionCatalysts\Adyen\Tests\Unit\Core;

use OxidSolutionCatalysts\Adyen\Service\ModuleSettings;
use OxidSolutionCatalysts\Adyen\Core\RefundMailService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Which recipients the configured modes expand to. The sending itself needs a
 * shop context and is covered by the mailer in Core\Email.
 */
class RefundMailServiceTest extends TestCase
{
    public function recipientModeProvider(): array
    {
        return [
            'no mail' => [ModuleSettings::MAIL_RECIPIENT_NONE, []],
            'customer only' => [ModuleSettings::MAIL_RECIPIENT_CUSTOMER, [ModuleSettings::MAIL_RECIPIENT_CUSTOMER]],
            'owner only' => [ModuleSettings::MAIL_RECIPIENT_OWNER, [ModuleSettings::MAIL_RECIPIENT_OWNER]],
            'both, customer first' => [
                ModuleSettings::MAIL_RECIPIENT_BOTH,
                [ModuleSettings::MAIL_RECIPIENT_CUSTOMER, ModuleSettings::MAIL_RECIPIENT_OWNER],
            ],
            'unknown value is no mail' => ['7', []],
            'empty value is no mail' => ['', []],
        ];
    }

    /**
     * @dataProvider recipientModeProvider
     */
    public function testResolveRecipients(string $mode, array $expected): void
    {
        $service = new RefundMailService();
        $method = new ReflectionMethod($service, 'resolveRecipients');
        $method->setAccessible(true);

        $this->assertSame($expected, $method->invoke($service, $mode));
    }

    /**
     * The refund mail must stay silent for the cancellation context: the cancel
     * flow sends its own mail. Without a recipient nothing is delivered either,
     * so no mailer is touched in both cases.
     */
    public function testRefundMailIsSuppressedForTheCancelContext(): void
    {
        $service = $this->getMockBuilder(RefundMailService::class)
            ->onlyMethods(['deliver', 'resolveRecipients'])
            ->getMock();
        $service->expects($this->never())->method('resolveRecipients');
        $service->expects($this->never())->method('deliver');

        $service->sendRefundMail(
            oxNew(\OxidEsales\Eshop\Application\Model\Order::class),
            19.90,
            'EUR',
            ModuleSettings::REFUND_CONTEXT_CANCEL
        );
    }
}
