# CryptoChannel
Implementazione js+php* di un canale di comunicazione crittata attraverso
la metodologia chiave simmetrica (RSA) + asimmetrica (AES-256).

La libreria maschera i seguenti passaggi:
- generazione chiave pubblica/privata da parte del destinatario quando il mittente inizializza una sessione di comunicazione
- richiesta della chiave pubblica di sessione del destinatario da parte del mittente
- generazione della chiave simmetrica da parte del mittente
- trasmissione della chiave simmetrica del mittente al destinatario utilizzando la chiave pubblica
- utilizzo della chiave simmetrica per la comunicazione tra mittente e destinatario

La comunicazione può avvenire sia {{javascript <-> Php}} che {{Php <-> Php}}.

La classe ChannelServer si preoccupa di spacchettare (unpack) i messaggi che arrivano dal client (sia js che php)
e di impacchettare (pack) i messaggi di risposta.

La classe ChannelClient si preoccupa di inizializzare la comunicazione con un ChannelServer (richiedendo la chiave pubblica e
trasmettendo lo chiave simmetrica) in modo da fornire i dati in chiaro al client dopo che la trasmissione è stata effettuata in modo crittato.

# Demo
Il file di demo permette di poter testare sia la modalità brower2server che server2server.

Dando un valore maggiore di zero al parametro "Salti" il contenuto della texarea viene reindirizzato alla stessa pagina 
però come se fosse un webservice, utilizzando canali crittati con chiavi diverse per ogni "salto".
