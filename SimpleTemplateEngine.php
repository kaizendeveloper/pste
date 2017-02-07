<?php
/**
 * Template engine semplicissimo
 *
 * Nota: tutte le variabili dentro il templates saranno visibili dentro la variabile $
 *
 */
namespace Views;

class SimpleTemplateEngine {

    //Per capire a partire da dove cominciamo a caricare i file
    protected $working_directory;

    protected $asset_directory;

    public function __construct() {

        //Alla costruzione stabiliamo la wd
        $this->working_directory = dirname(__FILE__);
        $this->asset_directory = dirname(__FILE__);

    }

    /**
     * Legge un file PHP e tramite la cattura del buffering poi restituiamo soltanto l'output
     *
     * @param $nomeTemplate
     * @param array $tpl
     * @return string
     */
    public function caricaTemplate($nomeTemplate, $tpl = array())
    {
        //Incorporiamo l'oggetto, così conferiamo le funzionalità della classe stessa
        $tplFunc = $this;

        ob_start();
        $fileToInclude = $this->cleanDoubleSlash($this->working_directory . DIRECTORY_SEPARATOR . $nomeTemplate);
        include $fileToInclude;
        return ob_get_clean();

    }

    /**
     * Inserisce un HTML in base a un URL
     *
     * @param $url
     * @return mixed|string
     */
    public function includeHTML($url){

        //Abbiamo l'extension CURL attivata?
        if (in_array('curl', get_loaded_extensions())) {

            //CURL è abilitato nel nostro server, facciamone uso
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120); //Timeout da 2 minuti
            //Verrà letto dopo in caso il server risponda con un 200
            $datiOttenuti = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            //Salviamo risorse liberandone la connessione
            curl_close($curl);

            //Se qualcosa andrà storto CURL darà false
            if ($httpCode !== 200) {

                $datiOttenuti = '';
            }

        } else {

            $datiOttenuti = file_get_contents($url);

        }

        return $datiOttenuti;

    }

    /**
     * Pulisce gli slash ripetuti in una stringa
     *
     * @param $stringaDaPulire
     * @return mixed
     */
    private function cleanDoubleSlash($stringaDaPulire){

        //Prendiamo il percorso del file, eliminando gli slash DIRECTORY_SEPARATOR e '\' di troppo
        //evitando di toglierli nel caso di http://www.elle.it, invece per i sistemi windows
        //evitiamo di togliere gli inizi delle cartelle di rete \\host\percorso
        return preg_replace('/(?<!:)(\/{2,})|(?<!^)(\\{2,})/i', DIRECTORY_SEPARATOR , $stringaDaPulire);

    }

    /**
     * Definisce una working directory differente a quella di default
     * @param $newWorkingDirectory
     * @return this
     */
    public function changeWorkingDirectory($newWorkingDirectory)
    {
        $this->working_directory = $newWorkingDirectory;
        return $this;
    }

    /**
     * Definisce una directory usata per caricare gli assets per default
     * @param $newAssetsDirectory
     * @return this
     */
    public function changeAssetsDirectory($newAssetsDirectory)
    {
        $this->working_directory = $newAssetsDirectory;
        return $this;
    }

    //@todo: Implementare una soluzione semplice per gli assets


}