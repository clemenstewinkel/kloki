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
        // Event-Typen
        foreach(['Vermietung', 'Eigenveranstaltung'] as $type)
        {
            $x = new KloKiEventType();
            $x->setName($type);
            $manager->persist($x);
        }

        // Event-Kategorien
        foreach(['Kabarett', 'Konzert', 'Lesung', 'Comedy', 'Tagung', 'Private Feier'] as $cat)
        {
            $x = new KloKiEventKategorie();
            $x->setName($cat);
            $manager->persist($x);
        }

        // Räume
        foreach([
            'Minoritensaal' => '#ff0000',
            'Webersaal'     => '#00ff00',
            'Färbersaal'    => '#0000ff',
            'Zwirnstube'    => '#ffff00',
            'Foyer unten'   => '#ff00ff',
            'Foyer oben'    => '#00ffff',
            'Restaurant'    => '#888888',
            'Wintergarten'  => '#996611'
             ] as $room  => $color)
        {
            $x = new Room();
            $x->setName($room);
            $x->setColor($color);
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
            ['Flügel', '10000'],
            ['Beamer', '10000'],
            ['Kerzen', '10000'],
            ['Blumen', '10000'],
            ['kleine Tonanlage', '10000']
        ];
        foreach($aa as $a)
        {
            $x = new Ausstattung();
            $x->setName($a[0]);
            $x->setNettopreis($a[1]);
            $manager->persist($x);
        }
        $manager->flush();
    }
}
