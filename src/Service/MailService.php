<?php

namespace App\Service;


use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class MailService
{

  /**
   *
   * @var MailerInterface
   */
  private MailerInterface $mailer;

  public function __construct(MailerInterface $mailer)
  {
    $this->mailer = $mailer;
  }

  public function sendEmail(
    string $from,
    string $subject,
    string $htmlTemplate,
    array $context,
    string $to = 'admin@symrecipe.com',
  ): void {

    //email
    $email = (new TemplatedEmail())
      ->from(new Address($from, 'Mailtrap'))
      ->to($to)
      ->subject($subject)
      ->htmlTemplate($htmlTemplate)
      ->context($context);
    $this->mailer->send($email);
  }
}
