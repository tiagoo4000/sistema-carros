<?php
$lote = $termo->lote ?? null;
$leilao = $termo->leilao ?? null;
$conta = $termo->conta ?? null;
$hoje = \Illuminate\Support\Carbon::now()->format('d/m/Y');
$dataLimite = $termo->data_limite_pagamento ? \Illuminate\Support\Carbon::parse($termo->data_limite_pagamento)->format('d/m/Y') : '—';
$valorArremate = number_format((float)($termo->valor_arremate ?? 0), 2, ',', '.');
$comPerc = number_format((float)($termo->comissao_percentual ?? 0), 2, ',', '.');
$comValor = number_format((float)($termo->comissao_valor ?? 0), 2, ',', '.');
$valorTotal = number_format((float)($termo->valor_total ?? 0), 2, ',', '.');
$empresaNome = config('app.name');
$empresaCnpj = null;
$empresaTelefone = null;
$empresaEndereco = null;
$empresaSite = null;
try {
    if (\Illuminate\Support\Facades\Schema::hasTable('system_configs')) {
        $empresaCnpj = \App\Models\SystemConfig::where('key','empresa_cnpj')->value('value');
        $empresaTelefone = \App\Models\SystemConfig::where('key','empresa_telefone')->value('value');
        $empresaEndereco = \App\Models\SystemConfig::where('key','empresa_endereco')->value('value');
        $empresaSite = \App\Models\SystemConfig::where('key','empresa_site')->value('value');
    }
} catch (\Throwable $e) {
    $empresaCnpj = $empresaCnpj ?? null;
    $empresaTelefone = $empresaTelefone ?? null;
    $empresaEndereco = $empresaEndereco ?? null;
    $empresaSite = $empresaSite ?? null;
}
$logo = public_path('images/logo.png');
$qrPath = public_path('qrcodes/termo-'.$termo->id.'.png');
$ident = 'TERMO-'.$termo->id;
$urlValidacao = url('/validacao/termo/'.$termo->id);
$taxasValor = 0.0;
try {
    if (\Illuminate\Support\Facades\Schema::hasTable('system_configs')) {
        $val = \App\Models\SystemConfig::where('key','taxas_valor')->value('value');
        $taxasValor = (float)($val ?? 0);
    }
} catch (\Throwable $e) {
    $taxasValor = 0.0;
}
$taxasValorFmt = number_format($taxasValor, 2, ',', '.');
$totalComTaxa = number_format(((float)($termo->valor_total ?? 0)) + $taxasValor, 2, ',', '.');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Termo de Arrematação</title>
    <style>
        @page { margin: 18mm 14mm 16mm 14mm; }
        body { font-family: "DejaVu Sans"; color: #111; font-size: 12px; line-height: 1.55; }
        .head-wrap { width: 100%; border: 1px solid #999; border-radius: 6px; }
        .head-top { text-align: center; padding: 6px 8px; border-bottom: 1px solid #999; letter-spacing: 1.2px; font-size: 12px; }
        .head-mid { padding: 8px 10px; text-align: center; }
        .head-mid .title { font-size: 18px; font-weight: 700; }
        .head-mid .meta { margin-top: 4px; color: #444; font-size: 12px; }
        .head-bot { padding: 6px 10px; border-top: 1px solid #999; }
        .head-flex { width: 100%; }
        .logo-cell { width: 22%; text-align: left; vertical-align: middle; }
        .company-cell { width: 78%; text-align: right; vertical-align: middle; }
        .company-name { font-weight: 700; font-size: 14px; }
        .company-meta { color: #555; font-size: 11px; }
        .section-title { background: #efefef; border: 1px solid #999; border-radius: 6px 6px 0 0; padding: 6px 10px; font-weight: 700; text-align: center; letter-spacing: .4px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #999; padding: 6px 8px; }
        .table th { background: #f5f5f5; font-size: 11px; }
        .table .thin td, .table .thin th { border-width: 1px; }
        .card { border: 1px solid #999; border-top: 0; border-radius: 0 0 6px 6px; padding: 8px 10px; }
        .soft { background: #f7f9fb; }
        .muted { color: #555; }
        .hl-red { color: #c1121f; font-weight: 700; }
        .qrcode { width: 110px; height: 110px; border: 1px solid #999; border-radius: 6px; text-align: center; line-height: 110px; color: #777; font-size: 11px; }
        .signs { width: 100%; margin-top: 12mm; }
        .signs td { text-align: center; vertical-align: bottom; }
        .sig-line { height: 30px; border-bottom: 1px solid #444; margin: 0 8mm; }
        .small { font-size: 11px; }
    </style>
</head>
<body>
<table class="head-wrap" cellspacing="0" cellpadding="0">
    <tr><td class="head-top"><?php echo e($ident); ?> • <?php echo e(str_pad($termo->id, 6, '0', STR_PAD_LEFT)); ?></td></tr>
    <tr>
        <td class="head-mid">
            <table class="head-flex" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="logo-cell">
                        <?php if(file_exists($logo)): ?>
                            <img src="<?php echo e($logo); ?>" alt="Logo" style="height: 48px;">
                        <?php else: ?>
                            <div class="company-name"><?php echo e($empresaNome); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="company-cell">
                        <div class="title">TERMO DE ARREMATAÇÃO</div>
                        <div class="meta">Emitido em <?php echo e($hoje); ?></div>
                        <div class="company-name" style="margin-top:4px;"><?php echo e($empresaNome); ?></div>
                        <div class="company-meta">
                            <?php if($empresaEndereco): ?> <?php echo e($empresaEndereco); ?> • <?php endif; ?>
                            <?php if($empresaTelefone): ?> <?php echo e($empresaTelefone); ?> • <?php endif; ?>
                            <?php if($empresaSite): ?> <?php echo e($empresaSite); ?> <?php endif; ?>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr><td class="head-bot small">CNPJ: <?php echo e($empresaCnpj ?: '—'); ?></td></tr>
</table>

<div class="section-title">ARREMATANTE</div>
<table class="table thin" cellspacing="0" cellpadding="0">
    <tr>
        <th width="35%">Nome</th>
        <th width="20%">CPF/CNPJ</th>
        <th width="15%">Telefone</th>
        <th width="15%">Celular</th>
        <th width="15%">E-mail</th>
    </tr>
    <tr>
        <td><?php echo e($termo->arrematante_nome); ?></td>
        <td><?php echo e($termo->arrematante_documento); ?></td>
        <td>—</td>
        <td>—</td>
        <td><?php echo e($termo->arrematante_email ?? '—'); ?></td>
    </tr>
    <tr>
        <th>Endereço</th>
        <th>Nº</th>
        <th>Bairro</th>
        <th>CEP</th>
        <th>Município/UF</th>
    </tr>
    <tr>
        <td>—</td>
        <td>—</td>
        <td>—</td>
        <td>—</td>
        <td>—</td>
    </tr>
</table>

<div class="section-title">DESCRIÇÃO DO LOTE</div>
<table class="table thin" cellspacing="0" cellpadding="0">
    <tr>
        <th>LEILÃO/LOTE</th>
        <th>DATA DO LEILÃO</th>
        <th>SITUAÇÃO</th>
    </tr>
    <tr>
        <td><?php echo e($lote?->titulo ?? '—'); ?></td>
        <td><?php echo e($hoje); ?></td>
        <td>ARREMATADO</td>
    </tr>
    <tr>
        <th>Nº do lote</th>
        <th>Valor arrematado</th>
        <th>Comissão (<?php echo e($comPerc); ?>%)</th>
    </tr>
    <tr>
        <td><?php echo e($lote?->ordem ?? $termo->lote_id); ?></td>
        <td>R$ <?php echo e($valorArremate); ?></td>
        <td>R$ <?php echo e($comValor); ?></td>
    </tr>
    <tr>
        <th>Taxa do pátio</th>
        <th>Frete</th>
        <th>Total</th>
    </tr>
    <tr>
        <td>R$ <?php echo e($taxasValorFmt); ?></td>
        <td>R$ —</td>
        <td>R$ <?php echo e($totalComTaxa); ?></td>
    </tr>
</table>

<div class="section-title">DADOS PARA PAGAMENTO</div>
<div class="card soft">
    <div><strong>Dados bancários para pagamento:</strong></div>
    <div class="small" style="margin-top:4px;">
        <div>Banco: <?php echo e($conta?->banco ?? '—'); ?></div>
        <div>Agência: <?php echo e($conta?->agencia ?? '—'); ?></div>
        <div>Conta: <?php echo e($conta?->conta ?? '—'); ?></div>
        <div>Beneficiário: <?php echo e($conta?->nome_completo ?: ($conta?->razao_social ?: '—')); ?> <?php if($conta?->cpf || $conta?->cnpj): ?> — <?php echo e($conta?->cpf ?: $conta?->cnpj); ?> <?php endif; ?></div>
        <?php if($conta?->chave_pix): ?><div>Chave PIX: <?php echo e($conta->chave_pix); ?></div><?php endif; ?>
    </div>
    <div style="margin-top:6px;">Valor total do pagamento: <strong>R$ <?php echo e($totalComTaxa); ?></strong></div>
    <div>Data limite para pagamento: <strong><?php echo e($dataLimite); ?></strong> até as 18h.</div>
    <div class="hl-red" style="margin-top:6px;">O PAGAMENTO SOMENTE TERÁ VALIDADE MEDIANTE AO COMPROVANTE BANCÁRIO.</div>
</div>

<div class="section-title">REVISÃO DE LEILOEIRO(A) NA JUNTA COMERCIAL</div>
<div class="card">
    <div class="muted">
        Este documento atesta a arrematação realizada conforme edital e legislação. O pagamento deverá ser efetuado na conta bancária indicada e somente terá validade mediante comprovação. A verificação de autenticidade deste termo pode ser realizada pelo endereço indicado abaixo ou via QR Code.
    </div>
</div>

<table class="table" cellspacing="0" cellpadding="0" style="margin-top:6mm;">
    <tr>
        <td width="30%" style="text-align:center; vertical-align:middle;">
            <?php if(file_exists($qrPath)): ?>
                <img src="<?php echo e($qrPath); ?>" alt="QR" style="width: 110px; height: 110px;">
            <?php else: ?>
                <div class="qrcode">QR</div>
            <?php endif; ?>
        </td>
        <td width="70%" style="vertical-align:middle;">
            <div class="small"><strong>Validação:</strong> <?php echo e($urlValidacao); ?></div>
            <div class="muted small">Use o QR Code para validar a autenticidade deste termo.</div>
        </td>
    </tr>
</table>

<table class="signs" cellspacing="0" cellpadding="0">
    <tr>
        <td width="33%"><div class="sig-line"></div><div class="small">Arrematante</div></td>
        <td width="33%"><div class="sig-line"></div><div class="small"><?php echo e($empresaNome); ?></div></td>
        <td width="33%"><div class="sig-line"></div><div class="small">Testemunha</div></td>
    </tr>
</table>
</body>
</html>
<?php /**PATH C:\sistema-casa-novo\resources\views/pdf/termo_arrematacao.blade.php ENDPATH**/ ?>