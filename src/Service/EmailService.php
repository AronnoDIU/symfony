<?php

// src/Service/EmailService.php

namespace App\Service;

use App\Entity\Sale;
use Exception;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EmailService
{
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @throws Exception
     */
    public function sendSaleNotificationEmail(Sale $sale): void
    {
        $customerEmail = $sale->getCustomer()->getEmail();
        $email = (new Email())
            ->from('admin@dev.symfony.com')
            ->to($customerEmail)
            ->subject('Sale Notification')
            ->html($this->twig->render('emails/email.html.twig', ['customerName' => $sale->getCustomer()->getName()]));

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}