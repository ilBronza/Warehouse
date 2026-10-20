# Warehouse

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require ilbronza/warehouse
```

## Usage

### Strutture e stanze

Il menu Magazzino comprende le anagrafiche **Strutture**, **Stanze**,
**Categorie di locali** e **Tipologie di locali**.

Le categorie raggruppano le tipologie: entrambe sono condivise tra tutte le
strutture. Ogni stanza richiede una struttura e una tipologia; la categoria si
ricava dalla tipologia. Nome, codice/numero, piano, superficie in m² e note
descrivono la singola stanza.

Il campo `destination_id` è facoltativo e collega la stanza a una `Destination`
del pacchetto Clients. Più stanze possono condividere la stessa destinazione.
La relazione inversa `Destination::rooms()` è registrata da Warehouse, anche
per il modello Destination configurato dall'applicazione ospite.

```php
use IlBronza\Warehouse\Models\Structure\Structure;
use IlBronza\Warehouse\Models\Room\RoomCategory;

$category = RoomCategory::getProjectClassName()::create(['name' => 'Direzionale']);
$type = $category->roomTypes()->create(['name' => 'Ufficio']);
$structure = Structure::getProjectClassName()::create(['name' => 'Sede Milano']);
$room = $structure->rooms()->create([
    'name' => 'Ufficio 01',
    'room_type_id' => $type->getKey(),
    'destination_id' => null,
]);
```

Eseguire `php artisan migrate` nell'applicazione ospite, dopo le migrazioni di
Clients. Le nuove tabelle sono `warehouse__structures`,
`warehouse__room_categories`, `warehouse__room_types` e `warehouse__rooms`.
Classi, tabelle, controller e parametri CRUD sono personalizzabili nelle sezioni
`warehouse.models.structure`, `roomCategory`, `roomType` e `room`. I nuovi valori
predefiniti vengono integrati anche nelle configurazioni già pubblicate.

La cancellazione di strutture, categorie e tipologie con elementi attivi
collegati viene bloccata. La cancellazione fisica di una destination azzera
`destination_id` e conserva le stanze. La cancellazione logica della destination
mantiene il riferimento nel database e la relazione restituisce `null` finché
la destination non viene ripristinata.

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author@email.com instead of using the issue tracker.

## Credits

- [Author Name][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ilbronza/warehouse.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ilbronza/warehouse.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ilbronza/warehouse/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/ilbronza/warehouse
[link-downloads]: https://packagist.org/packages/ilbronza/warehouse
[link-travis]: https://travis-ci.org/ilbronza/warehouse
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/ilbronza
[link-contributors]: ../../contributors
