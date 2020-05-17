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
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EventFixtures extends Fixture implements FixtureGroupInterface
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
        $allRooms = $this->roomRepo->findAll();
        $allKontakts = $this->addressRepo->findAll();
        $allKategories = $this->katRepo->findAll();
        $allTypes = [EventArtType::RENTAL, EventArtType::SHOW, EventArtType::FAIR];

        dump('Creating random events...');
        dump('Creating 100 random start-times around current date');
        $start_times = array();
        $today = date('Y-m-d');
        for($i = -50 ; $i < 50; $i++) {
            array_push($start_times, new \DateTime(sprintf("$today 14:00:00 +%d days +%d hours", $i, rand(-5, +5))));
        }
        foreach ($start_times as $start_time)
        {
            /** @var $start_time \DateTime $e */
            $e = new KloKiEvent();
            $e->setStart($start_time);
            $end_time = new \DateTime($start_time->format('Y-m-d H:i:s'));
            $end_time->add(new \DateInterval('PT4H'));
            $e->setEnd($end_time);
            $e->setName("A great event!");
            $e->setRoom($allRooms[rand(0, count($allRooms)-1)]);
            $e->setArt($allTypes[rand(0,2)]);
            $e->setHotelState(HotelStateType::NONE);
            $e->setContractState(ContractStateType::NONE);
            $e->setPressMaterialState(PressMaterialStateType::NONE);
            $e->setKontakt($allKontakts[rand(0, count($allKontakts)-1)]);
            $e->setKategorie($allKategories[rand(0, count($allKategories)-1)]);
            $e->setIsBestBenoetigt(false); // TODO
            $e->setIsFixed(false);// TODO
            $e->setIsLichtBenoetigt(false);// TODO
            $e->setIsTonBenoetigt(false); // TODO
            $e->setHelperRequired(true); // TODO
            $e->setIsReducedPrice(false);
            $e->setIs4hPrice(false);
            $e->setGemaListState(ContractStateType::NONE);
            $manager->persist($e);
        }
        $manager->flush();


            /*            $e = new KloKiEvent();
                        dump('Neues Event: -->' . $time_s . '<--');
                        $e->setStart(new \DateTime(date('Y-m-d H:i', $unix_ts_start)));
                        $e->setEnd(new \DateTime(date('Y-m-d H:i', $unix_ts_end)));
                        $e->setAllDay($fullday);
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

                        $e->setKategorie($this->katRepo->findOneBy(['name' => 'Konzert'])); // TODO
                        $e->setKontakt($this->addressRepo->findOneBy(['vorname' => 'Sonja'])); // TODO
                        $e->setContractState(ContractStateType::NONE);
                        $e->setHotelState(HotelStateType::NONE);
                        $e->setPressMaterialState(PressMaterialStateType::NONE);
            */
    }

    /**
     * @inheritDoc
     */
    public static function getGroups(): array
    {
        return ['events'];
    }
}
