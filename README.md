# Minibar

Die Minibar erweitert das Backend und Frontend um eine schwebende Leiste, die mit verschiedenen Elementen bestückt ist. Dazu gehören u.a. Systeminformationen, Informationen zum aktuellen Artikel, Buttons und Links zur Bearbeitung im Backend. Sie kann  um weitere Elemente erweitert werden.

Einstellungen zur Minibar finden sich in den Systemeinstellungen.  

## Features

- nur für im Backend angemeldete Benutzer
- Elemente via api registrierbar
- Unterstützt Lazy-Elemente die erst bei Interaktion initalisiert werden
- via system/setting einstellbar ob generell aktiv/inaktiv
- via system/setting einstellbar ob in Frontend und/oder Backend aktiv
- via system/setting einstellbar ob in popups aktiv/inaktiv

## Element API

### Die Minibar mit einfachen Elementen erweitern

Einfachste Elemente erweitern die Klasse `rex_minibar_element` und implementieren die methode `render()`.
Im Anschluß muss das neu erzeugte Element dann in der Minibar registriert werden via `rex_minibar::getInstance()->addElement(new mein_neues_element());`.

### Die Minibar mit lazy Elementen erweitern

Wenn das rendern eines Elementes zeit und/oder resourcen-aufwändig ist, kann die Klasse `rex_minibar_lazy_element` erweitert werden. Dabei müssen dann die methoden `renderFirstView()` und `renderComplete()` implementiert werden.
- `renderFirstView()` erzeugt die initiale Darstellung des Elements in der Toolbar. Dies sollte einfach und schnell passieren.
- Sobald der user via mouse-hover mit dem Element interagiert wird dieses via `renderComplete()` gerendert. Hier können nun aufwändigere Methoden laufen.

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
