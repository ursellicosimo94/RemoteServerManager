# Executer/multipleExecOnServers
Metodo che esegue una serie di comandi sui server nell'elenco del comando, l'ordine dei comandi corrisponde all'ordine in cui eseguirli.

| Descrizione | Info |
|:-:|:-:|
| Endpoint | http://localhost:8080/Executer/multipleExecOnServers |
| Metodo http | POST |
| Controller | Executer |
| Method | multipleExecOnServers |
| Funzionalità | Esegue più comandi, per ogni comando si possono specificare i server su cui eseguirlo |

## Spiegazione campi
Array dove ogni elemento contiene comando e server su cui eseguirlo.

### command
Stringa con il comando da eseguire
### servers
Array che contiene la lista dei nomi delle connessioni