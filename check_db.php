<?php

use App\Models\Leilao;
use App\Models\Lote;
use Illuminate\Support\Facades\Schema;

echo "Checking Database...\n";

if (Schema::hasTable('leiloes')) {
    echo "Table 'leiloes' exists. Count: " . Leilao::count() . "\n";
} else {
    echo "Table 'leiloes' DOES NOT exist.\n";
}

if (Schema::hasTable('lotes')) {
    echo "Table 'lotes' exists. Count: " . Lote::count() . "\n";
} else {
    echo "Table 'lotes' DOES NOT exist.\n";
}
