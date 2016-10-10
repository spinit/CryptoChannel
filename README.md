# CryptoChannel
Implementazione php di un canale di una comunicazione crittata attraverso
la metodologia chiave simmetrica/asimmetrica.

Il canale maschera incorpora i seguenti passaggi:
- generazione chiave pubblica/privata da parte del destinatario
- generazione della chiave simmetrica da parte del mittente
- trasmissione della chiave simmetrica dal mittente al destinatario utilizzando la chiave pubblica del destinatario
- utilizzo della chiave simmetrica per la comunicazione tra mittente e destinatario

La comunicazione può avvenire sia javascript <-> Php che Php <-> Php.

La classe ChannelServer si preoccupa di spacchettare (unpack) i messaggi che arrivano dal client e di impacchettare (pack) i 
messaggi da risposta.

La classe ChannelClient si preoccupa di inizializzare la comunicazione con un ChannelServer in modo da recuperare i dati in chiaro
dopo che la trasmissione è stata crittata. 
