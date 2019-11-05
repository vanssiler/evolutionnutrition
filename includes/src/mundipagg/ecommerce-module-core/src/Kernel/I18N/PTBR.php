<?php

namespace Mundipagg\Core\Kernel\I18N;

use Mundipagg\Core\Kernel\Abstractions\AbstractI18NTable;

class PTBR extends AbstractI18NTable
{
    protected function getTable()
    {
        return [
            'Invoice created: #%s.' => 'Invoice criada: #%s',
            'Invoice canceled: #%s.' => 'Invoice cancelada: #%s',
            'Webhook received: %s.%s' => 'Webhook recebido: %s.%s',
            'Order paid.' => 'Pedido pago.',
            'Order created at Mundipagg. Id: %s' => 'Pedido criado na Mundipagg. Id %s',
            'Order waiting for online retries at Mundipagg.' => 'Pedido aguardando por retentativas online na Mundipagg.',
            'Order canceled.' => 'Pedido cancelado.',
            'Payment received: %.2f' => 'Pagamento recebido: %.2f',
            'Canceled amount: %.2f' => 'Quantia cancelada: %.2f',
            'Refunded amount: %.2f' => 'Quantia estornada: %.2f',
            'Partial Payment' => 'Pagamento Parcial',
            'Charge canceled.' => 'Cobrança cancelada.',
            'Charge not found' => 'Cobrança não encontrada',
            'Creditmemo created: #%s.' => 'Creditmemo criado: #%s.',
            'until now' => 'até agora',
            'Extra amount paid: %.2f' => "Quantia extra paga: %.2f",
            "Order '%s' canceled at Mundipagg" => "Pedido '%s' cancelado na Mundipagg",
            'Remaining amount: %.2f' => "Quantidade faltante: %.2f",
            "Some charges couldn't be canceled at Mundipagg. Reasons:" => "Algumas cobranças não puderam ser canceladas na Mundipagg. Razões:",
            "without interest" => "sem juros",
            "with %.2f%% of interest" => "com %.2f%% de juros",
            "%dx of %s %s (Total: %s)" => "%dx de %s %s (Total: %s)",
            "Order payment failed" => "Pagamento do pedido falhou",
            "The order will be canceled" => "O pedido será cancelado",
            "An error occurred when trying to create the order. Please try again. Error Reference: %s" => 'Ocorreu um erro ao tentar criar o pedido. Por favor, tente novamente. Referência do erro: %s',
            "Can't cancel current order. Please cancel it by Mundipagg panel" => "Não foi possível cancelar o pedido. Por favor, realize o cancelamento no portal Mundipagg.",
            "Charge canceled with success" => "Charge cancelada com sucesso",
            'Invalid address. Please fill the street lines and try again.' => 'Endereço inválido. Preencha rua, número e bairro e tente novamente.',
            "The informed card couldn't be deleted." => "O cartão informado não pode ser deletado.",
            "The card '%s' was deleted." => "O cartão '%s' foi deletado.",
            "The card '%s' couldn't be deleted." => "O cartão '%s' não pôde ser deletado.",
            "Different paid amount for this invoice. Paid value: %.2f" => "Esta Invoice foi paga com um valor diferente do Grand Total do pedido. Valor pago: %.2f",
            "The %s should not be empty!" => "O campo %s não deve estar vazio",
            "street" => "rua",
            "number" => "número",
            "neighborhood" => "bairro",
            "city" => "cidade",
            "country" => "país",
            "state" => "estado",
            "document" => "CPF",
            "Can't create order." => "Não foi possível criar o pedido",
            'Invalid address configuration. Please fill the address configuration on admin panel.' => 'Configurações de endereço inválido. Preencha as configurações de endereço no painel de administração',
        ];
    }
}