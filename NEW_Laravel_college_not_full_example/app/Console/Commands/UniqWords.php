<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UniqWords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uniq:words {--fb2=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'count words in text';

    protected $nOrigWords = 0;
    protected $allUniqWords = [];

    var $Stem_Caching = 0;
    var $Stem_Cache = array();
    var $VOWEL = '/аеиоуыэюя/';
    var $PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/';
    var $REFLEXIVE = '/(с[яь])$/';
    var $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|ему|ому|их|ых|ую|юю|ая|яя|ою|ею)$/';
    var $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/';
    var $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ен|ило|ыло|ено|ят|ует|уют|ит|ыт|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/';
    var $NOUN = '/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|иям|ям|ием|ем|ам|ом|о|у|ах|иях|ях|ы|ь|ию|ью|ю|ия|ья|я)$/';
    var $RVRE = '/^(.*?[аеиоуыэюя])(.*)$/';
    var $DERIVATIONAL = '/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('uniq words finder');

        $fb2 = $this->option('fb2');
        if( $fb2 != null ) {
            $this->getfromFb2($fb2);
        }
        
        
        /*
        setlocale(LC_ALL, '');
        $fname = "/Users/nurlan/Downloads/rawforyou (1).sql";
        $text = file_get_contents($fname);
        $this->getUniques($text);
        */
        /*
        $handle = fopen($fname, "r") or die("Couldn't get handle");
        if ($handle) {
            while (!feof($handle)) {
                $buffer = fgets($handle, 1024000);
                $this->getUniquesPortion($buffer);
            }
            fclose($handle);
        }
        */

        

        $this->info( "Total words: " . $this->nOrigWords );
        $this->info( "Uniq words: " . count($this->allUniqWords) );
        
    }

    function getUniques($text)
    {
        $text = mb_strtolower($text);
        $text = $this->clearText($text);
        $wordsOriginal = explode(' ', $text);
        $words = [];
        foreach ($wordsOriginal as $value) {
            if ( strlen($value) > 2 ) {
                $words[] = $value;    
            }
        }

        $this->nOrigWords = $this->nOrigWords + count($words);
        
        // count uniq words
        $words = array_map([$this, 'stem_word'], $words);
        $words = array_unique($words); 
        $this->allUniqWords = array_merge($this->allUniqWords, $words);
        $this->allUniqWords = array_unique($this->allUniqWords);
    }

    function getfromFb2($file)
    {   
        $text = file_get_contents($file);
        $xml = simplexml_load_string($text);
        foreach($xml->body->section as $key => $section) {
            foreach ($section as $p) {
                $this->getUniques( (string) $p );
                foreach ($p as $item) {
                    $this->getUniques( (string) $item);
                }
                
            }
            $this->info('step total/uniq: ' . $this->nOrigWords . '/' . count($this->allUniqWords) );
        }
    }

    function clearText($text)
    {
        $text = str_replace([
                    ',', '.', ':', ';', '!', '?', ')', '(', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 
                    '«', '»', '"', "'", '<', '>', '%', '^', '&', '*', '_', '+', '=', '-', '/', '\\', '[', ']', 
                    '{', '}', '|', '…', '“', '„'
                ], '', $text);
        $text = str_replace(' ', ' ', $text);
        $text = str_replace('  ', ' ', $text);
        $text = str_replace('  ', ' ', $text);
        $text = str_replace('  ', ' ', $text);
        
        

        return $text;
    }


    // Lingua_Stem_Ru

    function s(&$s, $re, $to)
    {
        $orig = $s;
        $s = preg_replace($re, $to, $s);
        return $orig !== $s;
    }

    function m($s, $re)
    {
        return preg_match($re, $s);
    }

    function stem_word($word)
    {
        //$word = strtolower($word);
        $word = str_replace('ё', 'е', $word);
        # Check against cache of stemmed words
        if ($this->Stem_Caching && isset($this->Stem_Cache[$word])) {
            return $this->Stem_Cache[$word];
        }
        $stem = $word;
        do {
          if (!preg_match($this->RVRE, $word, $p)) break;
          $start = $p[1];
          $RV = $p[2];
          if (!$RV) break;

          # Step 1
          if (!$this->s($RV, $this->PERFECTIVEGROUND, '')) {
              $this->s($RV, $this->REFLEXIVE, '');

              if ($this->s($RV, $this->ADJECTIVE, '')) {
                  $this->s($RV, $this->PARTICIPLE, '');
              } else {
                  if (!$this->s($RV, $this->VERB, ''))
                      $this->s($RV, $this->NOUN, '');
              }
          }

          # Step 2
          $this->s($RV, '/и$/', '');

          # Step 3
          if ($this->m($RV, $this->DERIVATIONAL))
              $this->s($RV, '/ость?$/', '');

          # Step 4
          if (!$this->s($RV, '/ь$/', '')) {
              $this->s($RV, '/ейше?/', '');
              $this->s($RV, '/нн$/', 'н');
          }

          $stem = $start.$RV;
        } while(false);
        if ($this->Stem_Caching) $this->Stem_Cache[$word] = $stem;
        return $stem;
    }

    function stem_caching($parm_ref)
    {
        $caching_level = @$parm_ref['-level'];
        if ($caching_level) {
            if (!$this->m($caching_level, '/^[012]$/')) {
                die(__CLASS__ . "::stem_caching() - Legal values are '0','1' or '2'. '$caching_level' is not a legal value");
            }
            $this->Stem_Caching = $caching_level;
        }
        return $this->Stem_Caching;
    }

    function clear_stem_cache()
    {
        $this->Stem_Cache = array();
    }


}
