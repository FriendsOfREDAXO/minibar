# REDAXO Minibar

![Screenshot](https://github.com/FriendsOfREDAXO/minibar/blob/assets/minibar.png?raw=true)


Die Minibar erweitert das Backend und Frontend um eine schwebende Leiste, die mit verschiedenen Elementen bestückt ist. Dazu gehören u.a. Systeminformationen, Informationen zum aktuellen Artikel, Buttons und Links zur Bearbeitung im Backend. Sie kann  um weitere Elemente erweitert werden.

Einstellungen zur Minibar finden sich in den Systemeinstellungen.  

## Features

- nur für im Backend angemeldete Benutzer
- Elemente via api registrierbar
- Unterstützt Lazy-Elemente die erst bei Interaktion initalisiert werden
- via system/setting einstellbar ob generell aktiv/inaktiv
- via system/setting einstellbar ob in Frontend und/oder Backend aktiv
- via system/setting einstellbar ob in popups aktiv/inaktiv

## Voraussetzungen

Damit die Minibar vom Addon im Frontend eingebunden werden kann muss das HTML der Seite sowohl ein `</head>` als auch ein `</body>` enthalten. Die Minibar wird nur angezeigt, wenn man im Backend angemeldet ist.

Je nach Einstellung unter "System" wird die Minibar nur im Frontend und/oder Backend angezeigt.

## Element API - Eigene Widgets erstellen

Die Minibar kann mit eigenen Widgets/Elementen erweitert werden. Es gibt zwei Arten von Elementen: einfache Elemente und Lazy-Elemente.

Das Addon nutzt den PHP-Namespace `FriendsOfRedaxo\Minibar` (ab Version 3.0).  

**📖 Vollständige Anleitung:** Siehe [WIDGETS.md](WIDGETS.md) für eine ausführliche Dokumentation mit vielen Beispielen.

### Einfache Elemente

Einfache Elemente erweitern die Klasse `FriendsOfRedaxo\Minibar\Element\AbstractElement` und implementieren die Methode `render()`.

#### Minimales Beispiel

```php
<?php

use FriendsOfRedaxo\Minibar\Element\AbstractElement;

class MeinMinibarElement extends AbstractElement
{
    public function render()
    {
        return '<div class="rex-minibar-item">
            <span class="rex-minibar-value">Mein Element</span>
        </div>';
    }
}

// Element registrieren (z.B. in einer boot.php)
FriendsOfRedaxo\Minibar\Minibar::getInstance()->addElement(new MeinMinibarElement());
```

#### Vollständiges Beispiel mit allen Optionen

```php
<?php

use FriendsOfRedaxo\Minibar\Element\AbstractElement;

class MeinErweitertesElement extends AbstractElement
{
    public function render()
    {
        return '
        <div class="rex-minibar-item">
            <span class="rex-minibar-icon">
                <i class="rex-minibar-icon--fa rex-minibar-icon--fa-info-circle"></i>
            </span>
            <span class="rex-minibar-value">
                Info-Text
            </span>
        </div>
        <div class="rex-minibar-info">
            <div class="rex-minibar-info-header">Zusätzliche Informationen</div>
            <div class="rex-minibar-info-group">
                <div class="rex-minibar-info-piece">
                    <span class="title">Details</span>
                    <span>Hier steht der Inhalt</span>
                </div>
            </div>
        </div>';
    }

    // Optional: Position des Elements festlegen
    public function getOrientation()
    {
        return self::RIGHT; // oder ::LEFT
    }

    // Optional: Element als "danger" kennzeichnen (rote Färbung)
    public function isDanger()
    {
        return false;
    }

    // Optional: Element als "primary" kennzeichnen (blaue Färbung)
    public function isPrimary()
    {
        return true;
    }

    // Optional: Element als "warning" kennzeichnen (gelbe Färbung)
    public function isWarning()
    {
        return false;
    }
}

// Element registrieren (z.B. in einer boot.php)
FriendsOfRedaxo\Minibar\Minibar::getInstance()->addElement(new MeinErweitertesElement());
```

### Lazy-Elemente (für aufwändige Inhalte)

Wenn das Rendern eines Elements zeit- und/oder ressourcenaufwändig ist, kann die Klasse `rex_minibar_lazy_element` erweitert werden. Lazy-Elemente laden ihren vollständigen Inhalt erst bei Benutzerinteraktion (Mouse-Hover).

#### Beispiel für ein Lazy-Element

```php
<?php

use FriendsOfRedaxo\Minibar\Element\AbstractLazyElement;

class MeinLazyElement extends AbstractLazyElement
{
    /**
     * Initiale, schnelle Darstellung des Elements
     * Wird beim Laden der Seite angezeigt
     */
    protected function renderFirstView()
    {
        return '<div class="rex-minibar-item">
            <span class="rex-minibar-value">Lade Daten...</span>
        </div>';
    }

    /**
     * Vollständige Darstellung mit allen Daten
     * Wird erst bei Mouse-Hover geladen
     */
    protected function renderComplete()
    {
        // Hier können aufwändige Datenbankabfragen oder Berechnungen stattfinden
        $data = $this->getDatenAusDatenbank();
        
        return '
        <div class="rex-minibar-item">
            <span class="rex-minibar-icon">
                <i class="rex-minibar-icon--fa rex-minibar-icon--fa-database"></i>
            </span>
            <span class="rex-minibar-value">
                ' . count($data) . ' Einträge
            </span>
        </div>
        <div class="rex-minibar-info">
            <div class="rex-minibar-info-header">Datenbank-Informationen</div>
            <div class="rex-minibar-info-group">
                <div class="rex-minibar-info-piece">
                    <span>' . htmlspecialchars(implode(', ', $data)) . '</span>
                </div>
            </div>
        </div>';
    }

    private function getDatenAusDatenbank()
    {
        // Beispiel für eine aufwändige Datenbankabfrage
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT * FROM rex_article LIMIT 10');
        
        $results = [];
        foreach ($sql as $row) {
            $results[] = $row->getValue('name');
        }
        return $results;
    }

    public function getOrientation()
    {
        return self::LEFT;
    }
}

// Element registrieren (z.B. in einer boot.php)
FriendsOfRedaxo\Minibar\Minibar::getInstance()->addElement(new MeinLazyElement());
```

### Wo werden Elemente registriert?

Minibar-Elemente sollten in der `boot.php` Datei des eigenen Addons oder im Project-Addon registriert werden:

```php
<?php
// boot.php des eigenen Addons

use FriendsOfRedaxo\Minibar\Minibar;

// Element nur im Frontend anzeigen
if (rex::isFrontend()) {
    Minibar::getInstance()->addElement(new MeinFrontendElement());
}

// Element nur im Backend anzeigen
if (rex::isBackend()) {
    Minibar::getInstance()->addElement(new MeinBackendElement());
}

// Element überall anzeigen (Frontend und Backend)
Minibar::getInstance()->addElement(new MeinElement());

// Element nur bei bestimmten Bedingungen anzeigen
if (rex::isFrontend() && rex::isDebugMode()) {
    Minibar::getInstance()->addElement(new MeinDebugElement());
}
```

### HTML-Struktur und CSS-Klassen

Die Minibar verwendet spezifische CSS-Klassen für konsistentes Styling:

- `rex-minibar-item`: Haupt-Container für ein Element
- `rex-minibar-icon`: Container für Icons
- `rex-minibar-value`: Container für Text/Werte
- `rex-minibar-info`: Container für zusätzliche Informationen (wird bei Hover angezeigt)
- `rex-minibar-info-header`: Überschrift des Info-Bereichs
- `rex-minibar-info-group`: Gruppierung von Info-Elementen
- `rex-minibar-info-piece`: Einzelnes Info-Element mit Titel und Wert

#### Icons

Für Icons können Font Awesome Icons verwendet werden:
```html
<i class="rex-minibar-icon--fa rex-minibar-icon--fa-{icon-name}"></i>
```

Beispiele:
- `rex-minibar-icon--fa-info-circle`
- `rex-minibar-icon--fa-database`
- `rex-minibar-icon--fa-flag`
- `rex-minibar-icon--fa-heartbeat`

### Best Practices

1. **Performance**: Verwende Lazy-Elemente für aufwändige Operationen (Datenbankabfragen, API-Calls)
2. **Sicherheit**: Überprüfe Benutzerrechte, bevor sensible Informationen angezeigt werden
3. **HTML-Escaping**: Verwende `rex_escape()` oder `htmlspecialchars()` für Benutzereingaben
4. **Internationalisierung**: Nutze `rex_i18n::msg()` für mehrsprachige Texte
5. **Konditionale Anzeige**: Registriere Elemente nur, wenn sie benötigt werden (z.B. nur im Frontend oder nur für Admins)

### Beispiele aus dem Minibar-Addon

Im `lib/Element/` Verzeichnis finden sich weitere Beispiele:
- `Time.php`: Einfaches Element mit Zeitanzeige
- `System.php`: Komplexes Element mit Systeminformationen
- `Syslog.php`: Element mit bedingter Anzeige (nur für Admins)
- `Debug.php`: Element mit Warnung und Custom-Styling
- `StructureArticle.php`: Element mit Artikelinformationen im Frontend

## Entwicklung am Addon

Styles werden durch re-install des minibar Addons neu compiliert (Vorraussetzung: be_style Addon ist aktiv)

## Umstellung auf die moderne Namespace-Version

**Die letzte Version ohne PHP-Namespace ist *2.6.2*.** Alle nachfolgenden Versionen
haben die PHP-Klassen im Namespace `FriendsOfRedaxo\Minibar` angeordnet.

- Wer keine eigenen Minibar-Elemente im Einsatz hat, kann diesen Absatz ignorieren. 
- Wer Addons anderer Entwickler einsetzt, die Minibar-Elemente unter den alten Klassennamen benutzen,
  wende sich bitte mit einem Issue an den Entwickler des Addons.

### Vorgehensweise bei der Umstellung

Für einen begrenzten Übergangszeitraum stehen als Umstellungshilfe Klassen unter dem alten Namen zur Verfügung. Diese Klassen sind als `deprecated` markiert. Vorhandene eigene Minibar-Elemente sollten daher ohne Einschränkung weiter funktionieren.

> Es ist angeraten, zügig auf die moderne Namespace-Variante umzusteigen. Die Version 2.6.2 wird nicht weiterentwickelt.

Die alten Klassen werden in modernen Entwicklungsumgebungen bzw. Editoren i.d.R. erkannt und kenntlich gemacht, da die alten Klassen eine Kennzeichnung als `deprecated` haben. Auch statische Code-Analysen z.B. mit RexSTAN weisen auf die Deprecated-Klassen hin. 

I.d.R. sollte es ausreichen, die alten gegen die neuen Namespace-Klassennamen auszutauschen.

### Zuordnung der alten und neuen Klassennamen

| bis 2.6.2 | danach mit Namespace |
|---|---|
| `rex_minibar` | `FriendsOfRedaxo\Minibar\Minibar` |
| `rex_minibar_element_structure_article` | `FriendsOfRedaxo\Minibar\Element\StructureArticle` |
| `rex_minibar_element_debug` | `FriendsOfRedaxo\Minibar\Element\Debug` |
| `rex_minibar_element` | `FriendsOfRedaxo\Minibar\Element\AbstractElement` |
| `rex_minibar_lazy_element` | `FriendsOfRedaxo\Minibar\Element\AbstractLazyElement` |
| `rex_minibar_element_scheme` | `FriendsOfRedaxo\Minibar\Element\Scheme` |
| `rex_minibar_element_syslog` | `FriendsOfRedaxo\Minibar\Element\Syslog` |
| `rex_minibar_element_system` | `FriendsOfRedaxo\Minibar\Element\System` |
| `rex_minibar_element_time` | `FriendsOfRedaxo\Minibar\Element\Time` |
| `rex_minibar_element_url2_yform` | `FriendsOfRedaxo\Minibar\Element\Url2Yform` |
| `rex_api_minibar` | `FriendsOfRedaxo\Minibar\Api\LazyLoader` |
| `rex_system_setting_minibar_hide_empty_metainfos` | `FriendsOfRedaxo\Minibar\Settings\HideEmptyMetainfos` |
| `rex_system_setting_minibar` | `FriendsOfRedaxo\Minibar\Settings\Scope` |
| `rex_system_setting_minibar_inpopup` | `FriendsOfRedaxo\Minibar\Settings\MinibarInPopup` |


## 💌 Give back some love

[Consider supporting the project](https://github.com/sponsors/staabm), so we can make this tool even better even faster for everyone.
