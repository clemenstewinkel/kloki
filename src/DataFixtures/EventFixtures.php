<?php

namespace App\DataFixtures;

use App\DBAL\Types\ContractStateType;
use App\DBAL\Types\EventArtType;
use App\DBAL\Types\HotelStateType;
use App\DBAL\Types\PressMaterialStateType;
use App\Entity\Addresse;
use App\Entity\Ausstattung;
use App\Entity\Bestuhlungsplan;
use App\Entity\KloKiEvent;
use App\Entity\KloKiEventKategorie;
use App\Entity\Room;
use App\Entity\StageOrder;
use App\Repository\AddresseRepository;
use App\Repository\KloKiEventKategorieRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use Cassandra\Date;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EventFixtures extends Fixture
{
    private $params;
    private $userRepo;
    private $roomRepo;
    private $katRepo;
    private $addressRepo;

    public function __construct(
        ParameterBagInterface $params,
        UserRepository $userRepo,
        KloKiEventKategorieRepository $katRepo,
        AddresseRepository $addressRepo,
        RoomRepository $roomRepo)
    {
        $this->params = $params;
        $this->userRepo = $userRepo;
        $this->roomRepo = $roomRepo;
        $this->katRepo = $katRepo;
        $this->addressRepo = $addressRepo;
    }

    public function load(ObjectManager $manager)
    {
        $roomIds = $this->roomRepo->createQueryBuilder('a')->select('a.id')->getQuery()->getArrayResult();
        /**
         * Events aus einer CSV-Datei lesen
         */
        $events = file(__DIR__ . '/../../dummy-daten/events.csv');
        $fmt = new \IntlDateFormatter('de_DE', \IntlDateFormatter::FULL, \IntlDateFormatter::SHORT, 'UTC', \IntlDateFormatter::GREGORIAN);
        $tag_fmt = new \IntlDateFormatter('de_DE', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, 'UTC', \IntlDateFormatter::GREGORIAN);
        foreach($events as $event)
        {
            $e = new KloKiEvent();
            $a = explode(';', $event);
            $time_s = $a[0] . ' ' . $a[1];
            $fullday = false;
            $unix_ts_tag_beginn = $tag_fmt->parse($a[0]);
            if(trim($a[1]) == '')
            {
                $fullday = true;
                $unix_ts_start = $unix_ts_tag_beginn;
                $unix_ts_end   = $unix_ts_tag_beginn + (24 * 3600);
            }
            else // kein ganzer Tag!
            {
                $unix_ts_start = $fmt->parse($time_s);
                // Gibt es ein "-" in der Uhrzeit?
                if(strstr($a[1], '-'))
                {
                    $b = explode('-', $a[1]);
                    $unix_ts_end = $fmt->parse($a[0] . ' ' . trim($b[1]));
                }
                else // kein "-"
                {
                    // Bis mitternacht
                    $unix_ts_end = $unix_ts_tag_beginn + (24 * 3600);
                }
            }
            if($unix_ts_start)
            {
                dump('Neues Event: -->' . $time_s . '<--');
                if($fullday)
                {
                    dump("Ganzer Tag: " . date('Y-m-d', $unix_ts_start));
                }
                else
                {
                    dump('Zeit: ' . date('Y-m-d H:i', $unix_ts_start) . ' bis ' . date('H:i', $unix_ts_end) );
                }
                $e->setStart(new \DateTime(date('Y-m-d H:i', $unix_ts_start)));
                $e->setEnd(new \DateTime(date('Y-m-d H:i', $unix_ts_end)));
                $e->setAllDay($fullday);
                $e->setName(trim($a[2]));
                $e->setIsFixed(true); // TODO
                $e->setHelperEinlassEins( $this->userRepo->findOneBy(['name' => trim($a[7])]));
                $e->setHelperEinlassZwei( $this->userRepo->findOneBy(['name' => trim($a[8])]));
                $e->setHelperKasse(       $this->userRepo->findOneBy(['name' => trim($a[9])]));
                $e->setHelperSpringerEins($this->userRepo->findOneBy(['name' => trim($a[10])]));
                $e->setHelperSpringerZwei($this->userRepo->findOneBy(['name' => trim($a[11])]));

                if(strpos($a[3], 'Vermietung') !== false)
                    $e->setArt(EventArtType::RENTAL);
                elseif (strpos($a[3], 'Markt' ) !== false)
                    $e->setArt(EventArtType::FAIR);
                else
                    $e->setArt(EventArtType::SHOW);

                $roomId = array_rand($roomIds);
                $e->setRoom($this->roomRepo->findOneBy(['id' => $roomIds[$roomId]['id']]));

                $e->setIsBestBenoetigt(false); // TODO
                $e->setIsFixed(true);// TODO
                $e->setIsLichtBenoetigt(false);// TODO
                $e->setIsTonBenoetigt(false); // TODO
                $e->setKategorie($this->katRepo->findOneBy(['name' => 'Konzert'])); // TODO
                $e->setKontakt($this->addressRepo->findOneBy(['vorname' => 'Sonja'])); // TODO
                $e->setHelperRequired(true); // TODO
                $e->setContractState(ContractStateType::NONE);
                $e->setIsReducedPrice(false);
                $e->setIs4hPrice(false);
                $e->setHotelState(HotelStateType::NONE);
                $e->setPressMaterialState(PressMaterialStateType::NONE);
                $manager->persist($e);

            }
            else
            {
                dump("Kann die Zeit nicht lesen: -->" . $time_s . "<-- !!");
            }
        }

        $manager->flush();
    }
}
