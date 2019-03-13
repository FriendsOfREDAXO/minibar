**der aktuelle stand funktioniert noch nicht**


es wird hier versucht den stand aus https://github.com/redaxo/redaxo/commit/34cc319ecd28d24f509f067fc3d3e2830b63d4ea als addon zu fassen um dann die idee der minibar hier weiter auszuarbeiten


## Styles via CLI kompilieren

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
