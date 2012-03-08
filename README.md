event library for gino CMS by Otto Srl, MIT license
===================================================================
Libreria per la gestione di eventi.
Tra le caratteristiche: categorie, calendario, selezione (2), ordinamento, aggancio alla newsletter, una interfaccia per esporre qualcosa di particolare sul layout (box html).
La documentazione dell'ultima versione disponibile la si trova qui:    
http://otto-torino.github.com/gino-event

CARATTERISTICHE
------------------------------
- titolo
- categoria (opzionale)
- data
- orario
- luogo
- durata
- informazioni
- descrizione
- descrizione breve
- immagine (ridimensionamento e creazione thumb automatizzati)
- file allegato
- geolocalizzazione (longitudine, latitudine)
- gestione di eventi privati

OPZIONI CONFIGURABILI
------------------------------
- titolo pagina elenco eventi
- titolo scheda evento
- titolo ricerca eventi
- abilita categorie
- abilita selezione degli eventi
- abilita la newsletter
- abilita ordinamento
- numero di eventi visualizzati per pagina
- numero di caratteri mostrati nella descrizione breve
- larghezza delle immagini a seguito di ridimensionamento e creazione thumb
- modalità di visualizzazione dell'evento completo (layer, nuova pagina)
- dimensioni layer (vedi opzione precedente)
- titolo pagina eventi random
- numero eventi random per pagina
- eventi selezionati A (titolo, numero eventi per pagina)
- eventi selezionati B (titolo, numero eventi per pagina)
- titolo pagina eventi personalizzati
- numero eventi personalizzati per pagina
- titolo pagina eventi archiviati
- predisposizione di una pagina personalizzata per gli eventi appartenenti a determinate categorie;
si possonono indicare fino a quattro categorie (ID della categoria, titolo pagina, numero eventi per pagina, paginazione degli eventi)

**Opzioni del calendario**:
- titolo pagina
- imposta il primo giorno della settimana a lunedì
- numero caratteri dei giorni
- visualizzazione completa
- posizione degli elementi nella visualizzazione completa (permutazioni tra lista eventi e calendario)

OUTPUT
------------------------------
- calendario
- blocco html (ad es. per home page)
- lista eventi futuri o in via di svolgimento
- lista eventi random
- lista eventi selezionati (2)
- lista eventi della settimana
- lista eventi archiviati
- ricerca eventi
- lista eventi per categoria (4)

INSTALLAZIONE
------------------------------
Per installare questa libreria seguire la seguente procedura:

- creare un pacchetto zip di nome "event_pkg.zip" con tutti i file eccetto il README
- loggarsi nell'area amministrativa e entrare nella sezione "moduli di sistema"
- seguire il link (+) "installa nuovo modulo" e caricare il pacchetto creato al primo punto
- creare nuove istanze del modulo nella sezione "moduli" dell'area amministrativa.
