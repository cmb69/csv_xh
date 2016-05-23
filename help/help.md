Csv_XH
======

Csv_XH offers a user interface for CSV files, so they can be viewed
and edited on arbitrary CMSimple_XH pages.

Installation
------------

### Configuration



Usage
-----

To embed a CSV viewer/editor on a CMSimple_XH page:

    {{{PLUGIN:csv('%filename%');}}}

`%filename%` is the name of a CSV file in the content/ folder of CMSimple_XH.

Example:

    {{{PLUGIN:csv('tasks.csv');}}}

- `type`
    - `hidden`
    - `textarea`
    - `date`
    - `checkbox`
    - `select`

License
-------

Credits
-------

Csv_XH uses [TableFilter](http://koalyptus.github.io/TableFilter/) by koalyptus.
Many thanks for releasing this library under MIT license.
