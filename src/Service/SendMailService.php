<?php /** @noinspection PhpInternalEntityUsedInspection */


namespace App\Service;

use App\Entity\KloKiEvent;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Throwable;
use Twig\Environment;

class SendMailService
{
    private $twig;
    private $params;
    private $mailer;

    public function __construct(Swift_Mailer $mailer, ParameterBagInterface $params, Environment $twig)
    {
        $this->twig = $twig;
        $this->params = $params;
        $this->mailer = $mailer;
    }

    public function informAboutContractRequest(KloKiEvent $event)
    {
        $rcpts = explode(';', $this->params->get('admin_mail_rcpts'));
        try { $template = $this->twig->loadTemplate('email/informAboutContractRequest.html.twig'); }
        catch (Exception $e) { return false; }
        try { $renderedMail = $template->render(['e' => $event]); }
        catch (Throwable $e) { return false; }

        $message = (new Swift_Message('Bitte den Vertrag erstellen für: '. $event->getName().', '.$event->getStart()->format('d.m.Y')))
            ->setFrom(['noreply@klosterkirche-lennep.de' => 'Kloki Mailing Service'])
            ->setTo($rcpts)
            ->setBody($renderedMail, 'text/html')
        ;
        return $this->mailer->send($message);
    }

    public function informAboutHelperCancelled(KloKiEvent $event, UserInterface $user)
    {
        $rcpts = explode(';', $this->params->get('admin_mail_rcpts'));
        try { $template = $this->twig->loadTemplate('email/informAboutHelperCancelled.html.twig'); }
        catch (Exception $e) { return false; }
        try { $renderedMail = $template->render(['e' => $event, 'u' => $user]); }
        catch (Throwable $e) { return false; }

        $message = (new Swift_Message('Absage eines eingeteilten Helfers bei '. $event->getName().', '.$event->getStart()->format('d.m.Y')))
            ->setFrom(['noreply@klosterkirche-lennep.de' => 'Kloki Mailing Service'])
            ->setTo($rcpts)
            ->setBody($renderedMail, 'text/html')
        ;
        return $this->mailer->send($message);
    }

    public function sendDutyListToHelper(string $finalDay, Array $events, User $helper, string $additionalText)
    {
        $rcpts = $helper->getEmail(); //explode(';', $this->params->get('admin_mail_rcpts'));
        try { $template = $this->twig->loadTemplate('email/informComingDuties.html.twig'); }
        catch (Exception $e) { dump($e); return false; }
        try { $renderedMail = $template->render(['finalDay' => $finalDay, 'events' => $events, 'u' => $helper, 's' => $additionalText]); }
        catch (Throwable $e) { dump($e); return false; }

        $message = (new Swift_Message('Einteilung für Dienste in der Klosterkirche'))
            ->setFrom(['noreply@klosterkirche-lennep.de' => 'Kloki Mailing Service'])
            ->setTo($rcpts)
            ->setBody($renderedMail, 'text/html')
        ;
        $logoAttachment = \Swift_Attachment::fromPath('build/images/logo_for_mail.e958f3df.svg')->setDisposition('inline');
        $logoAttachment->getHeaders()->addTextHeader('Content-ID', '<ABC123>');
        $logoAttachment->getHeaders()->addTextHeader('X-Attachment-Id', 'ABC123');
        $message->embed($logoAttachment);
        return $this->mailer->send($message);
    }

    public function informHelpersAboutEvent(KloKiEvent $event, string $additionalText)
    {
        $rcpts = array();
        if($event->getHelperEinlassEins()  && (!in_array($event->getHelperEinlassEins()->getEmail(),  $rcpts))) array_push($rcpts, $event->getHelperEinlassEins()->getEmail());
        if($event->getHelperEinlassZwei()  && (!in_array($event->getHelperEinlassZwei()->getEmail(),  $rcpts))) array_push($rcpts, $event->getHelperEinlassZwei()->getEmail());
        if($event->getHelperSpringerEins() && (!in_array($event->getHelperSpringerEins()->getEmail(), $rcpts))) array_push($rcpts, $event->getHelperSpringerEins()->getEmail());
        if($event->getHelperSpringerZwei() && (!in_array($event->getHelperSpringerZwei()->getEmail(), $rcpts))) array_push($rcpts, $event->getHelperSpringerZwei()->getEmail());
        if($event->getHelperKasse()        && (!in_array($event->getHelperKasse()->getEmail(),        $rcpts))) array_push($rcpts, $event->getHelperKasse()->getEmail());
        if($event->getHelperGarderobe()    && (!in_array($event->getHelperGarderobe()->getEmail(),    $rcpts))) array_push($rcpts, $event->getHelperGarderobe()->getEmail());
        try { $template = $this->twig->loadTemplate('email/informHelpersAboutEvent.html.twig'); }
        catch (Exception $e) { return false; }
        try { $renderedMail = $template->render(['klo_ki_event' => $event, 'add_text' => $additionalText]); }
        catch (Throwable $e) { return false; }

        $message = (new Swift_Message('Informationen zu ' . $event->getName() . ' am ' . $event->getStart()->format('d. m. Y')))
            ->setFrom(['noreply@klosterkirche-lennep.de' => 'Kloki Mailing Service'])
            ->setTo($rcpts)
            ->setBody($renderedMail, 'text/html')
        ;
        $this->mailer->send($message);
        return count($rcpts);
    }

    public function informTechsAboutEvent(KloKiEvent $event, string $additionalText)
    {
        $rcpts = array();
        if($event->getLichtTechniker()  && (!in_array($event->getLichtTechniker()->getEmail(),  $rcpts))) array_push($rcpts, $event->getLichtTechniker()->getEmail());
        if($event->getTonTechniker()  && (!in_array($event->getTonTechniker()->getEmail(),  $rcpts))) array_push($rcpts, $event->getTonTechniker()->getEmail());
        try { $template = $this->twig->loadTemplate('email/informTechsAboutEvent.html.twig'); }
        catch (Exception $e) { return false; }
        try { $renderedMail = $template->render(['klo_ki_event' => $event, 'add_text' => $additionalText]); }
        catch (Throwable $e) { return false; }

        $message = (new Swift_Message('Informationen zu ' . $event->getName() . ' am ' . $event->getStart()->format('d. m. Y')))
            ->setFrom(['noreply@klosterkirche-lennep.de' => 'Kloki Mailing Service'])
            ->setTo($rcpts)
            ->setBody($renderedMail, 'text/html')
        ;
        $this->mailer->send($message);
        return count($rcpts);
    }

}