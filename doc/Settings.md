# Configurazione dei server
Nel file [servers.json](settings/servers.json) sarà necessario inserire tutte le informazioni necessarie per la connessioni ai server remoti. Il file di configurazione deve avere la seguente struttura:
```JSON
{
	"Server 1": {
		"nome": "Server 1",
		"hostname": "127.0.0.1",
		"username": "username",
		"port": 22,
		"publicKey": "/home/username/.ssh/chiave.key",
		"authMode": "authWithCertificate"
	},
	"Server 2": {
		"nome": "Server 2",
		"hostname": "127.0.0.1",
		"username": "username",
		"password": "password",
		"port": 22,
		"publicKey": "/home/username/.ssh/chiave.key",
		"authMode": "authWithCertificate"
	},
	"Server 3": {
		"nome": "Server 3",
		"hostname": "127.0.0.1",
		"username": "username",
		"password": "password",
		"port": 22,
		"authMode": "authWithPassword"
	}
}
```

## Connessione
I parametri necessari per la connessione sono:
 + hostname
 + port

## Spiegazione campi per l'autenticazione
Di seguito una tabella che spiega parametro per parametro

| Nome | Tipo | Descrizione | Obbligatorio |
|:-:|:-:|:-:|:-:|
| nome | `string` | Nome della connessione. Sarà l'alias da utilizzare nei payload delle chiamate | `true` |
| [authMode](#authmode) | `string` | Tipo di autorizzazione | `true` |
| hostname | `string` | Hostname come IP o stringa. Es. 192.168.1.234 o batch.aws.dominio.com | `true` |
| username | `string` | Username del server remoto | `true` |
| password | `string` | Password usata per le connessioni che lo richiedono | `false` |
| port | `int` | Porta per la connessione ssh | `true` |
| publicKey | `string` | Path della chiave publica usata per le connessioni con certificato | `false` |

### authMode
Oltre ai campi necessari per la connessione
Valori possibili, significato e campi necessari:

| Valore | Significato | Campi necessari |
|:-:|:-:|:-:|
| authWithPassword | Classica autorizzazione con username e password | username<br>password |
| authWithCertificate | Autenticazione con chiave pubblica ed eventualmente anche password | username<br>publicKey<br>[password] |
