# Minibar Widgets erstellen - Ausführliche Anleitung

Diese Anleitung erklärt detailliert, wie eigene Widgets für die REDAXO Minibar erstellt werden können.

## Inhaltsverzeichnis

1. [Grundlagen](#grundlagen)
2. [Einfache Widgets](#einfache-widgets)
3. [Lazy Widgets](#lazy-widgets)
4. [Praxis-Beispiele](#praxis-beispiele)
5. [API-Referenz](#api-referenz)

## Grundlagen

Die Minibar ist eine schwebende Toolbar im REDAXO Backend und Frontend, die mit eigenen Widgets erweitert werden kann. Es gibt zwei Arten von Widgets:

- **Einfache Widgets** (`rex_minibar_element`): Für schnelle, einfache Inhalte
- **Lazy Widgets** (`rex_minibar_lazy_element`): Für aufwändige Inhalte, die erst bei Bedarf geladen werden

## Einfache Widgets

### Minimales Widget

Das einfachste Widget besteht aus einer Klasse, die `rex_minibar_element` erweitert und die `render()`-Methode implementiert:

```php
<?php

use FriendsOfRedaxo\Minibar\Element\AbstractElement;

class MySimpleWidget extends AbstractElement
{
    public function render()
    {
        return '<div class="rex-minibar-item">
            <span class="rex-minibar-value">Hello World!</span>
        </div>';
    }
}
```

### Widget mit Icon

Icons machen Widgets visuell ansprechender. Die Minibar unterstützt Font Awesome Icons:

```php
<?php

use FriendsOfRedaxo\Minibar\Element\AbstractElement;

class MyIconWidget extends AbstractElement
{
    public function render()
    {
        return '<div class="rex-minibar-item">
            <span class="rex-minibar-icon">
                <i class="rex-minibar-icon--fa rex-minibar-icon--fa-star"></i>
            </span>
            <span class="rex-minibar-value">
                Favoriten
            </span>
        </div>';
    }
}
```

### Widget mit zusätzlichen Informationen

Widgets können bei Mouse-Hover zusätzliche Informationen anzeigen:

```php
<?php

use FriendsOfRedaxo\Minibar\Element\AbstractElement;

class MyInfoWidget extends AbstractElement
{
    public function render()
    {
        $siteStartArticle = rex_article::getSiteStartArticle();
        $articleCount = $siteStartArticle ? $siteStartArticle->getChildren(true)->count() : 0;
        
        return '
        <div class="rex-minibar-item">
            <span class="rex-minibar-icon">
                <i class="rex-minibar-icon--fa rex-minibar-icon--fa-file"></i>
            </span>
            <span class="rex-minibar-value">
                ' . $articleCount . ' Artikel
            </span>
        </div>
        <div class="rex-minibar-info">
            <div class="rex-minibar-info-header">Artikel-Statistik</div>
            <div class="rex-minibar-info-group">
                <div class="rex-minibar-info-piece">
                    <span class="title">Gesamt</span>
                    <span>' . $articleCount . ' Artikel</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title">Status</span>
                    <span>Aktiv</span>
                </div>
            </div>
        </div>';
    }
}
```

### Widget mit Links

Widgets können klickbare Links enthalten:

```php
<?php

use FriendsOfRedaxo\Minibar\Element\AbstractElement;

class MyLinkWidget extends AbstractElement
{
    public function render()
    {
        return '<div class="rex-minibar-item">
            <a href="' . rex_url::backendPage('structure') . '">
                <span class="rex-minibar-icon">
                    <i class="rex-minibar-icon--fa rex-minibar-icon--fa-sitemap"></i>
                </span>
                <span class="rex-minibar-value">
                    Struktur
                </span>
            </a>
        </div>';
    }
}
```

### Widget mit Position

Die Position eines Widgets (links oder rechts) kann festgelegt werden:

```php
<?php

use FriendsOfRedaxo\Minibar\Element\AbstractElement;

class MyPositionedWidget extends AbstractElement
{
    public function render()
    {
        return '<div class="rex-minibar-item">
            <span class="rex-minibar-value">Rechts positioniert</span>
        </div>';
    }
    
    public function getOrientation()
    {
        return self::RIGHT; // oder ::LEFT
    }
}
```

### Widget mit Status-Färbung

Widgets können farblich hervorgehoben werden:

```php
<?php

use FriendsOfRedaxo\Minibar\Element\AbstractElement;

class MyStatusWidget extends AbstractElement
{
    public function render()
    {
        return '<div class="rex-minibar-item">
            <span class="rex-minibar-value">Wichtiger Hinweis</span>
        </div>';
    }
    
    // Für rote Färbung (Gefahr/Fehler)
    public function isDanger()
    {
        return true;
    }
    
    // Oder für blaue Färbung (Primär/Info)
    public function isPrimary()
    {
        return false;
    }
    
    // Oder für gelbe Färbung (Warnung)
    public function isWarning()
    {
        return false;
    }
}
```

## Lazy Widgets

Lazy Widgets laden ihren vollständigen Inhalt erst, wenn der Benutzer mit der Maus über das Widget fährt. Dies ist ideal für aufwändige Operationen.

### Basis Lazy Widget

```php
<?php

use FriendsOfRedaxo\Minibar\Element\AbstractLazyElement;

class MyMLazyWidget extends AbstractLazyElement
{
    protected function renderFirstView()
    {
        // Schnelle, initiale Ansicht
        return '<div class="rex-minibar-item">
            <span class="rex-minibar-value">Klick für Details</span>
        </div>';
    }
    
    protected function renderComplete()
    {
        // Aufwändige Daten erst hier laden
        $data = $this->loadExpensiveData();
        
        return '<div class="rex-minibar-item">
            <span class="rex-minibar-value">' . count($data) . ' Einträge</span>
        </div>
        <div class="rex-minibar-info">
            <div class="rex-minibar-info-header">Details</div>
            <div class="rex-minibar-info-group">
                <div class="rex-minibar-info-piece">
                    <span>' . implode('<br>', $data) . '</span>
                </div>
            </div>
        </div>';
    }
    
    private function loadExpensiveData()
    {
        // Beispiel: Datenbankabfrage
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT name FROM rex_article LIMIT 5');
        
        $results = [];
        foreach ($sql as $row) {
            $results[] = rex_escape($row->getValue('name'));
        }
        return $results;
    }
}
```

## Praxis-Beispiele

### Beispiel 1: Benutzer-Info Widget

```php
<?php

use FriendsOfRedaxo\Minibar\Element\AbstractElement;

class MyUserWidget extends AbstractElement
{
    public function render()
    {
        $user = rex::getUser();
        if (!$user) {
            return '';
        }
        
        return '
        <div class="rex-minibar-item">
            <span class="rex-minibar-icon">
                <i class="rex-minibar-icon--fa rex-minibar-icon--fa-user"></i>
            </span>
            <span class="rex-minibar-value">
                ' . rex_escape($user->getValue('login')) . '
            </span>
        </div>
        <div class="rex-minibar-info">
            <div class="rex-minibar-info-header">Benutzer-Informationen</div>
            <div class="rex-minibar-info-group">
                <div class="rex-minibar-info-piece">
                    <span class="title">Name</span>
                    <span>' . rex_escape($user->getValue('name')) . '</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title">E-Mail</span>
                    <span>' . rex_escape($user->getValue('email')) . '</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title">Rolle</span>
                    <span>' . ($user->isAdmin() ? 'Administrator' : 'Benutzer') . '</span>
                </div>
            </div>
        </div>';
    }
    
    public function getOrientation()
    {
        return self::RIGHT;
    }
}
```

### Beispiel 2: Cache-Status Widget (Lazy)

```php
<?php

use FriendsOfRedaxo\Minibar\Element\AbstractLazyElement;

class MyCacheWidget extends AbstractLazyElement
{
    protected function renderFirstView()
    {
        return '<div class="rex-minibar-item">
            <span class="rex-minibar-icon">
                <i class="rex-minibar-icon--fa rex-minibar-icon--fa-hdd"></i>
            </span>
            <span class="rex-minibar-value">Cache</span>
        </div>';
    }
    
    protected function renderComplete()
    {
        $cacheDir = rex_path::addonCache('structure');
        $cacheFiles = glob($cacheDir . '*.cache') ?: [];
        $totalSize = 0;
        
        if ($cacheFiles) {
            foreach ($cacheFiles as $file) {
                $size = filesize($file);
                if ($size !== false) {
                    $totalSize += $size;
                }
            }
        }
        
        $sizeInMB = round($totalSize / 1024 / 1024, 2);
        
        return '
        <div class="rex-minibar-item">
            <span class="rex-minibar-icon">
                <i class="rex-minibar-icon--fa rex-minibar-icon--fa-hdd"></i>
            </span>
            <span class="rex-minibar-value">
                ' . count($cacheFiles) . ' Cache-Dateien
            </span>
        </div>
        <div class="rex-minibar-info">
            <div class="rex-minibar-info-header">Cache-Informationen</div>
            <div class="rex-minibar-info-group">
                <div class="rex-minibar-info-piece">
                    <span class="title">Dateien</span>
                    <span>' . count($cacheFiles) . '</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span class="title">Größe</span>
                    <span>' . $sizeInMB . ' MB</span>
                </div>
                <div class="rex-minibar-info-piece">
                    <span><a href="' . rex_url::backendPage('system/settings') . '">Cache leeren</a></span>
                </div>
            </div>
        </div>';
    }
    
    public function getOrientation()
    {
        return self::RIGHT;
    }
}
```

### Beispiel 3: Debug-Modus Warnung

```php
<?php

use FriendsOfRedaxo\Minibar\Element\AbstractElement;

class MyDebugWarning extends AbstractElement
{
    public function render()
    {
        // Nur anzeigen, wenn Debug-Modus aktiv ist
        if (!rex::isDebugMode()) {
            return '';
        }
        
        return '
        <div class="rex-minibar-item">
            <span class="rex-minibar-icon">
                <i class="rex-minibar-icon--fa rex-minibar-icon--fa-exclamation-triangle"></i>
            </span>
            <span class="rex-minibar-value">
                Debug-Modus aktiv
            </span>
        </div>
        <div class="rex-minibar-info">
            <div class="rex-minibar-info-header">Warnung</div>
            <div class="rex-minibar-info-group">
                <div class="rex-minibar-info-piece">
                    <span>Der Debug-Modus sollte auf Live-Systemen deaktiviert werden!</span>
                </div>
            </div>
        </div>';
    }
    
    public function isDanger()
    {
        return true; // Rote Färbung
    }
}
```

### Beispiel 4: Mehrsprachiges Widget

```php
<?php

use FriendsOfRedaxo\Minibar\Element\AbstractElement;

class MyMultilangWidget extends AbstractElement
{
    public function render()
    {
        $currentLang = rex_clang::getCurrent();
        $allLangs = rex_clang::getAll();
        
        $langList = '';
        foreach ($allLangs as $lang) {
            $langList .= '<div class="rex-minibar-info-piece">';
            $langList .= '<span>' . rex_escape($lang->getName()) . '</span>';
            $langList .= '</div>';
        }
        
        return '
        <div class="rex-minibar-item">
            <span class="rex-minibar-icon">
                <i class="rex-minibar-icon--fa rex-minibar-icon--fa-language"></i>
            </span>
            <span class="rex-minibar-value">
                ' . rex_escape($currentLang->getName()) . '
            </span>
        </div>
        <div class="rex-minibar-info">
            <div class="rex-minibar-info-header">' . rex_i18n::msg('clang_select') . '</div>
            <div class="rex-minibar-info-group">
                ' . $langList . '
            </div>
        </div>';
    }
}
```

## Widget registrieren

Widgets werden in der `boot.php` des eigenen Addons oder im Project-Addon registriert:

```php
<?php

use FriendsOfRedaxo\Minibar\Minibar;

// In: /redaxo/src/addons/mein_addon/boot.php

// Einfaches Widget registrieren
Minibar::getInstance()->addElement(new MySimpleWidget());

// Widget nur im Frontend anzeigen
if (rex::isFrontend()) {
    Minibar::getInstance()->addElement(new MyFrontendWidget());
}

// Widget nur im Backend anzeigen
if (rex::isBackend()) {
    Minibar::getInstance()->addElement(new MyBackendWidget());
}

// Widget nur für Administratoren
if (rex::getUser() && rex::getUser()->isAdmin()) {
    Minibar::getInstance()->addElement(new MyAdminWidget());
}

// Widget nur im Debug-Modus
if (rex::isDebugMode()) {
    Minibar::getInstance()->addElement(new MyDebugWidget());
}

// Mehrere Bedingungen kombinieren
if (rex::isFrontend() && rex::getUser() && rex::getUser()->isAdmin()) {
    Minibar::getInstance()->addElement(new MyConditionalWidget());
}
```

## API-Referenz

### FriendsOfRedaxo\Minibar\Element\AbstractElement

Basis-Klasse für einfache Widgets.

#### Methoden

**`render(): string`** (abstract, erforderlich)
- Gibt den HTML-Code des Widgets zurück
- Muss implementiert werden

**`getOrientation(): string`**
- Gibt die Position zurück: `self::LEFT` oder `self::RIGHT`
- Standard: `LEFT`

**`isDanger(): bool`**
- Gibt `true` zurück für rote Färbung (Gefahr/Fehler)
- Standard: `false`

**`isPrimary(): bool`**
- Gibt `true` zurück für blaue Färbung (Primär/Info)
- Standard: `false`

**`isWarning(): bool`**
- Gibt `true` zurück für gelbe Färbung (Warnung)
- Standard: `false`

### FriendsOfRedaxo\Minibar\Element\AbstractLazyElement

Erweiterte Klasse für Lazy-Loading Widgets.

#### Methoden

**`renderFirstView(): string`** (abstract, erforderlich)
- Gibt den initialen HTML-Code zurück (schnell)
- Wird beim Seitenladen ausgeführt

**`renderComplete(): string`** (abstract, erforderlich)
- Gibt den vollständigen HTML-Code zurück
- Wird erst bei Mouse-Hover ausgeführt

**`isFirstView(): bool`** (static)
- Prüft, ob gerade die erste Ansicht gerendert wird
- Intern verwendet

### FriendsOfRedaxo\Minibar\Minibar

Manager-Klasse für die Minibar.

#### Methoden

**`getInstance(): FriendsOfRedaxo\Minibar\Minibar`** (static)
- Gibt die Singleton-Instanz zurück

**`addElement(FriendsOfRedaxo\Minibar\Element\AbstractElement $element): void`**
- Registriert ein neues Widget

**`isActive(): bool`**
- Prüft, ob die Minibar aktiv ist

**`shouldRender(): bool`**
- Prüft, ob die Minibar gerendert werden soll

## HTML-Struktur und CSS-Klassen

### Basis-Struktur

```html
<div class="rex-minibar-item">
    <span class="rex-minibar-icon">
        <!-- Icon hier -->
    </span>
    <span class="rex-minibar-value">
        <!-- Text/Wert hier -->
    </span>
</div>
```

### Mit zusätzlichen Informationen

```html
<div class="rex-minibar-item">
    <span class="rex-minibar-value">Haupttext</span>
</div>
<div class="rex-minibar-info">
    <div class="rex-minibar-info-header">Überschrift</div>
    <div class="rex-minibar-info-group">
        <div class="rex-minibar-info-piece">
            <span class="title">Titel</span>
            <span>Inhalt</span>
        </div>
    </div>
</div>
```

### CSS-Klassen

| Klasse | Beschreibung |
|--------|--------------|
| `rex-minibar-item` | Haupt-Container für Widget |
| `rex-minibar-icon` | Container für Icons |
| `rex-minibar-value` | Container für Text/Werte |
| `rex-minibar-info` | Container für Zusatzinfos (bei Hover) |
| `rex-minibar-info-header` | Überschrift der Zusatzinfos |
| `rex-minibar-info-group` | Gruppe von Info-Elementen |
| `rex-minibar-info-piece` | Einzelnes Info-Element |
| `rex-minibar-icon--fa` | Font Awesome Icon Basis |
| `rex-minibar-icon--fa-{name}` | Spezifisches Font Awesome Icon |

## Best Practices

### Performance

1. **Lazy Loading nutzen**: Verwende `AbstractLazyElement` für:
   - Datenbankabfragen
   - API-Calls
   - Datei-Operationen
   - Komplexe Berechnungen

2. **Caching**: Speichere aufwändige Daten zwischen:
```php
private function getCachedData()
{
    $key = 'my_widget_data';
    $cache = new rex_cache_file([
        'cache_dir' => rex_path::cache('my_widget/'),
        'ttl' => 300, // 5 Minuten Cache
    ]);

    $data = $cache->get($key, function () {
        return $this->loadExpensiveData();
    });

    return $data;
}
```

### Sicherheit

1. **Benutzerrechte prüfen**:
```php
public function render()
{
    if (!rex::getUser() || !rex::getUser()->isAdmin()) {
        return ''; // Nichts anzeigen
    }
    // ...
}
```

2. **HTML escapen**:
```php
$output = rex_escape($userInput);
// oder
$output = htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
```

3. **SQL Injection vermeiden**:
```php
$sql = rex_sql::factory();
$sql->setQuery('SELECT * FROM table WHERE id = ?', [$id]); // Mit Platzhalter
```

### Internationalisierung

Verwende `rex_i18n::msg()` für alle Texte:

```php
// In lang/de_de.lang:
my_widget_title = Mein Widget
my_widget_info = Informationen

// Im PHP-Code:
$title = rex_i18n::msg('my_widget_title');
$info = rex_i18n::msg('my_widget_info');
```

### Fehlerbehandlung

```php
public function render()
{
    try {
        $data = $this->loadData();
        return $this->renderWithData($data);
    } catch (Exception $e) {
        // Fehler loggen
        rex_logger::logException($e);
        
        // Fallback anzeigen
        return '<div class="rex-minibar-item">
            <span class="rex-minibar-value">Fehler beim Laden</span>
        </div>';
    }
}
```

### Code-Organisation

Organisiere deine Widget-Klassen übersichtlich:

```
/redaxo/src/addons/mein_addon/
├── boot.php
├── lib/
│   └── Minibar/
│       ├── Base.php
│       ├── User_widget.php
│       ├── Cache_widget.php
│       └── Debug_widget.php
└── lang/
    ├── de_de.lang
    └── en_gb.lang
```

In `boot.php`:
```php
<?php


use FriendsOfRedaxo\Minibar\Minibar;

// Widgets registrieren
Minibar::getInstance()->addElement(new MyUserWidget());
Minibar::getInstance()->addElement(new MyCacheWidget());
```

## Weitere Ressourcen

- Beispiel-Widgets im Minibar-Addon: `/redaxo/src/addons/minibar/lib/Element/`
- REDAXO Dokumentation: https://redaxo.org/doku/
- Font Awesome Icons: https://fontawesome.com/icons

## Support

Bei Fragen oder Problemen:
- GitHub Issues: https://github.com/FriendsOfREDAXO/minibar/issues
- REDAXO Slack: https://redaxo.org/slack/
