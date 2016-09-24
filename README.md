# CryptoChannel
Implementazione php di un canale di comunicazione crittata attraverso
la metodologia chiave simmetrica/asimmetrica.

Il canale maschera tutti i dettagli necessari ai seguenti passaggi:
- richiesta della chiave pubblica del destinatario del messaggio
- generazione della chiave simmetrica da parte del mittente
- crittazione del messaggio tramite la chiave simmetrica
- crittazione della chiave simmetrica del mittente tramite la chiave pubblica del destinatario
- generazione del messaggio da inviare che incapsuli sia la chiave simmetrica crittata sia il messaggio crittato
- estrazione, da parte del destinatario, della chiave privata e del messaggio in chiaro
- invio della risposta crittata mediante la chiave simmetrica
- estrazione, da parte del mittente, del messaggio in chiaro
