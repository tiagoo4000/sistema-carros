<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .title { font-size: 18px; font-weight: 700; margin-bottom: 12px; }
        .section { margin-top: 16px; }
        .row { display: flex; justify-content: space-between; }
        .box { padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        .label { font-weight: 600; color: #555; }
        .value { margin-top: 3px; }
        .muted { color: #666; }
        .qr { margin-top: 8px; }
    </style>
    <title>Termo de Arrematação</title>
    </head>
<body>
    <div class="title">Termo de Arrematação</div>
    <div class="section box">
        <div class="label">Lote</div>
        <div class="value">#{{ $lote->id }} — {{ $lote->titulo }}</div>
        <div class="muted">Leilão #{{ $lote->leilao_id }}</div>
    </div>
    <div class="section row">
        <div class="box" style="width: 49%">
            <div class="label">Arrematante</div>
            <div class="value">{{ $termo->arrematante_nome }}</div>
            <div class="value">Documento: {{ $termo->arrematante_documento }}</div>
            @if($termo->arrematante_email)
            <div class="value">E-mail: {{ $termo->arrematante_email }}</div>
            @endif
            @if($termo->arrematante_cidade)
            <div class="value">Cidade: {{ $termo->arrematante_cidade }}</div>
            @endif
        </div>
        <div class="box" style="width: 49%">
            <div class="label">Pagamento</div>
            <div class="value">Valor de arrematação: R$ {{ number_format((float)$termo->valor_arrematacao, 2, ',', '.') }}</div>
            <div class="value">Favorecido: {{ $termo->conta_bancaria_nome }}</div>
            <div class="value">Documento: {{ $termo->conta_bancaria_documento }}</div>
            <div class="value">Banco: {{ $termo->conta_bancaria_banco }} — Agência {{ $termo->conta_bancaria_agencia }} — Conta {{ $termo->conta_bancaria_conta }}</div>
            @if($termo->conta_bancaria_pix)
            <div class="value">Chave PIX: {{ $termo->conta_bancaria_pix }}</div>
            @endif
            @if($termo->conta_bancaria_qr)
            <?php
                $qrPath = storage_path('app/'.$termo->conta_bancaria_qr);
                $qrData = file_exists($qrPath) ? base64_encode(file_get_contents($qrPath)) : null;
            ?>
            @if(!empty($qrData))
            <div class="qr">
                <img src="data:image/png;base64,{{ $qrData }}" width="140" />
            </div>
            @endif
            @endif
        </div>
    </div>
    <div class="section muted">
        Gerado em {{ now()->format('d/m/Y H:i') }}.
    </div>
</body>
</html>

