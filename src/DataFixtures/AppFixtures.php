<?php

namespace App\DataFixtures;

use App\Entity\Addresse;
use App\Entity\Ausstattung;
use App\Entity\Bestuhlungsplan;
use App\Entity\KloKiEventKategorie;
use App\Entity\KloKiEventType;
use App\Entity\Room;
use App\Entity\StageOrder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AppFixtures extends Fixture
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function load(ObjectManager $manager)
    {
        // Event-Kategorien
        foreach(['Kabarett', 'Konzert', 'Lesung', 'Comedy', 'Tagung', 'Private Feier'] as $cat)
        {
            $x = new KloKiEventKategorie();
            $x->setName($cat);
            $manager->persist($x);
        }

        // Räume
        foreach([
            ['Minoritensaal' , '#ff9797', 20825 , 15000 , 16000, 8000 ],
            ['Färbersaal'    , '#a6a6fa', 7000  , 5000  , 3500 , 2000 ],
            ['Zwirnstube'    , '#ffff00', 5000  , 3000  , 3000 , 1000 ],
            ['Foyer oben'    , '#00ffff', 5000  , 3000  , 3000 , 1000 ],
            ['Webersaal'     , '#00ff00', 12500 , 8000  , 8000 , 4000 ],
            ['Foyer unten'   , '#f2c197', 5000  , 3000  , 3000 , 2000 ]
             ] as $room)
        {
            $x = new Room();
            $x->setName($room[0]);
            $x->setColor($room[1]);
            $x->setFullDayPrice($room[2]);
            $x->setHalfDayPrice($room[3]);
            $x->setFullDayPriceIntern($room[4]);
            $x->setHalfDayPriceIntern($room[5]);
            $manager->persist($x);
        }

        $a = new Addresse();
        $a->setNachname('Tewinkel')
            ->setVorname('Sonja')
            ->setStrasse('Geschwister-Scholl-Weg 5')
            ->setPlz('51519')
            ->setOrt('Odenthal')
            ->setTelefon('+49 2202 862322')
        ;
        $manager->persist($a);

        $a = new Addresse();
        $a->setNachname('Jakob')
            ->setVorname('Schröder')
            ->setStrasse('Gustavstr. 17')
            ->setPlz('50939')
            ->setOrt('Köln')
            ->setTelefon('+49 221 1234556')
        ;
        $manager->persist($a);

        /**
         * Bühnenanweisungen
         */
        foreach(scandir(__DIR__ . '/../../dummy-daten/stageOrders/') as $file)
        {
            if($file != '.' && $file != '..')
            {
                dump("Processing file: " . $file);
                $fullPdfFileName = tempnam($this->params->get('pdf_file_directory'), 'stageOrder_');
                $fullPngFileName = tempnam($this->params->get('png_file_directory'), 'stageOrder_');

                dump("Saving pdf to " . $fullPdfFileName);
                dump("Saving png to " . $fullPngFileName);


                $pdfFileName = basename($fullPdfFileName);
                $pngFileName = basename($fullPngFileName);

                copy(__DIR__ . '/../../dummy-daten/stageOrders/' . $file, $fullPdfFileName);

                $im = new \Imagick($fullPdfFileName . '[0]');
                $im->setImageFormat('png');
                $im->resizeImage(100,150, \Imagick::FILTER_CUBIC, 1.0);
                file_put_contents($fullPngFileName, $im );

                $ba = new StageOrder();
                $ba->setName($file);
                $ba->setPngFileName($pngFileName);
                $ba->setPdfFileName($pdfFileName);
                $manager->persist($ba);
            }
        }

        /**
         * Bestuhlungspläne
         */
        foreach(scandir(__DIR__ . '/../../dummy-daten/stuhlPlaene') as $file)
        {
            if($file != '.' && $file != '..')
            {
                dump("Processing file: " . $file);
                $fullPdfFileName = tempnam($this->params->get('pdf_file_directory'), 'stuhlplan_');
                $fullPngFileName = tempnam($this->params->get('png_file_directory'), 'stuhlplan_');
                $pdfFileName = basename($fullPdfFileName);
                $pngFileName = basename($fullPngFileName);

                copy(__DIR__ . '/../../dummy-daten/stuhlPlaene/' . $file, $fullPdfFileName);

                $im = new \Imagick($fullPdfFileName . '[0]');
                $im->setImageFormat('png');
                $im->resizeImage(100,150, \Imagick::FILTER_CUBIC, 1.0);
                file_put_contents($fullPngFileName, $im );

                $ba = new Bestuhlungsplan();
                $ba->setName($file);
                $ba->setSitzplaetze(123);
                $ba->setSitzplaetzeOben(12);
                $ba->setStehplaetze(456);
                $ba->setPngFilePath($pngFileName);
                $ba->setPdfFilePath($pdfFileName);
                $manager->persist($ba);
            }
        }


        /**
         * Ausstattung
         */
        $aa = [
            ['Bühne',        'Bühne (inkl. Bühnenlicht, weiß, dimmbar)',                    8500,  6000],
            ['Vorbühne 1/2', 'halbe Vorbühne',                                              4500,  3000],
            ['Vorbühne 1/1', 'Volle Vorbühne',                                              9000,  7000],
            ['Tonanlage',    'kleine Tonanlage in Selbstbedienung inkl. 1 Kabelmikrophon',  6500,  4000],
            ['Headset',      'zusätzliches Headset (Funk)',                                 1500,  800],
            ['Ton/Licht',    'professionelle Ton- und Lichtanlage (inkl. Techniker)',       42500, 30000],
            ['Leinwand',     'Leinwand',                                                    4000,  3000],
            ['Beamer',       'Beamer',                                                      8500,  5000],
            ['Klavier',      'Klavier inkl. Stimmung',                                      11000, 9000],
            ['Flügel',       'Flügel inkl. Stimmung',                                       14000, 11000],
            ['Monitor',      'Monitor im Foyer',                                            3000,  2000],
            ['Kerzen',       'Kerzensatz',                                                  6500,  4000],
            ['V-Pauschale',  'Veranstaltungspauschale (Ticketing, Website)',                5500,  4000],
        ];
        foreach($aa as $a)
        {
            $x = new Ausstattung();
            $x->setName($a[0]);
            $x->setDescription($a[1]);
            $x->setNettopreis($a[2]);
            $x->setNettopreisIntern($a[3]);
            $manager->persist($x);
        }
        $manager->flush();
    }
}
