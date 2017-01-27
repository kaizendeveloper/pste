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
    protected $workingDirectory;

    public function __construct() {

        //Alla costruzione stabiliamo la wd
        $this->workingDirectory = dirname(__FILE__);

    }

    /**
     * Legge un file PHP e tramite la cattura del buffering poi restituiamo soltanto l'output
     *
     * @param $nomeTemplate
     * @param array $tpl
     * @return string
     */
    public function caricaTemplate($nomeTemplate, $tpl = array()){
        $tplFunc = $this;
        ob_start();
            include $this->workingDirectory . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $nomeTemplate;
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
     * Definisce una working directory differente a quella di default
     * @param $newWorkingDirectory
     * @return this
     */
    public function changeWorkingDirectory($newWorkingDirectory)
    {
        $this->workingDirectory = $newWorkingDirectory;
        return $this;
    }

}