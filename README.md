# DaVi

DaVi or Data Viewer is a web application to view raw data from a database. In contrast to classic database administration, such as adminer or PHPMyAdmin, it's not all about tables, but about entities.

In order to use DaVi you must first create an entity. Then you specify which columns/properties this entity should have. With this information, DaVi can then read the data from the database. So just like in any database management software.

But DaVi can do even more. You can add different handlers to each column that can refine the data, create links to other data, or format data. For example, different date formats can be used in the database - such as Unixtime - which is then converted into a readable date using the appropriate handler. Another example are JSON data. These can be validated using the appropriate handler. 

So you don't need another external software to check the data. You have all in one app and you can develop for specific use cases new handlers.

## Configuration

Entities are configured with yaml files. In this example this user entity has four properties.
<pre>
properties:
  usr_id:
    preDefined: "Integer"
    column: "usr_id"
    label: "User ID"
  name:
    preDefined: "String"
    column: "name"
    label: "Name"
  active:
    preDefined: "Integer"
    column: "active"
    label: "Active"
    settings:
      options:
        0:
          label: 'inactive'
        1:
          label: 'active'
    handler:
      valueFormatterItemHandler: "OptionsValueFormatterItemHandler"
  inactivation_date:
    preDefined: "Date"
    column: "inactivation_date"
    label: "Inactivation date"
    handler:
      valueFormatterItemHandler: "DateTimeValueFormatterItemHandler"
</pre>
The active and inactivation date columns have each an item handler, which formats the raw data. In the first case this shows what the raw values 0 and 1 mean and in the second case the date is converted from the standard database format to an easier to read format.

Another example with two columns. One contains HTML and the other JSON. The JSON column must contain a valid JSON object, otherwise an error will occur.
<pre>
properties:
  description:
    preDefined: "HtmlItem"
    label: "Description"
    column: "description"
    length: 65535
    handler:
      preRenderingItemHandler: "HtmlPreRenderingItemHandler"
  parameter:
    preDefined: "JsonItem"
    label: "parameter"
    column: "parameter"
    length: 65535
    handler:
      preRenderingItemHandler: "JsonPreRenderingItemHandler"
      validatorItemHandler
        JsonValidatorItemHandler: 
          jsonType: "jsonObject" 
          jsonMandatory: true 
          logCode: "INT-2000"
</pre>


## Frontend
There is a frontend developed with React. You can find it here: https://github.com/muellerjoerggit/dataviewer_frontend

Here are two example pictures. The first image shows a list of all users found. And the second image shows a specific user.
![img.png](docs/frontend1.png)

![img.png](docs/frontend2.png)

## Getting started
The data viewer is aimed more at technically savvy users and therefore there is no login area. Access control can be done via HTTP authentication.

You have to configure your entity types. You can find some examples under src/DaViEntity/EntityTypes. 

Then clients also have to be created. If you for example have five customers, you have to create for every customer a client.

With the command <code>php bin/console make davi:generate:example-data:user</code> you can create some example data.

## Credits

DaVi builds on top of Symfony, Symfony skeleton and FrankenPHP. Symfony Docker and Symfony skeleton are available under the MIT License.

https://github.com/dunglas/symfony-docker
https://github.com/symfony/skeleton