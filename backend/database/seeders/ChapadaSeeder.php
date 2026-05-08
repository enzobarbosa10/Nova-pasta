<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChapadaSeeder extends Seeder
{
    public function run(): void
    {
        // Expeditions
        $expeditions = [
            [
                'id' => Str::uuid(),
                'name' => 'Vale do Pati - Expedição Completa',
                'destination' => 'Vale do Pati, Chapada Diamantina - BA',
                'dates' => '15/07/2025 - 22/07/2025',
                'capacity' => 12,
                'remaining_spots' => 5,
                'trail_level' => 'HARD',
                'status' => 'OPEN',
                'accommodation' => 'Pousadas locais e camping',
                'transport' => 'Van fretada saindo de Salvador',
                'costs' => 1300.00,
                'margin_predicted' => 35.5,
                'margin_real' => null,
                'participants' => json_encode([]),
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Trilha da Fumaça - Cachoeira Mais Alta do Brasil',
                'destination' => 'Cachoeira da Fumaça, Lençóis - BA',
                'dates' => '01/08/2025 - 05/08/2025',
                'capacity' => 15,
                'remaining_spots' => 3,
                'trail_level' => 'MODERATE',
                'status' => 'GUARANTEED',
                'accommodation' => 'Pousada Lençóis Centro',
                'transport' => 'Ônibus + transfer local',
                'costs' => 950.00,
                'margin_predicted' => 40.0,
                'margin_real' => null,
                'participants' => json_encode([]),
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Poço Encantado & Poço Azul - Maravilhas Subterrâneas',
                'destination' => 'Iraquara & Andaraí, Chapada Diamantina - BA',
                'dates' => '10/09/2025 - 13/09/2025',
                'capacity' => 10,
                'remaining_spots' => 7,
                'trail_level' => 'EASY',
                'status' => 'OPEN',
                'accommodation' => 'Pousada Eco Chapada',
                'transport' => 'Van particular',
                'costs' => 1010.00,
                'margin_predicted' => 38.0,
                'margin_real' => null,
                'participants' => json_encode([]),
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Morro do Pai Inácio ao Nascer do Sol',
                'destination' => 'Palmeiras, Chapada Diamantina - BA',
                'dates' => '20/06/2025 - 23/06/2025',
                'capacity' => 20,
                'remaining_spots' => 0,
                'trail_level' => 'MODERATE',
                'status' => 'COMPLETED',
                'accommodation' => 'Hotel fazenda local',
                'transport' => 'Carro próprio + guia',
                'costs' => 770.00,
                'margin_predicted' => 32.0,
                'margin_real' => 29.5,
                'participants' => json_encode([]),
                'created_at' => now()->subDays(45),
                'updated_at' => now()->subDays(5),
            ],
        ];

        foreach ($expeditions as $exp) {
            DB::table('expeditions')->updateOrInsert(['id' => $exp['id']], $exp);
        }

        // Leads
        $leads = [
            [
                'id' => Str::uuid(),
                'name' => 'Ana Clara Souza',
                'whatsapp' => '71991234567',
                'instagram' => '@anaclara.viagem',
                'source' => 'Instagram',
                'interest' => 'Trekking e cachoeiras',
                'destination' => 'Vale do Pati',
                'date_desired' => '2025-07-15',
                'people_count' => 2,
                'estimated_ticket' => 3800.00,
                'status' => 'PROPOSAL',
                'notes' => 'Quer o pacote completo com fotografia',
                'last_contact' => now()->subDays(2)->toDateString(),
                'next_follow_up' => now()->addDays(1)->toDateString(),
                'tags' => json_encode(['premium', 'aventura']),
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(2),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Rafael Mendes',
                'whatsapp' => '11987654321',
                'instagram' => '@rafaeltrip',
                'source' => 'Indicação',
                'interest' => 'Família com crianças',
                'destination' => 'Poço Azul',
                'date_desired' => '2025-09-10',
                'people_count' => 4,
                'estimated_ticket' => 6200.00,
                'status' => 'QUALIFIED',
                'notes' => 'Grupo familiar, prefere trilhas leves',
                'last_contact' => now()->subDays(3)->toDateString(),
                'next_follow_up' => now()->addDays(2)->toDateString(),
                'tags' => json_encode(['família', 'leve']),
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(3),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Fernanda Lima',
                'whatsapp' => '21998887766',
                'instagram' => '@fefechapada',
                'source' => 'Google',
                'interest' => 'Trilha da Fumaça',
                'destination' => 'Lençóis',
                'date_desired' => '2025-08-01',
                'people_count' => 3,
                'estimated_ticket' => 4500.00,
                'status' => 'RESERVED',
                'notes' => 'Pagou sinal de 30%, restante em junho',
                'last_contact' => now()->subDays(1)->toDateString(),
                'next_follow_up' => now()->addDays(7)->toDateString(),
                'tags' => json_encode(['reservado', 'sinal-pago']),
                'created_at' => now()->subDays(18),
                'updated_at' => now()->subDays(1),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Carlos Drummond',
                'whatsapp' => '31996543210',
                'instagram' => null,
                'source' => 'Site',
                'interest' => 'Ecoturismo geral',
                'destination' => 'Chapada Diamantina',
                'date_desired' => '2025-07-20',
                'people_count' => 2,
                'estimated_ticket' => 2800.00,
                'status' => 'NEW',
                'notes' => 'Primeiro contato pelo formulário do site',
                'last_contact' => now()->toDateString(),
                'next_follow_up' => now()->toDateString(),
                'tags' => json_encode(['novo']),
                'created_at' => now()->subHours(6),
                'updated_at' => now()->subHours(6),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Juliana Rocha',
                'whatsapp' => '85994561234',
                'instagram' => '@julianarocha_nature',
                'source' => 'Instagram',
                'interest' => 'Fotografia de natureza',
                'destination' => 'Vale do Pati',
                'date_desired' => '2025-07-15',
                'people_count' => 1,
                'estimated_ticket' => 2200.00,
                'status' => 'PAID',
                'notes' => 'Pagamento integral confirmado',
                'last_contact' => now()->subDays(5)->toDateString(),
                'next_follow_up' => now()->addDays(30)->toDateString(),
                'tags' => json_encode(['pago', 'fotógrafa']),
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(5),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Marcos Vinícius',
                'whatsapp' => '62993210987',
                'instagram' => '@mv_aventuras',
                'source' => 'WhatsApp',
                'interest' => 'Aventura extrema',
                'destination' => 'Morro do Pai Inácio',
                'date_desired' => '2025-10-05',
                'people_count' => 6,
                'estimated_ticket' => 8400.00,
                'status' => 'CONTACTED',
                'notes' => 'Grupo de amigos, quer pacote exclusivo',
                'last_contact' => now()->subDays(1)->toDateString(),
                'next_follow_up' => now()->addDays(3)->toDateString(),
                'tags' => json_encode(['grupo', 'exclusivo']),
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(1),
            ],
        ];

        foreach ($leads as $lead) {
            DB::table('leads')->updateOrInsert(['id' => $lead['id']], $lead);
        }

        // Checklist items
        $checklistItems = [
            ['id' => Str::uuid(), 'task' => 'Confirmar transporte Vale do Pati', 'category' => 'PRE', 'status' => 'PENDING', 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'task' => 'Enviar contrato para Fernanda Lima', 'category' => 'PRE', 'status' => 'PENDING', 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'task' => 'Reservar pousadas em Lençóis agosto', 'category' => 'PRE', 'status' => 'PENDING', 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'task' => 'Atualizar seguro viagem grupos julho', 'category' => 'PRE', 'status' => 'DONE', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($checklistItems as $item) {
            DB::table('checklist_items')->insert($item);
        }
    }
}
