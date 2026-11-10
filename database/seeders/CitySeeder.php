<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            // Amazonas
            ['department' => 'Amazonas', 'name' => 'Leticia'],
            ['department' => 'Amazonas', 'name' => 'Puerto Nariño'],
            // Antioquia
            ['department' => 'Antioquia', 'name' => 'Medellín'],
            ['department' => 'Antioquia', 'name' => 'Bello'],
            ['department' => 'Antioquia', 'name' => 'Itagüí'],
            ['department' => 'Antioquia', 'name' => 'Envigado'],
            ['department' => 'Antioquia', 'name' => 'Apartadó'],
            ['department' => 'Antioquia', 'name' => 'Rionegro'],
            ['department' => 'Antioquia', 'name' => 'Turbo'],
            ['department' => 'Antioquia', 'name' => 'Caucasia'],
            ['department' => 'Antioquia', 'name' => 'Montería'],
            ['department' => 'Antioquia', 'name' => 'Sabaneta'],
            ['department' => 'Antioquia', 'name' => 'Copacabana'],
            ['department' => 'Antioquia', 'name' => 'La Estrella'],
            ['department' => 'Antioquia', 'name' => 'Girardota'],
            ['department' => 'Antioquia', 'name' => 'Barbosa'],
            ['department' => 'Antioquia', 'name' => 'Caldas'],
            ['department' => 'Antioquia', 'name' => 'Andes'],
            ['department' => 'Antioquia', 'name' => 'Yarumal'],
            ['department' => 'Antioquia', 'name' => 'Marinilla'],
            ['department' => 'Antioquia', 'name' => 'El Carmen de Viboral'],
            ['department' => 'Antioquia', 'name' => 'Sonsón'],
            ['department' => 'Antioquia', 'name' => 'Santa Fe de Antioquia'],
            ['department' => 'Antioquia', 'name' => 'Urrao'],
            // Arauca
            ['department' => 'Arauca', 'name' => 'Arauca'],
            ['department' => 'Arauca', 'name' => 'Saravena'],
            ['department' => 'Arauca', 'name' => 'Tame'],
            ['department' => 'Arauca', 'name' => 'Arauquita'],
            ['department' => 'Arauca', 'name' => 'Fortul'],
            // Atlántico
            ['department' => 'Atlántico', 'name' => 'Barranquilla'],
            ['department' => 'Atlántico', 'name' => 'Soledad'],
            ['department' => 'Atlántico', 'name' => 'Malambo'],
            ['department' => 'Atlántico', 'name' => 'Sabanalarga'],
            ['department' => 'Atlántico', 'name' => 'Baranoa'],
            ['department' => 'Atlántico', 'name' => 'Puerto Colombia'],
            ['department' => 'Atlántico', 'name' => 'Galapa'],
            ['department' => 'Atlántico', 'name' => 'Santo Tomás'],
            // Bogotá D.C.
            ['department' => 'Bogotá D.C.', 'name' => 'Bogotá D.C.'],
            // Bolívar
            ['department' => 'Bolívar', 'name' => 'Cartagena'],
            ['department' => 'Bolívar', 'name' => 'Magangué'],
            ['department' => 'Bolívar', 'name' => 'Turbaco'],
            ['department' => 'Bolívar', 'name' => 'El Carmen de Bolívar'],
            ['department' => 'Bolívar', 'name' => 'Mompox'],
            ['department' => 'Bolívar', 'name' => 'Arjona'],
            // Boyacá
            ['department' => 'Boyacá', 'name' => 'Tunja'],
            ['department' => 'Boyacá', 'name' => 'Duitama'],
            ['department' => 'Boyacá', 'name' => 'Sogamoso'],
            ['department' => 'Boyacá', 'name' => 'Chiquinquirá'],
            ['department' => 'Boyacá', 'name' => 'Puerto Boyacá'],
            ['department' => 'Boyacá', 'name' => 'Paipa'],
            ['department' => 'Boyacá', 'name' => 'Ramiriquí'],
            ['department' => 'Boyacá', 'name' => 'Villa de Leyva'],
            // Caldas
            ['department' => 'Caldas', 'name' => 'Manizales'],
            ['department' => 'Caldas', 'name' => 'La Dorada'],
            ['department' => 'Caldas', 'name' => 'Riosucio'],
            ['department' => 'Caldas', 'name' => 'Chinchiná'],
            ['department' => 'Caldas', 'name' => 'Villamaría'],
            ['department' => 'Caldas', 'name' => 'Anserma'],
            ['department' => 'Caldas', 'name' => 'Salamina'],
            // Caquetá
            ['department' => 'Caquetá', 'name' => 'Florencia'],
            ['department' => 'Caquetá', 'name' => 'San Vicente del Caguán'],
            ['department' => 'Caquetá', 'name' => 'Belén de los Andaquíes'],
            ['department' => 'Caquetá', 'name' => 'Puerto Rico'],
            // Casanare
            ['department' => 'Casanare', 'name' => 'Yopal'],
            ['department' => 'Casanare', 'name' => 'Aguazul'],
            ['department' => 'Casanare', 'name' => 'Villanueva'],
            ['department' => 'Casanare', 'name' => 'Paz de Ariporo'],
            ['department' => 'Casanare', 'name' => 'Tauramena'],
            // Cauca
            ['department' => 'Cauca', 'name' => 'Popayán'],
            ['department' => 'Cauca', 'name' => 'Santander de Quilichao'],
            ['department' => 'Cauca', 'name' => 'Puerto Tejada'],
            ['department' => 'Cauca', 'name' => 'El Tambo'],
            ['department' => 'Cauca', 'name' => 'Caloto'],
            ['department' => 'Cauca', 'name' => 'Corinto'],
            // Cesar
            ['department' => 'Cesar', 'name' => 'Valledupar'],
            ['department' => 'Cesar', 'name' => 'Aguachica'],
            ['department' => 'Cesar', 'name' => 'Bosconia'],
            ['department' => 'Cesar', 'name' => 'La Jagua de Ibirico'],
            ['department' => 'Cesar', 'name' => 'Chimichagua'],
            // Chocó
            ['department' => 'Chocó', 'name' => 'Quibdó'],
            ['department' => 'Chocó', 'name' => 'Istmina'],
            ['department' => 'Chocó', 'name' => 'Tadó'],
            ['department' => 'Chocó', 'name' => 'Riosucio'],
            // Córdoba
            ['department' => 'Córdoba', 'name' => 'Montería'],
            ['department' => 'Córdoba', 'name' => 'Cereté'],
            ['department' => 'Córdoba', 'name' => 'Sahagún'],
            ['department' => 'Córdoba', 'name' => 'Lorica'],
            ['department' => 'Córdoba', 'name' => 'Tierralta'],
            ['department' => 'Córdoba', 'name' => 'Montelíbano'],
            ['department' => 'Córdoba', 'name' => 'Planeta Rica'],
            // Cundinamarca
            ['department' => 'Cundinamarca', 'name' => 'Soacha'],
            ['department' => 'Cundinamarca', 'name' => 'Fusagasugá'],
            ['department' => 'Cundinamarca', 'name' => 'Facatativá'],
            ['department' => 'Cundinamarca', 'name' => 'Zipaquirá'],
            ['department' => 'Cundinamarca', 'name' => 'Chía'],
            ['department' => 'Cundinamarca', 'name' => 'Girardot'],
            ['department' => 'Cundinamarca', 'name' => 'Mosquera'],
            ['department' => 'Cundinamarca', 'name' => 'Madrid'],
            ['department' => 'Cundinamarca', 'name' => 'Funza'],
            ['department' => 'Cundinamarca', 'name' => 'Cajicá'],
            ['department' => 'Cundinamarca', 'name' => 'La Mesa'],
            ['department' => 'Cundinamarca', 'name' => 'Sibaté'],
            ['department' => 'Cundinamarca', 'name' => 'Tocancipá'],
            ['department' => 'Cundinamarca', 'name' => 'Sopó'],
            ['department' => 'Cundinamarca', 'name' => 'Cota'],
            ['department' => 'Cundinamarca', 'name' => 'Tabio'],
            ['department' => 'Cundinamarca', 'name' => 'Tenjo'],
            ['department' => 'Cundinamarca', 'name' => 'Bojacá'],
            ['department' => 'Cundinamarca', 'name' => 'El Rosal'],
            ['department' => 'Cundinamarca', 'name' => 'Subachoque'],
            // Guainía
            ['department' => 'Guainía', 'name' => 'Inírida'],
            // Guaviare
            ['department' => 'Guaviare', 'name' => 'San José del Guaviare'],
            ['department' => 'Guaviare', 'name' => 'Calamar'],
            // Huila
            ['department' => 'Huila', 'name' => 'Neiva'],
            ['department' => 'Huila', 'name' => 'Pitalito'],
            ['department' => 'Huila', 'name' => 'Garzón'],
            ['department' => 'Huila', 'name' => 'La Plata'],
            ['department' => 'Huila', 'name' => 'San Agustín'],
            // La Guajira
            ['department' => 'La Guajira', 'name' => 'Riohacha'],
            ['department' => 'La Guajira', 'name' => 'Maicao'],
            ['department' => 'La Guajira', 'name' => 'Uribia'],
            ['department' => 'La Guajira', 'name' => 'Manaure'],
            ['department' => 'La Guajira', 'name' => 'San Juan del Cesar'],
            // Magdalena
            ['department' => 'Magdalena', 'name' => 'Santa Marta'],
            ['department' => 'Magdalena', 'name' => 'Ciénaga'],
            ['department' => 'Magdalena', 'name' => 'Fundación'],
            ['department' => 'Magdalena', 'name' => 'El Banco'],
            ['department' => 'Magdalena', 'name' => 'Plato'],
            // Meta
            ['department' => 'Meta', 'name' => 'Villavicencio'],
            ['department' => 'Meta', 'name' => 'Acacías'],
            ['department' => 'Meta', 'name' => 'Granada'],
            ['department' => 'Meta', 'name' => 'Puerto López'],
            ['department' => 'Meta', 'name' => 'San Martín'],
            ['department' => 'Meta', 'name' => 'Restrepo'],
            // Nariño
            ['department' => 'Nariño', 'name' => 'Pasto'],
            ['department' => 'Nariño', 'name' => 'Tumaco'],
            ['department' => 'Nariño', 'name' => 'Ipiales'],
            ['department' => 'Nariño', 'name' => 'Túquerres'],
            ['department' => 'Nariño', 'name' => 'La Unión'],
            // Norte de Santander
            ['department' => 'Norte de Santander', 'name' => 'Cúcuta'],
            ['department' => 'Norte de Santander', 'name' => 'Ocaña'],
            ['department' => 'Norte de Santander', 'name' => 'Pamplona'],
            ['department' => 'Norte de Santander', 'name' => 'Villa del Rosario'],
            ['department' => 'Norte de Santander', 'name' => 'Los Patios'],
            ['department' => 'Norte de Santander', 'name' => 'El Zulia'],
            // Putumayo
            ['department' => 'Putumayo', 'name' => 'Mocoa'],
            ['department' => 'Putumayo', 'name' => 'Puerto Asís'],
            ['department' => 'Putumayo', 'name' => 'Orito'],
            ['department' => 'Putumayo', 'name' => 'Valle del Guamuez'],
            // Quindío
            ['department' => 'Quindío', 'name' => 'Armenia'],
            ['department' => 'Quindío', 'name' => 'Calarcá'],
            ['department' => 'Quindío', 'name' => 'Montenegro'],
            ['department' => 'Quindío', 'name' => 'La Tebaida'],
            ['department' => 'Quindío', 'name' => 'Circasia'],
            ['department' => 'Quindío', 'name' => 'Quimbaya'],
            // Risaralda
            ['department' => 'Risaralda', 'name' => 'Pereira'],
            ['department' => 'Risaralda', 'name' => 'Dosquebradas'],
            ['department' => 'Risaralda', 'name' => 'Santa Rosa de Cabal'],
            ['department' => 'Risaralda', 'name' => 'La Virginia'],
            ['department' => 'Risaralda', 'name' => 'Marsella'],
            // San Andrés y Providencia
            ['department' => 'San Andrés y Providencia', 'name' => 'San Andrés'],
            ['department' => 'San Andrés y Providencia', 'name' => 'Providencia'],
            // Santander
            ['department' => 'Santander', 'name' => 'Bucaramanga'],
            ['department' => 'Santander', 'name' => 'Floridablanca'],
            ['department' => 'Santander', 'name' => 'Girón'],
            ['department' => 'Santander', 'name' => 'Piedecuesta'],
            ['department' => 'Santander', 'name' => 'Barrancabermeja'],
            ['department' => 'Santander', 'name' => 'Socorro'],
            ['department' => 'Santander', 'name' => 'San Gil'],
            ['department' => 'Santander', 'name' => 'Vélez'],
            ['department' => 'Santander', 'name' => 'Lebrija'],
            ['department' => 'Santander', 'name' => 'Málaga'],
            // Sucre
            ['department' => 'Sucre', 'name' => 'Sincelejo'],
            ['department' => 'Sucre', 'name' => 'Corozal'],
            ['department' => 'Sucre', 'name' => 'Sampués'],
            ['department' => 'Sucre', 'name' => 'San Marcos'],
            ['department' => 'Sucre', 'name' => 'Tolú'],
            // Tolima
            ['department' => 'Tolima', 'name' => 'Ibagué'],
            ['department' => 'Tolima', 'name' => 'Espinal'],
            ['department' => 'Tolima', 'name' => 'Melgar'],
            ['department' => 'Tolima', 'name' => 'Honda'],
            ['department' => 'Tolima', 'name' => 'Líbano'],
            ['department' => 'Tolima', 'name' => 'Chaparral'],
            ['department' => 'Tolima', 'name' => 'Mariquita'],
            // Valle del Cauca
            ['department' => 'Valle del Cauca', 'name' => 'Cali'],
            ['department' => 'Valle del Cauca', 'name' => 'Buenaventura'],
            ['department' => 'Valle del Cauca', 'name' => 'Palmira'],
            ['department' => 'Valle del Cauca', 'name' => 'Tuluá'],
            ['department' => 'Valle del Cauca', 'name' => 'Buga'],
            ['department' => 'Valle del Cauca', 'name' => 'Cartago'],
            ['department' => 'Valle del Cauca', 'name' => 'Yumbo'],
            ['department' => 'Valle del Cauca', 'name' => 'Jamundí'],
            ['department' => 'Valle del Cauca', 'name' => 'Florida'],
            ['department' => 'Valle del Cauca', 'name' => 'Candelaria'],
            ['department' => 'Valle del Cauca', 'name' => 'Roldanillo'],
            ['department' => 'Valle del Cauca', 'name' => 'Zarzal'],
            ['department' => 'Valle del Cauca', 'name' => 'La Unión'],
            ['department' => 'Valle del Cauca', 'name' => 'Riofrío'],
            // Vaupés
            ['department' => 'Vaupés', 'name' => 'Mitú'],
            // Vichada
            ['department' => 'Vichada', 'name' => 'Puerto Carreño'],
            ['department' => 'Vichada', 'name' => 'La Primavera'],
        ];

        City::insert($cities);
    }
}
