<?php

namespace App\Services;

use App\Models\FakeUsuario;

class FakeUserService
{
    protected array $nomes = [
    'João','Maria','Pedro','Ana','Lucas','Julia','Gabriel','Bruno','Rafael','Carlos','Paulo','Felipe',
    'Gustavo','Andre','Diego','Rodrigo','Thiago','Leonardo','Matheus','Mariana','Larissa','Camila',
    'Beatriz','Aline','Priscila','Fernanda','Luana','Patricia','Daniel','Eduardo','Henrique','Caio',
    'Vitor','Renato','Sergio','Marcelo','Roberto','Fabio','Mauricio','Igor','Alex','Vinicius',
    'Jean','Leandro','Samuel','Adriano','Cristiano','Wesley','Douglas','Jonathan','Alisson','Otavio',
    'Brenda','Carolina','Tatiane','Vanessa','Simone','Bianca','Isabela','Nathalia','Jessica','Amanda',
    'Kelly','Leticia','Elaine','Rafaela','Debora','Sheila','Monica','Sabrina','Daiane','Paula',

    'Fernando','Ricardo','Cesar','Claudio','Emanuel','Hugo','Ivan','Jorge','Kleber','Luis',
    'Mateus','Nathan','Orlando','Pablo','Renan','Saulo','Tales','Ubiratan','Valdir','William',
    'Yuri','Zeca','Alberto','Breno','Caetano','Davi','Elton','Filipe','Gilberto','Heitor',
    'Icaro','Juliano','Kaique','Luan','Murilo','Nicolas','Oscar','Patrick','Ramon','Silvio',
    'Tulio','Vicente','Washington','Yago','Zildo',

    'Adriana','Bruna','Cintia','Denise','Erika','Flavia','Giovana','Helena','Ivone','Janaina',
    'Katia','Lidiane','Mirela','Neide','Olivia','Pamela','Quiteria','Rosana','Sandra','Talita',
    'Ursula','Valeria','Wanda','Yasmin','Zuleica','Alana','Bárbara','Clarice','Dandara','Estela',
    'Fabiana','Grazi','Heloisa','Ingrid','Joice','Karina','Lorena','Madalena','Nubia','Paloma',
    'Raquel','Solange','Tamires','Viviane','Zenaide',

    'Enzo','Theo','Breno','Gael','Noah','Benjamin','Henry','Levi','Anthony','Bryan',
    'Ryan','Kevin','Jordan','Cauã','Miguel','Arthur','Bernardo','Lorenzo','Henzo','Pietro',
    'Augusto','Bento','Danilo','Eder','Fabricio','Geraldo','Haroldo','Italo','Jonas','Kauê',
    'Lauro','Marcos','Natan','Olavo','Pedro Henrique','Rafael Silva','Tiago Alves','Victor Hugo','Willian Souza','Zé Roberto',

    'Ana Clara','Ana Luiza','Ana Beatriz','Maria Clara','Maria Eduarda','Maria Julia','Maria Fernanda',
    'Laura','Alice','Sophia','Valentina','Manuela','Livia','Melissa','Cecilia','Evelyn','Gabriela',
    'Hadassa','Isis','Jade','Kiara','Lais','Milena','Nina','Olga','Petra','Quezia','Rebeca',
    'Sarah','Tereza','Vitoria','Yohana','Zara'
    ];

    protected array $ufs = ['AC','AL','AM','AP','BA','CE','DF','ES','GO','MA','MG','MS','MT','PA','PB','PE','PI','PR','RJ','RN','RO','RR','RS','SC','SE','SP','TO'];

