<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro;

use byrokrat\autogiro\{Layouts, Grammar, Tree};
use byrokrat\autogiro\Message;
use byrokrat\banking;
use byrokrat\id;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GrammarSpec extends ObjectBehavior
{
    function a_file(string $content): string
    {
        return array_reduce(
            explode("\n", $content),
            function ($carry, $line) {
                if (empty(trim($line))) {
                    return $carry;
                }
                return $carry . ltrim($line) . "\n";
            }
        );
    }

    function let(
        banking\AccountFactory $accountFactory,
        banking\BankgiroFactory $bankgiroFactory,
        id\IdFactory $personalIdFactory,
        id\IdFactory $organizationIdFactory,
        Message\MessageFactory $messageFactory,
        banking\AccountNumber $account,
        banking\Bankgiro $bankgiro,
        id\Id $id,
        Message\Message $message
    ) {
        $accountFactory->createAccount(Argument::any())->willReturn($account);
        $bankgiroFactory->createAccount(Argument::any())->willReturn($bankgiro);
        $personalIdFactory->create(Argument::any())->willReturn($id);
        $organizationIdFactory->create(Argument::any())->willReturn($id);
        $messageFactory->createMessage(Argument::any())->willReturn($message);

        $this->beConstructedWith(
            $accountFactory,
            $bankgiroFactory,
            $personalIdFactory,
            $organizationIdFactory,
            $messageFactory
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Grammar::CLASS);
    }

    function it_counts_lines()
    {
        $this->getCurrentLineCount()->shouldEqual(0);

        $this->parse(
            $this->a_file("
                01AUTOGIRO              20080611            AG-MEDAVI           4711170009912346
                092008061199000000010
            ")
        );

        $this->getCurrentLineCount()->shouldEqual(2);

        $this->resetLineCount();

        $this->getCurrentLineCount()->shouldEqual(0);
    }

    function it_parses_mandate_responses_in_the_new_format()
    {
        $this->parse(
            $this->a_file("
                01AUTOGIRO              20080611            AG-MEDAVI           4711170009912346
                73000991234600000000000001035001000001000020196803050000     043220080611
                7300099123460000000002222101                                 033320080611
                7300099123460000000000000103                                 033320080611
                73000991234600000000022221010000000000000000995556000521     460220080611
                73000991234600000000000001028901003232323232005556000521     430720080611
                73000991234600000000033310220000000000000000995556000521     042920080611
                73000991234600000000000001013300001212121212191212121212     423220080611
                7300099123460000000007771014                                 032120080611
                73000991234600000000000001048901003232323232005556000521     053220080611
                73000991234600000000000001058901003232323232005556000521     053320080611
                092008061199000000010
            ")
        )->getLayoutId()->shouldEqual(Layouts::LAYOUT_MANDATE_RESPONSE);
    }

    function it_parses_mandate_responses_in_the_old_format()
    {
        $this->parse(
            $this->a_file("
                012004011899000009912346AG-MEDAVI
                7300099123460000000000023344330012121212000019121212121200000041020041018000000
                7300099123460000000000034433890132323211100000555600052100000043220041018041026
                7300099123460000000000042233500100123560000019680305111100000043220041018041026
                7300099123460000000000052244700100000123456719460817222200000043220041018041026
                7300099123460000000000061155134800000987600019461017333300000043220041018041026
                7300099123460000000000044333600000123456777019490730444400000041020041018000000
                7300099123460000195809010000                                 033320041018000000
                092004011899000000007
            ")
        )->getLayoutId()->shouldEqual(Layouts::LAYOUT_MANDATE_RESPONSE);

        $this->parse(
            $this->a_file("
                012004110899000009912346AG-MEDAVI
                7300099123460000000008765432000000000000000099556677881100000043220041108041116
                73000991234600000000022210010000000000000000995566778812000000410
                7300099123460000000003331002000000000000000099569041666600000043220041108041116
                7300099123460000000004441003000000000000000099557012755500000043220041108041116
                7300099123460000000007771014000000000000000099809011112200000043220041108041116
                7300099123460000000005551004000000000000000099616161191100000043220041108041116
                092004110899000000006
            ")
        )->getLayoutId()->shouldEqual(Layouts::LAYOUT_MANDATE_RESPONSE);
    }
}
