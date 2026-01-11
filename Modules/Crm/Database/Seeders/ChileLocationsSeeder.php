<?php

namespace Modules\Crm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChileLocationsSeeder extends Seeder
{
    public function run(): void
    {
        // Primero, insertamos Chile como país
        $chileId = DB::table('locations')->insertGetId([
            'name'          => 'Chile',
            'type'          => 'country',
            'country_code'  => 'CL',
            'parent_id'     => null,
            'level'         => 0,
            'code'          => 'CL',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // Lista de regiones, provincias y comunas de Chile
        $chileStructure = [
            [
                "name" => "Arica y Parinacota",
                "type" => "region",
                "code" => "XV",
                "provinces" => [
                    ["name" => "Arica", "code" => "ARI", "communes" => ["Arica", "Camarones"]],
                    ["name" => "Parinacota", "code" => "PAR", "communes" => ["Putre", "General Lagos"]],
                ]
            ],
            [
                "name" => "Tarapacá",
                "type" => "region",
                "code" => "I",
                "provinces" => [
                    ["name" => "Iquique", "code" => "IQQ", "communes" => ["Iquique", "Alto Hospicio"]],
                    ["name" => "Tamarugal", "code" => "TAM", "communes" => ["Pozo Almonte", "Camiña", "Colchane", "Huara", "Pica"]],
                ]
            ],
            [
                "name" => "Antofagasta",
                "type" => "region",
                "code" => "II",
                "provinces" => [
                    ["name" => "Antofagasta", "code" => "ANT", "communes" => ["Antofagasta", "Mejillones", "Sierra Gorda", "Taltal"]],
                    ["name" => "El Loa", "code" => "LOA", "communes" => ["Calama", "Ollagüe", "San Pedro de Atacama"]],
                    ["name" => "Tocopilla", "code" => "TOC", "communes" => ["Tocopilla", "María Elena"]],
                ]
            ],
            [
                "name" => "Atacama",
                "type" => "region",
                "code" => "III",
                "provinces" => [
                    ["name" => "Copiapó", "code" => "COP", "communes" => ["Copiapó", "Caldera", "Tierra Amarilla"]],
                    ["name" => "Huasco", "code" => "HUA", "communes" => ["Vallenar", "Alto del Carmen", "Freirina", "Huasco"]],
                    ["name" => "Chañaral", "code" => "CHA", "communes" => ["Chañaral", "Diego de Almagro"]],
                ]
            ],
            [
                "name" => "Coquimbo",
                "type" => "region",
                "code" => "IV",
                "provinces" => [
                    ["name" => "Elqui", "code" => "ELQ", "communes" => ["La Serena", "Coquimbo", "Andacollo", "La Higuera", "Paihuano", "Vicuña"]],
                    ["name" => "Limarí", "code" => "LIM", "communes" => ["Ovalle", "Combarbalá", "Monte Patria", "Punitaqui", "Río Hurtado"]],
                    ["name" => "Choapa", "code" => "CHO", "communes" => ["Illapel", "Canela", "Los Vilos", "Salamanca"]],
                ]
            ],
            [
                "name" => "Valparaíso",
                "type" => "region",
                "code" => "V",
                "provinces" => [
                    ["name" => "Valparaíso", "code" => "VAL", "communes" => ["Valparaíso", "Casablanca", "Concón", "Juan Fernández", "Puchuncaví", "Quintero", "Viña del Mar"]],
                    ["name" => "Isla de Pascua", "code" => "ISP", "communes" => ["Isla de Pascua"]],
                    ["name" => "Los Andes", "code" => "LDA", "communes" => ["Los Andes", "Calle Larga", "Rinconada", "San Esteban"]],
                    ["name" => "Petorca", "code" => "PET", "communes" => ["La Ligua", "Cabildo", "Papudo", "Petorca", "Zapallar"]],
                    ["name" => "Quillota", "code" => "QUI", "communes" => ["Quillota", "La Calera", "Hijuelas", "La Cruz", "Nogales"]],
                    ["name" => "San Antonio", "code" => "SAA", "communes" => ["San Antonio", "Algarrobo", "Cartagena", "El Quisco", "El Tabo", "Santo Domingo"]],
                    ["name" => "San Felipe de Aconcagua", "code" => "SFA", "communes" => ["San Felipe", "Catemu", "Llaillay", "Panquehue", "Putaendo", "Santa María"]],
                    ["name" => "Marga Marga", "code" => "MMA", "communes" => ["Quilpué", "Limache", "Olmué", "Villa Alemana"]],
                ]
            ],
            [
                "name" => "Metropolitana de Santiago",
                "type" => "region",
                "code" => "RM",
                "provinces" => [
                    ["name" => "Santiago", "code" => "STG", "communes" => [
                        "Santiago", "Cerrillos", "Cerro Navia", "Conchalí", "El Bosque", "Estación Central", "Huechuraba", "Independencia", "La Cisterna", "La Florida", "La Granja", "La Pintana", "La Reina", "Las Condes", "Lo Barnechea", "Lo Espejo", "Lo Prado", "Macul", "Maipú", "Ñuñoa", "Pedro Aguirre Cerda", "Peñalolén", "Providencia", "Pudahuel", "Quilicura", "Quinta Normal", "Recoleta", "Renca", "San Joaquín", "San Miguel", "San Ramón", "Vitacura"
                    ]],
                    ["name" => "Cordillera", "code" => "COR", "communes" => ["Puente Alto", "Pirque", "San José de Maipo"]],
                    ["name" => "Chacabuco", "code" => "CHB", "communes" => ["Colina", "Lampa", "Tiltil"]],
                    ["name" => "Maipo", "code" => "MAI", "communes" => ["San Bernardo", "Buin", "Calera de Tango", "Paine"]],
                    ["name" => "Melipilla", "code" => "MEL", "communes" => ["Melipilla", "Alhué", "Curacaví", "María Pinto", "San Pedro"]],
                    ["name" => "Talagante", "code" => "TLG", "communes" => ["Talagante", "El Monte", "Isla de Maipo", "Padre Hurtado", "Peñaflor"]],
                ]
            ],
            [
                "name" => "Libertador General Bernardo O’Higgins",
                "type" => "region",
                "code" => "VI",
                "provinces" => [
                    ["name" => "Cachapoal", "code" => "CAP", "communes" => ["Rancagua", "Codegua", "Coinco", "Coltauco", "Doñihue", "Graneros", "Las Cabras", "Machalí", "Malloa", "Mostazal", "Olivar", "Peumo", "Pichidegua", "Quinta de Tilcoco", "Rengo", "Requínoa", "San Vicente"]],
                    ["name" => "Colchagua", "code" => "COL", "communes" => ["San Fernando", "Chépica", "Chimbarongo", "Lolol", "Nancagua", "Palmilla", "Peralillo", "Placilla", "Pumanque", "Santa Cruz"]],
                    ["name" => "Cardenal Caro", "code" => "CCR", "communes" => ["Pichilemu", "La Estrella", "Litueche", "Marchihue", "Navidad", "Paredones"]],
                ]
            ],
            [
                "name" => "Maule",
                "type" => "region",
                "code" => "VII",
                "provinces" => [
                    ["name" => "Talca", "code" => "TLC", "communes" => ["Talca", "Constitución", "Curepto", "Empedrado", "Maule", "Pelarco", "Pencahue", "Río Claro", "San Clemente", "San Rafael"]],
                    ["name" => "Cauquenes", "code" => "CAU", "communes" => ["Cauquenes", "Chanco", "Pelluhue"]],
                    ["name" => "Curicó", "code" => "CUR", "communes" => ["Curicó", "Hualañé", "Licantén", "Molina", "Rauco", "Romeral", "Sagrada Familia", "Teno", "Vichuquén"]],
                    ["name" => "Linares", "code" => "LNR", "communes" => ["Linares", "Colbún", "Longaví", "Parral", "Retiro", "San Javier", "Villa Alegre", "Yerbas Buenas"]],
                ]
            ],
            [
                "name" => "Ñuble",
                "type" => "region",
                "code" => "XVI",
                "provinces" => [
                    ["name" => "Diguillín", "code" => "DIG", "communes" => ["Bulnes", "Chillán", "Chillán Viejo", "El Carmen", "Pemuco", "Pinto", "Quillón", "San Ignacio", "Yungay"]],
                    ["name" => "Punilla", "code" => "PUN", "communes" => ["Coihueco", "Ñiquén", "San Carlos", "San Fabián", "San Nicolás"]],
                    ["name" => "Itata", "code" => "ITA", "communes" => ["Cobquecura", "Coelemu", "Ninhue", "Portezuelo", "Quirihue", "Ránquil", "Treguaco"]],
                ]
            ],
            [
                "name" => "Biobío",
                "type" => "region",
                "code" => "VIII",
                "provinces" => [
                    ["name" => "Concepción", "code" => "CON", "communes" => ["Concepción", "Coronel", "Chiguayante", "Florida", "Hualpén", "Hualqui", "Lota", "Penco", "San Pedro de la Paz", "Santa Juana", "Talcahuano", "Tomé"]],
                    ["name" => "Arauco", "code" => "ARU", "communes" => ["Lebu", "Arauco", "Cañete", "Contulmo", "Curanilahue", "Los Álamos", "Tirúa"]],
                    ["name" => "Biobío", "code" => "BIO", "communes" => ["Los Ángeles", "Antuco", "Cabrero", "Laja", "Mulchén", "Nacimiento", "Negrete", "Quilaco", "Quilleco", "San Rosendo", "Santa Bárbara", "Tucapel", "Yumbel"]],
                ]
            ],
            [
                "name" => "La Araucanía",
                "type" => "region",
                "code" => "IX",
                "provinces" => [
                    ["name" => "Cautín", "code" => "CAU", "communes" => ["Temuco", "Carahue", "Cunco", "Curarrehue", "Freire", "Galvarino", "Gorbea", "Lautaro", "Loncoche", "Melipeuco", "Nueva Imperial", "Padre Las Casas", "Perquenco", "Pitrufquén", "Pucón", "Saavedra", "Teodoro Schmidt", "Toltén", "Vilcún", "Villarrica", "Cholchol"]],
                    ["name" => "Malleco", "code" => "MLC", "communes" => ["Angol", "Collipulli", "Curacautín", "Ercilla", "Lonquimay", "Los Sauces", "Lumaco", "Purén", "Renaico", "Traiguén", "Victoria"]],
                ]
            ],
            [
                "name" => "Los Ríos",
                "type" => "region",
                "code" => "XIV",
                "provinces" => [
                    ["name" => "Valdivia", "code" => "VDI", "communes" => ["Valdivia", "Corral", "Lanco", "Los Lagos", "Máfil", "Mariquina", "Paillaco", "Panguipulli"]],
                    ["name" => "Ranco", "code" => "RCO", "communes" => ["La Unión", "Futrono", "Lago Ranco", "Río Bueno"]],
                ]
            ],
            [
                "name" => "Los Lagos",
                "type" => "region",
                "code" => "X",
                "provinces" => [
                    ["name" => "Llanquihue", "code" => "LLQ", "communes" => ["Puerto Montt", "Calbuco", "Cochamó", "Fresia", "Frutillar", "Los Muermos", "Llanquihue", "Maullín", "Puerto Varas"]],
                    ["name" => "Chiloé", "code" => "CHL", "communes" => ["Castro", "Ancud", "Chaitén", "Chonchi", "Curaco de Vélez", "Dalcahue", "Puqueldón", "Queilén", "Quellón", "Quemchi", "Queilen", "Quinchao"]],
                    ["name" => "Osorno", "code" => "OSO", "communes" => ["Osorno", "Puerto Octay", "Purranque", "Puyehue", "Río Negro", "San Juan de la Costa", "San Pablo"]],
                    ["name" => "Palena", "code" => "PAL", "communes" => ["Chaitén", "Futaleufú", "Hualaihué", "Palena"]],
                ]
            ],
            [
                "name" => "Aysén del General Carlos Ibáñez del Campo",
                "type" => "region",
                "code" => "XI",
                "provinces" => [
                    ["name" => "Coyhaique", "code" => "CYQ", "communes" => ["Coyhaique", "Lago Verde"]],
                    ["name" => "Aysén", "code" => "AI", "communes" => ["Aysén", "Cisnes", "Guaitecas"]],
                    ["name" => "Capitán Prat", "code" => "CPT", "communes" => ["Cochrane", "O’Higgins", "Tortel"]],
                    ["name" => "General Carrera", "code" => "GCR", "communes" => ["Chile Chico", "Río Ibáñez"]],
                ]
            ],
            [
                "name" => "Magallanes y de la Antártica Chilena",
                "type" => "region",
                "code" => "XII",
                "provinces" => [
                    ["name" => "Magallanes", "code" => "MAG", "communes" => ["Punta Arenas", "Laguna Blanca", "Río Verde", "San Gregorio"]],
                    ["name" => "Antártica Chilena", "code" => "ANT", "communes" => ["Cabo de Hornos", "Antártica"]],
                    ["name" => "Tierra del Fuego", "code" => "TF", "communes" => ["Porvenir", "Primavera", "Timaukel"]],
                    ["name" => "Última Esperanza", "code" => "UE", "communes" => ["Natales", "Torres del Paine"]],
                ]
            ],
        ];

        // Insertar regiones, provincias y comunas
        foreach ($chileStructure as $region) {
            $regionId = DB::table('locations')->insertGetId([
                'name'          => $region['name'],
                'type'          => $region['type'],
                'country_code'  => 'CL',
                'parent_id'     => $chileId,
                'level'         => 1,
                'code'          => $region['code'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            foreach ($region['provinces'] as $province) {
                $provinceId = DB::table('locations')->insertGetId([
                    'name'          => $province['name'],
                    'type'          => 'province',
                    'country_code'  => 'CL',
                    'parent_id'     => $regionId,
                    'level'         => 2,
                    'code'          => $province['code'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                foreach ($province['communes'] as $commune) {
                    DB::table('locations')->insert([
                        'name'          => $commune,
                        'type'          => 'municipality',
                        'country_code'  => 'CL',
                        'parent_id'     => $provinceId,
                        'level'         => 3,
                        'code'          => null,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }
        }
    }
}
