# Remote server manager
Questo tool php consente semplicemente di collegarsi in modo sequenziale ai vari server configurati, eseguire uno o più comandi e recuperare gli output

## Installazione
Step di installazione
 + Installazione PHP 8+
 + Installazione composer (guide online per il vostro S.O.)
 + Esegiure nella cartella del file `index.php` il comando: `composer install`
 + Configurare i server: vedi [documentazione](doc/Settings.md)
 + Avviare il progetto eseguendo `php -S localhost:8080`

## Utilizzo
Una volta completata l'installazione basterà lanciare il comando `php -S localhost:8080` e chiamare i vari endpoint nella collection postman. Qualora non vi sia possibile utilizzare la porta `8080` potete usare una di vostra scelta ma ricordatevi di cambiarla nelle regole della collection postman.
