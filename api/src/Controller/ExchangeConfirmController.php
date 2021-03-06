<?php

namespace App\Controller;

use App\Entity\Exchange;
use App\lib\IgdbBundle\IgdbWrapper\IgdbWrapper;

class ExchangeConfirmController{

    private $igdb;

    public function __construct(IgdbWrapper $igdb){
        $this->igdb = $igdb;
    }

    public function __invoke(Exchange $data): Exchange {
        if($data->getConfirmed() !== null){
            return $this->redirectToRoute('home');
        }
        $data->setConfirmed(true);

        $messageConfirmReceiver = (new \Swift_Message('Votre confirmation de swap a bien été prise en compte !'))
            ->setFrom('swapit.esgi@gmail.com')
            ->setTo($data->getUserOwner()->getEmail())
            ->setBody(
                $this->renderView(
                    'mail/swap_confirmation_to_owner.html.twig',
                    [
                        'exchange' => $data,
                        'user' => $data->getUserProposer(),
                        'ownerGame' => $data->getOwnerGame(),
                        'selected_game' => $data->getGame(),
                        'owner' => $data->getUserOwner()
                    ]
                    ),
                    'text/html'
                );
                
        $mailer->send($messageConfirmReceiver);

        $messageConfirmProposer = (new \Swift_Message('Votre demande de swap a bien été validée !'))
            ->setFrom('swapit.esgi@gmail.com')
            ->setTo($data->getUserProposer()->getEmail())
            ->setBody(
                $this->renderView(
                    'mail/swap_confirmation_to_proposer.html.twig',
                    [
                        'exchange' => $data,
                        'user' => $data->getUserProposer(),
                        'ownerGame' => $data->getOwnerGame(),
                        'selected_game' => $data->getGame(),
                        'owner' => $data->getUserOwner()
                    ]
                    ),
                    'text/html'
                );
                
        $mailer->send($messageConfirmProposer);
    
        return $data;
    }
}