    protected array $autos = [
    'Turbo','16V','Sport','Racing','GT','Sprint','Acelerado','Veloz','Rally','Torque','Drift','Rapido',
    'Flex','Classic','Prime','Power','Street','Track','City','Road','Runner',

    'Rebaixado','Socado','Top','Completo','Filé','Liso','Zerado','Inteiro','Novo','Conservado',
    'Brabo','Monstro','Nave','Maquina','Canhao','Foguete','Ignorante','Pancada','Pesado','Diferenciado',

    'So no grau','Na fixa','Na pista','De garagem','Chave na mao','Pegar e andar','Sem detalhe','Impecavel','Relikia','Mosca branca',
    'Boyzinho','Maloka','Raiz','Estilo','De patrão','De respeito','Lacrado','Topzera','Bonito','Alinhado',

    'Baixo','Alto','Original','Envelopado','Roda grande','Som forte','Turbozinho','Aspirado','Economico','Valente',
    'Guerreiro','Dia a dia','De passeio','Estradeiro','Da firma','Do corre','Blindado','De lei','Na moral','So alegria'
    ];

    public function ensurePool(int $size = 200): void
    {
        $current = FakeUsuario::count();
        $needed = max(0, $size - $current);
        if ($needed === 0) {
            return;
        }
        $generated = [];
        while ($needed > 0) {
            $apelido = $this->generateNickname();
            if (isset($generated[$apelido])) continue;
            if (FakeUsuario::where('apelido', $apelido)->exists()) continue;
            $uf = $this->ufs[array_rand($this->ufs)];
            FakeUsuario::create([
                'apelido' => $apelido,
                'estado' => $uf,
                'tag' => null,
            ]);
            $generated[$apelido] = true;
            $needed--;
        }
    }

    public function pickRandom(): FakeUsuario
    {
        $exists = FakeUsuario::inRandomOrder()->first();
        if ($exists) return $exists;
        $this->ensurePool(200);
        return FakeUsuario::inRandomOrder()->firstOrFail();
    }

    public function pickForLote(int $loteId): FakeUsuario
    {
        $recentNames = \DB::table('lances')
            ->where('lote_id', $loteId)
            ->where('is_fake', true)
            ->orderByDesc('id')
            ->limit(3)
            ->pluck('nome_exibicao')
            ->filter()
            ->all();

        for ($i = 0; $i < 5; $i++) {
            $user = $this->pickRandom();
            if (!in_array($user->apelido, $recentNames, true)) {
                return $user;
            }
        }
        return $this->pickRandom();
    }

    public function pickForLoteAvoiding(int $loteId, array $exclude): FakeUsuario
    {
        $candidate = FakeUsuario::whereNotIn('apelido', $exclude)->inRandomOrder()->first();
        if ($candidate) {
            return $candidate;
        }
        return $this->pickForLote($loteId);
    }

    protected function generateNickname(): string
    {
        $nome = $this->nomes[array_rand($this->nomes)];
        $auto = $this->autos[array_rand($this->autos)];

        if (mt_rand(1, 100) <= 20) {
            $uf = $this->ufs[array_rand($this->ufs)];
            return "{$nome} {$auto} {$uf}";
        }
        return "{$nome} {$auto}";
    }

    public function generateNameForLance(int $loteId, int $globalWindow = 15, int $maxPorLote = 3): string
    {
        $loteCounts = \DB::table('lances')
            ->select('nome_exibicao', \DB::raw('COUNT(*) as total'))
            ->where('lote_id', $loteId)
            ->where('is_fake', true)
            ->groupBy('nome_exibicao')
            ->pluck('total', 'nome_exibicao')
            ->toArray();

        $recentGlobal = \DB::table('lances')
            ->where('is_fake', true)
            ->orderByDesc('id')
            ->limit($globalWindow)
            ->pluck('nome_exibicao')
            ->filter()
            ->values()
            ->all();
        $recentSet = array_flip($recentGlobal);

        for ($i = 0; $i < 60; $i++) {
            $nome = $this->generateNickname();
            if (($loteCounts[$nome] ?? 0) >= $maxPorLote) continue;
            if (isset($recentSet[$nome])) continue;
            return $nome;
        }

        for ($i = 0; $i < 60; $i++) {
            $nome = $this->generateNickname();
            if (($loteCounts[$nome] ?? 0) >= $maxPorLote) continue;
            return $nome;
        }

        return $this->generateNickname();
    }
}
