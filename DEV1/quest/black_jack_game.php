<?php
//ゲームの制御、勝敗
class BlackJackGame{
    private $deck;
    private $player;
    private $dealer;
    private $card;

    public function __construct()
    {
        $this->deck = new Deck();
        $this->player = new Player();
        $this->dealer = new Dealer();
        $this->card = new Card();
    }

    public function gamestart()
    {
        echo 'ブラックジャックを開始します。'. PHP_EOL;
        $cntp = 0;
        $cntd = 0;

        $this->deck->DeckShuffle();

        $this->DrawPlayerHand();
        $this->player->GetHand($cntp);
        $cntp++;
        $this->DrawPlayerHand();
        $this->player->GetHand($cntp);
        $cntp++;

        $this->DrawDealerHand();
        $this->dealer->GetHand($cntd);
        $cntd++;
        $this->DrawDealerHand();
        $this->dealer->GetHand($cntd);
        $cntd++;

        $res = $this->player->DrawOrStand();

        while($res == 'y' || $res == 'Y') {
            $this->DrawPlayerHand();
            $this->player->GetHand($cntp);
            $cntp++;
            $res = $this->player->DrawOrStand();
        }

        if($this->player->score > 21){
            echo 'あなたの得点は'.$this->player->score.'です。'. PHP_EOL;
            echo 'バーストです。あなたの負けです。'. PHP_EOL;
            echo 'ブラックジャックを終了します。'. PHP_EOL;
            exit;
        }
        
        $this->dealer->FlipCardDealer();

        while($this->dealer->score < 17){
            $this->DrawDealerHand();
            $this->dealer->GetHand($cntd);
            echo 'ディーラーの現在の得点は'. $this->dealer->score. 'です。'. PHP_EOL;
            $cntd++;
        }
        if($this->dealer->score > 21){
            echo 'ディーラーの得点は'.$this->dealer->score.'です。'. PHP_EOL;
            echo 'ディーラーのバーストです。あなたの勝ちです！'. PHP_EOL;
            echo 'ブラックジャックを終了します。'. PHP_EOL;
            exit;
        }

        echo 'あなたの得点は'.$this->player->score.'です。'. PHP_EOL;
        echo 'ディーラーの得点は'.$this->dealer->score.'です。'. PHP_EOL;

        if($this->player->score > $this->dealer->score){
            echo 'あなたの勝ちです！'. PHP_EOL;
            echo 'ブラックジャックを終了します。'. PHP_EOL;
        }
        else if($this->player->score < $this->dealer->score){
            echo 'あなたの負けです。'. PHP_EOL;
            echo 'ブラックジャックを終了します。'. PHP_EOL;
        }
        else{
            echo '引き分けです。'. PHP_EOL;
            echo 'ブラックジャックを終了します。'. PHP_EOL;
        }
    }

    public function DrawPlayerHand(){
        
        $this->card->SetCard($this->deck);

        $this->player->SetPlayer($this->card);
        
    }

    public function DrawDealerHand(){
        
        $this->card->SetCard($this->deck);
        $this->dealer->SetDealer($this->card);

    }
}
//カード生成
class Card{
    public $suit;
    public $number;

    public function SetCard($deck){
        $cardNumber = $deck->GetDeck();
        $num = $this->CreateSuit($cardNumber);
        $this->CreatePictureCard($num);
    }

    public function CreatePictureCard($num){
        if($num == 1){
            $this->number = 'A';
        }
        else if($num == 11){
            $this->number = 'J';
        }
        else if($num == 12){
            $this->number = 'Q';
        }
        else if($num == 13){
            $this->number = 'K';
        }
        else{
            $this->number = $num;
        }
    }

    public function CreateSuit($num){
        if($num >= 1 && $num <= 13){
            $this->suit = 'クラブ'; 
            return $num;
        }
        else if($num >= 14 && $num <= 26){
            $this->suit = 'スペード';
            return $num -13;
        }
        else if($num >= 27 && $num <= 39){
            $this->suit = 'ダイヤ';
            return $num -26;
        }
        else{
            $this->suit = 'ハート';
            return $num -39;
        } 
    }

   

    public function GetSuit(){
        return $this->suit;
    }

    public function GetNumber(){
        return $this->number;
    }
}

//デッキの初期化
class Deck{
    public $deck;

    public function DeckShuffle(){
            $this->deck = range(1,52);
            shuffle($this->deck); 
    }

    public function GetDeck(){
        return array_shift($this->deck);
    }


}
//プレイヤーの得点、手札
class Player{
    public $hand = [];
    public $score = 0;

    public function SetPlayer($card){
        $this->hand []= $card;
    }

    public function GetHand($turn){

        $card = $this->hand[$turn];
        echo 'あなたの引いたカードは'.$card->GetSuit().'の'.$card->GetNumber().'です。'. PHP_EOL;
        
        $score = $card->GetNumber();

        if($score == 'A'){
            $this->score += 1;
        }
        else if($score == 'J' || $score == 'Q' || $score == 'K'){
            $this->score += 10;
        }
        else{
            $this->score += $score;
        }
    }

    public function DrawOrStand(){
        if($this->score < 21){
        echo 'あなたの現在の得点は'.$this->score.'です。カードを引きますか？(Y/N)'. PHP_EOL;
        $res = trim(fgets(STDIN));
        return $res;
        }
    }
}
//ディーラーの得点、手札
class Dealer{
public $hand = [];
public $score = 0;

public function SetDealer($card){
    $this->hand []= $card;
}

public function GetHand($turn){

    $card = $this->hand[$turn];

    if($turn != 1){
   
    echo 'ディーラーの引いたカードは'.$card->GetSuit().'の'.$card->GetNumber().'です。'. PHP_EOL;
    }
    else{
        echo 'ディーラーの引いた2枚目のカードはわかりません。'. PHP_EOL;
    }

    $cardnum = $card->GetNumber();

        if($cardnum == 'A'){
            $this->score += 1;
        }
        else if($cardnum == 'J' || $cardnum =='Q' || $cardnum =='K'){
            $this->score += 10;
        }
        else{
            $this->score += $cardnum;
        }
}

public function FlipCardDealer(){
    $card = $this->hand[1];
    echo 'ディーラーの引いた2枚目のカードは'.$card->GetSuit().'の'.$card->GetNumber().'でした。'. PHP_EOL;
    echo 'ディーラーの現在の得点は'. $this->score. 'です。'. PHP_EOL;
}
}

$blackjack = new BlackJackGame();
$blackjack->gamestart();

?>