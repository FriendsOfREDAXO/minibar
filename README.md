# Minibar

## Features

- nur für im Backend angemeldete BENUTZER
- Elemente via api registrierbar
- Unterstützt Lazy-Elemente die erst bei Interaktion mit dem ser initalisiert werden
- via system/setting einstellbar ob generell aktiv/inaktiv
- via system/setting einstellbar ob in Frontend und/oder Backend aktiv
- via system/setting einstellbar ob in popups aktiv/inaktiv

## Entwicklung am Addon

### Styles via CLI kompilieren

**In den Minibar AddOn Ordner wechseln**
```
$ cd redaxo/src/addons/minibar
```

**Styles kompilieren**
```
$ sass scss/styles.scss assets/styles.css -C
```

**CSS in den public assets Ordner kopieren**
```
$ cp -R assets/ ../../../../assets/addons/minibar/
```
