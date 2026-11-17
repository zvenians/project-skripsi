<?php

namespace App\Conversations;

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question as BotManQuestion;
use BotMan\BotMan\Messages\Incoming\Answer as BotManAnswer;


class ExampleConversation extends Conversation
{
    protected $facility = 'facility';
    /**
     * First question
     */
    public function askFacility()
    {
        $question = Question::create("Silahkan pilih tempat yang ingin dicari.")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Music Center')->value('mc'),
                Button::create('Student Center')->value('sc'),
                Button::create('Ruang Kaca')->value('rk'),
                Button::create('New Dormitory Girls')->value('ndg'),
                Button::create('New Dormitory Boys')->value('ndb'),
                Button::create('102')->value('nacb'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                switch ($answer->getValue()) {
                    case 'mc':
                        $this->facility = 'mc';
                        $this->jawabanNya('Music Center');
                        break;
                    case 'sc':
                        $this->facility = 'sc';
                        $this->jawabanNya('Stdent Center');
                        break;
                    case 'rk':
                        $this->facility = 'rk';
                        $this->jawabanNya('Ruang Kaca');
                        break;
                    case 'ndg':
                        $this->facility = 'ndg';
                        $this->jawabanNya('New Girls Dormitory');
                        break;
                    case 'ndb':
                        $this->facility = 'ndb';
                        $this->jawabanNya('New Boys Dormitory');
                        break;
                    case '102':
                        $this->facility = 't102';
                        $this->jawabanNya('nacb');
                        break;

                    default:
                        # code...
                       break;
                }

            }
        });
    }

    public function jawabanNya($tokoh)
    {
        $this->ask('Kamu memilih facility dari '.$tokoh.'. Silahkan masukkan nomor hadits yang ingin dicari.', function (Answer $answer) {
            $no = $answer->getText();
            // echo $no;
            $hasil=$this->getData($this->facility, $no);
            $jawaban = sprintf("Hadits menjelaskan tentang: ".$hasil[3].". \r\t\n\n ".$hasil[0]."\r\t\n\n ".$hasil[1]);
            // $this->say('Hadits menjelaskan tentang: '.$hasil[3]);
            // $this->say($hasil[0]);
            $this->say($jawaban);
            if ($hasil[2]==true) {
                $this->say('Silahkan laporkan permasalahan ini dengan menu /lapor .');
            }
        });
    }

    // public function getData($facility, $no)
    // {
    //     try {
    //         $str='https://scrape-fastapi.herokuapp.com/hadits/?tokoh='.$facility.'&no='.$no;
    //         // $str='https://hadits-api-zhirrr.vercel.app/books/'.$facility.'/'.$no;
    //         $dt = json_decode(file_get_contents($str));
    //         return [$dt->data->contents->arab, $dt->data->contents->id, false, $dt->data->contents->judul];
    //     } catch (\Throwable $th) {
    //         return ["Something went wrong 😯️","Sepertinya ada masalah.🧐️", true];
    //     }

    // }
    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askFacility();
        // $this->cariLagi();
    }
}